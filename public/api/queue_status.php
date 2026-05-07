<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('USER');

header('Content-Type: application/json');

$ticket_id = (int)($_GET['ticket_id'] ?? 0);
if ($ticket_id <= 0) {
  echo json_encode(['ok' => false, 'error' => 'Missing ticket_id']);
  exit;
}

$ticket = queue_ticket_status($ticket_id, (int)auth_user()['id']);
if (!$ticket) {
  echo json_encode(['ok' => false, 'error' => 'Ticket not found']);
  exit;
}

echo json_encode(['ok' => true, 'ticket' => $ticket]);
