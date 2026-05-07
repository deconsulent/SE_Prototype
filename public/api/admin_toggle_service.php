<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('ADMIN');
require_post_csrf();

$service_id = (int)($_POST['service_id'] ?? 0);
$active = (int)($_POST['active'] ?? 0);

if ($service_id <= 0) {
  flash_set('warning', 'Invalid service.');
  redirect('../admin.php');
}

service_set_active($service_id, $active === 1);
flash_set('success', 'Service updated.');
redirect('../admin.php');
