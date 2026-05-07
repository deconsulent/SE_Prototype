<?php
declare(strict_types=1);

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): void {
  header('Location: ' . $path);
  exit;
}

function base_url(string $path = ''): string {
  $config = require __DIR__ . '/../config/config.php';
  $base = rtrim($config['app']['base_url'] ?? '', '/');
  $path = ltrim($path, '/');
  return $base . ($path ? '/' . $path : '');
}

function flash_set(string $type, string $message): void {
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
  if (!isset($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $f;
}

function csrf_token(): string {
  if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}

function csrf_validate(?string $token): bool {
  return isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function require_post_csrf(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
  }
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    http_response_code(403);
    exit('Invalid CSRF token');
  }
}
