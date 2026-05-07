<?php
declare(strict_types=1);

function analytics_service_summary(int $service_id): array {
  // Simple daily stats for prototype
  $stmt = db()->prepare('
    SELECT
      SUM(status="WAITING") AS waiting,
      SUM(status="CALLED") AS called,
      SUM(status="SERVED") AS served,
      SUM(status="NOSHOW") AS noshow
    FROM queue_tickets
    WHERE service_id = ? AND queue_date = CURDATE()
  ');
  $stmt->execute([$service_id]);
  $counts = $stmt->fetch() ?: [];

  $stmt = db()->prepare('
    SELECT AVG(TIMESTAMPDIFF(MINUTE, joined_at, served_at)) AS avg_total_wait
    FROM queue_tickets
    WHERE service_id = ? AND queue_date = CURDATE() AND status = "SERVED" AND served_at IS NOT NULL
  ');
  $stmt->execute([$service_id]);
  $avg = $stmt->fetch();

  return [
    'waiting' => (int)($counts['waiting'] ?? 0),
    'called' => (int)($counts['called'] ?? 0),
    'served' => (int)($counts['served'] ?? 0),
    'noshow' => (int)($counts['noshow'] ?? 0),
    'avg_total_wait_min' => (int)round((float)($avg['avg_total_wait'] ?? 0)),
  ];
}
