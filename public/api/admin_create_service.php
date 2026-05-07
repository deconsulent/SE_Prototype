<?php
require_once __DIR__ . '/../../app/bootstrap.php';
require_role('ADMIN');
require_post_csrf();

$name = trim($_POST['name'] ?? '');
$location = trim($_POST['location'] ?? '');
$open_time = trim($_POST['open_time'] ?? '');
$close_time = trim($_POST['close_time'] ?? '');
$avg = (int)($_POST['avg_service_minutes'] ?? 5);

if ($name === '' || $location === '' || $open_time === '' || $close_time === '') {
  flash_set('warning', 'All fields are required.');
  redirect('../admin.php');
}

$service_id = service_create($name, $location, $open_time, $close_time, $avg);
flash_set('success', 'Service created (ID ' . $service_id . ').');
redirect('../admin.php');
