<?php
// Copy this file to config.php and edit values.
// Supports both local development and Railway deployment

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbPort = (int)(getenv('DB_PORT') ?: 3306);
$dbName = getenv('DB_NAME') ?: 'u862140414_QueueLess';
$dbUser = getenv('DB_USER') ?: 'u862140414_QueueLess';
$dbPass = getenv('DB_PASS') ?: 'QueueLess0101$$$';
$baseUrl = getenv('BASE_URL') ?: 'https://lightgreen-partridge-149091.hostingersite.com/public/';

return [
  'db' => [
    'host' => $dbHost,
    'port' => $dbPort,
    'name' => $dbName,
    'user' => $dbUser,
    'pass' => $dbPass,
    'charset' => 'utf8mb4',
  ],
  'app' => [
    'base_url' => $baseUrl,
    'session_name' => 'QUEU_LESS_SESSID',
  ],
];
