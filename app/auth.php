<?php
declare(strict_types=1);

function auth_user(): ?array {
  return $_SESSION['user'] ?? null;
}

function auth_role(): ?string {
  $u = auth_user();
  return $u['role'] ?? null;
}

function require_login(): void {
  if (!auth_user()) {
    flash_set('warning', 'Please log in first.');
    redirect('login.php');
  }
}

function require_role(string $role): void {
  require_login();
  if (auth_role() !== $role) {
    http_response_code(403);
    exit('Forbidden');
  }
}

function require_any_role(array $roles): void {
  require_login();
  $r = auth_role();
  if (!$r || !in_array($r, $roles, true)) {
    http_response_code(403);
    exit('Forbidden');
  }
}

function auth_login(string $email, string $password): bool {
  $stmt = db()->prepare('SELECT id, name, email, role, password_hash FROM users WHERE email = ?');
  $stmt->execute([$email]);
  $user = $stmt->fetch();
  if (!$user) return false;
  if (!password_verify($password, $user['password_hash'])) return false;

  unset($user['password_hash']);
  $_SESSION['user'] = $user;
  return true;
}

function auth_logout(): void {
  unset($_SESSION['user']);
  session_regenerate_id(true);
}

function auth_register(string $name, string $email, string $password): array {
  if (strlen($password) < 8) {
    return ['ok' => false, 'error' => 'Password must be at least 8 characters.'];
  }
  $hash = password_hash($password, PASSWORD_DEFAULT);

  try {
    $stmt = db()->prepare('INSERT INTO users (name, email, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$name, $email, 'USER', $hash]);
    return ['ok' => true];
  } catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate')) {
      return ['ok' => false, 'error' => 'Email already registered.'];
    }
    return ['ok' => false, 'error' => 'Registration failed.'];
  }
}
