<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('STAFF');

header('Content-Type: application/json');

$service_id = (int)($_GET['service_id'] ?? 0);
if ($service_id <= 0) {
  echo json_encode(['ok' => false, 'error' => 'Missing service_id']);
  exit;
}

echo json_encode([
  'ok' => true,
  'signature' => queue_service_snapshot_signature($service_id),
]);
