<?php
declare(strict_types=1);

// SQLSTATE class for integrity constraint violations (includes duplicate-entry errors).
const SQLSTATE_INTEGRITY_CONSTRAINT_CLASS = '23000';

function queue_today(): string {
  return (new DateTimeImmutable('now'))->format('Y-m-d');
}

function queue_get_user_active_ticket(int $user_id, int $service_id): ?array {
  $stmt = db()->prepare('
    SELECT * FROM queue_tickets
    WHERE user_id = ? AND service_id = ? AND queue_date = CURDATE()
      AND status IN ("WAITING","CALLED")
    ORDER BY id DESC LIMIT 1
  ');
  $stmt->execute([$user_id, $service_id]);
  $t = $stmt->fetch();
  return $t ?: null;
}

function queue_join(int $user_id, int $service_id): array {
  // Prevent double-join for same service same day
  $existing = queue_get_user_active_ticket($user_id, $service_id);
  if ($existing) {
    return ['ok' => true, 'ticket_id' => (int)$existing['id'], 'already' => true];
  }

  $pdo = db();
  $pdo->beginTransaction();
  try {
    // Determine next ticket number for this service today
    $stmt = $pdo->prepare('SELECT COALESCE(MAX(ticket_no), 0) AS m FROM queue_tickets WHERE service_id = ? AND queue_date = CURDATE() FOR UPDATE');
    $stmt->execute([$service_id]);
    $max = (int)($stmt->fetch()['m'] ?? 0);
    $ticket_no = $max + 1;

    // Estimate wait time at join (rough)
    $avg = queue_avg_service_minutes($service_id);
    $position = queue_position_estimate($service_id, $ticket_no); // position in queue if inserted at end
    $eta = max(0, $position * $avg);

    $stmt = $pdo->prepare('
      INSERT INTO queue_tickets (service_id, user_id, queue_date, ticket_no, status, priority, joined_at, eta_minutes_at_join)
      VALUES (?, ?, CURDATE(), ?, "WAITING", 0, NOW(), ?)
    ');
    $stmt->execute([$service_id, $user_id, $ticket_no, $eta]);

    $ticket_id = (int)$pdo->lastInsertId();
    $pdo->commit();
    return ['ok' => true, 'ticket_id' => $ticket_id, 'already' => false];
  } catch (Throwable $e) {
    $pdo->rollBack();
    if ($e instanceof PDOException && (string)$e->getCode() === SQLSTATE_INTEGRITY_CONSTRAINT_CLASS) {
      return queue_reopen_latest_ticket($user_id, $service_id);
    }
    return ['ok' => false, 'error' => 'Failed to join queue.'];
  }
}

function queue_reopen_latest_ticket(int $user_id, int $service_id): array {
  $pdo = db();
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare('
      SELECT id, status
      FROM queue_tickets
      WHERE user_id = ? AND service_id = ? AND queue_date = CURDATE()
      ORDER BY id DESC
      LIMIT 1
      FOR UPDATE
    ');
    $stmt->execute([$user_id, $service_id]);
    $ticket = $stmt->fetch();
    if (!$ticket) {
      $pdo->commit();
      return ['ok' => false, 'error' => 'Failed to join queue.'];
    }

    if (in_array($ticket['status'], ['WAITING', 'CALLED'], true)) {
      $pdo->commit();
      return ['ok' => true, 'ticket_id' => (int)$ticket['id'], 'already' => true];
    }

    $stmt = $pdo->prepare('SELECT COALESCE(MAX(ticket_no), 0) AS m FROM queue_tickets WHERE service_id = ? AND queue_date = CURDATE() FOR UPDATE');
    $stmt->execute([$service_id]);
    $max = (int)($stmt->fetch()['m'] ?? 0);
    $ticket_no = $max + 1;

    $avg = queue_avg_service_minutes($service_id);
    $position = queue_position_estimate($service_id, $ticket_no);
    $eta = max(0, $position * $avg);

    $stmt = $pdo->prepare('
      UPDATE queue_tickets
      SET ticket_no = ?, status = "WAITING", priority = 0, joined_at = NOW(), called_at = NULL, served_at = NULL, eta_minutes_at_join = ?
      WHERE id = ?
    ');
    $stmt->execute([$ticket_no, $eta, (int)$ticket['id']]);

    $pdo->commit();
    return ['ok' => true, 'ticket_id' => (int)$ticket['id'], 'already' => false];
  } catch (Throwable $e) {
    $pdo->rollBack();
    return ['ok' => false, 'error' => 'Failed to join queue.'];
  }
}

function queue_avg_service_minutes(int $service_id): int {
  // Use configured average, fallback to 5
  $s = service_get($service_id);
  $avg = (int)($s['avg_service_minutes'] ?? 5);
  return max(1, $avg);
}

function queue_position_for_ticket(int $ticket_id): int {
  // position among WAITING tickets with smaller ticket_no (today)
  $stmt = db()->prepare('SELECT service_id, ticket_no, queue_date FROM queue_tickets WHERE id = ?');
  $stmt->execute([$ticket_id]);
  $t = $stmt->fetch();
  if (!$t) return 0;

  $stmt = db()->prepare('
    SELECT COUNT(*) AS c FROM queue_tickets
    WHERE service_id = ? AND queue_date = ? AND status = "WAITING" AND ticket_no < ?
  ');
  $stmt->execute([(int)$t['service_id'], $t['queue_date'], (int)$t['ticket_no']]);
  $c = (int)($stmt->fetch()['c'] ?? 0);
  return $c; // number of people ahead
}

function queue_position_estimate(int $service_id, int $new_ticket_no): int {
  // Assume all existing WAITING tickets are ahead
  $stmt = db()->prepare('
    SELECT COUNT(*) AS c FROM queue_tickets
    WHERE service_id = ? AND queue_date = CURDATE() AND status = "WAITING"
  ');
  $stmt->execute([$service_id]);
  return (int)($stmt->fetch()['c'] ?? 0);
}

function queue_ticket_status(int $ticket_id, int $user_id): ?array {
  // Allows only owner to query
  $stmt = db()->prepare('
    SELECT qt.*, s.name AS service_name, s.location, s.avg_service_minutes
    FROM queue_tickets qt
    JOIN services s ON s.id = qt.service_id
    WHERE qt.id = ? AND qt.user_id = ?
  ');
  $stmt->execute([$ticket_id, $user_id]);
  $t = $stmt->fetch();
  if (!$t) return null;

  $ahead = 0;
  if ($t['status'] === 'WAITING') {
    $ahead = queue_position_for_ticket($ticket_id);
  }
  $avg = max(1, (int)($t['avg_service_minutes'] ?? 5));
  $eta = ($t['status'] === 'WAITING') ? ($ahead * $avg) : 0;

  return [
    'ticket_id' => (int)$t['id'],
    'service_id' => (int)$t['service_id'],
    'service_name' => $t['service_name'],
    'location' => $t['location'],
    'ticket_no' => (int)$t['ticket_no'],
    'status' => $t['status'],
    'ahead' => $ahead,
    'eta_minutes' => $eta,
    'joined_at' => $t['joined_at'],
    'called_at' => $t['called_at'],
  ];
}

function staff_call_next(int $service_id): ?array {
  // Pop next WAITING ticket with highest priority then earliest ticket_no
  $pdo = db();
  $pdo->beginTransaction();
  try {
    $stmt = $pdo->prepare('
      SELECT id FROM queue_tickets
      WHERE service_id = ? AND queue_date = CURDATE() AND status = "WAITING"
      ORDER BY priority DESC, ticket_no ASC
      LIMIT 1
      FOR UPDATE
    ');
    $stmt->execute([$service_id]);
    $row = $stmt->fetch();
    if (!$row) {
      $pdo->commit();
      return null;
    }
    $ticket_id = (int)$row['id'];

    $stmt = $pdo->prepare('UPDATE queue_tickets SET status = "CALLED", called_at = NOW() WHERE id = ?');
    $stmt->execute([$ticket_id]);

    $pdo->commit();

    return queue_ticket_admin_view($ticket_id);
  } catch (Throwable $e) {
    $pdo->rollBack();
    return null;
  }
}

function staff_update_ticket_status(int $ticket_id, string $new_status): bool {
  $allowed = ['SERVED','NOSHOW','CANCELLED','WAITING'];
  if (!in_array($new_status, $allowed, true)) return false;

  $fields = 'status = ?';
  $params = [$new_status];

  if ($new_status === 'SERVED') {
    $fields .= ', served_at = NOW()';
  }
  $params[] = $ticket_id;

  $stmt = db()->prepare("UPDATE queue_tickets SET $fields WHERE id = ?");
  return $stmt->execute($params);
}

function queue_ticket_admin_view(int $ticket_id): ?array {
  $stmt = db()->prepare('
    SELECT qt.id, qt.ticket_no, qt.status, qt.joined_at, qt.called_at,
           u.name AS user_name, u.email AS user_email,
           s.name AS service_name, s.location
    FROM queue_tickets qt
    JOIN users u ON u.id = qt.user_id
    JOIN services s ON s.id = qt.service_id
    WHERE qt.id = ?
  ');
  $stmt->execute([$ticket_id]);
  $t = $stmt->fetch();
  return $t ?: null;
}

function queue_list_waiting(int $service_id): array {
  $stmt = db()->prepare('
    SELECT qt.id, qt.ticket_no, qt.status, qt.joined_at,
           u.name AS user_name
    FROM queue_tickets qt
    JOIN users u ON u.id = qt.user_id
    WHERE qt.service_id = ? AND qt.queue_date = CURDATE() AND qt.status IN ("WAITING","CALLED")
    ORDER BY qt.status DESC, qt.ticket_no ASC
  ');
  $stmt->execute([$service_id]);
  return $stmt->fetchAll();
}

function queue_service_snapshot_signature(int $service_id): string {
  $stmt = db()->prepare('
    SELECT
      SUM(status = "WAITING") AS waiting,
      SUM(status = "CALLED") AS called,
      SUM(status = "SERVED") AS served,
      SUM(status = "NOSHOW") AS noshow,
      SUM(status = "CANCELLED") AS cancelled,
      COUNT(*) AS total,
      COALESCE(MAX(id), 0) AS max_id
    FROM queue_tickets
    WHERE service_id = ? AND queue_date = CURDATE()
  ');
  $stmt->execute([$service_id]);
  $row = $stmt->fetch() ?: [];
  return implode(':', [
    (int)($row['waiting'] ?? 0),
    (int)($row['called'] ?? 0),
    (int)($row['served'] ?? 0),
    (int)($row['noshow'] ?? 0),
    (int)($row['cancelled'] ?? 0),
    (int)($row['total'] ?? 0),
    (int)($row['max_id'] ?? 0),
  ]);
}
