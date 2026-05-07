<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('USER');
require_post_csrf();

$service_id = (int)($_POST['service_id'] ?? 0);
if ($service_id <= 0) {
  flash_set('warning', 'Invalid service.');
  redirect('../user.php');
}

$s = service_get($service_id);
if (!$s || !(int)$s['is_active']) {
  flash_set('error', 'Service is not available.');
  redirect('../user.php');
}

$res = queue_join((int)auth_user()['id'], $service_id);
if (!$res['ok']) {
  flash_set('error', $res['error'] ?? 'Failed to join.');
  redirect('../user.php');
}

$ticket_id = (int)$res['ticket_id'];
flash_set('success', ($res['already'] ? 'You are already in the queue.' : 'Joined queue successfully.'));
redirect('../ticket.php?ticket_id=' . $ticket_id);
