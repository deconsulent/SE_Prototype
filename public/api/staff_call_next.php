<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('STAFF');
require_post_csrf();

$service_id = (int)($_POST['service_id'] ?? 0);
if ($service_id <= 0) {
  flash_set('warning', 'Invalid service.');
  redirect('../staff.php');
}

$next = staff_call_next($service_id);
if (!$next) {
  flash_set('warning', 'No WAITING tickets.');
  redirect('../staff.php?service_id=' . $service_id);
}

flash_set('success', 'Called ticket #' . (int)$next['ticket_no'] . ' (' . $next['user_name'] . ').');
redirect('../staff.php?service_id=' . $service_id);
