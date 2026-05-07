<?php
// Copy this file to config.php and edit values.
return [
  'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => 'queueless',
    'user' => 'root',
    'pass' => '',
    'charset' => 'utf8mb4',
  ],
  'app' => [
    'base_url' => 'http://localhost/queueless/public', // adjust for your setup
    'session_name' => 'QUEU_LESS_SESSID',
  ],
];
