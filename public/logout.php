<?php
require_once __DIR__ . '/../app/bootstrap.php';
auth_logout();
flash_set('success', 'Logged out.');
redirect('index.php');
