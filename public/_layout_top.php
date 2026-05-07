<?php
require_once __DIR__ . '/../app/bootstrap.php';
$u = auth_user();
$flash = flash_get();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>QueueLess</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="nav">
    <div style="font-weight:700;">QueueLess</div>
    <div style="flex:1;"></div>
    <?php if ($u): ?>
      <span class="badge"><?= e($u['role']) ?></span>
      <span><?= e($u['name']) ?></span>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
  <div class="container">
    <?php if ($flash): ?>
      <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
