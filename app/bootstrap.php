<?php
declare(strict_types=1);

date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config = require __DIR__ . '/../config/config.php';

session_name($config['app']['session_name'] ?? 'QUEU_LESS_SESSID');
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/services.php';
require_once __DIR__ . '/queue.php';
require_once __DIR__ . '/analytics.php';
