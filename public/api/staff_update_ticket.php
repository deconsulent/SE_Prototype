<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('STAFF');
require_post_csrf();

$ticket_id = (int)($_POST['ticket_id'] ?? 0);
$service_id = (int)($_POST['service_id'] ?? 0);
$status = (string)($_POST['status'] ?? '');

if ($ticket_id <= 0 || $status === '') {
  flash_set('warning', 'Invalid request.');
  redirect('../staff.php' . ($service_id>0 ? '?service_id=' . $service_id : ''));
}

if (!staff_update_ticket_status($ticket_id, $status)) {
  flash_set('error', 'Failed to update ticket.');
  redirect('../staff.php' . ($service_id>0 ? '?service_id=' . $service_id : ''));
}

flash_set('success', 'Ticket updated: ' . e($status));
redirect('../staff.php' . ($service_id>0 ? '?service_id=' . $service_id : ''));
