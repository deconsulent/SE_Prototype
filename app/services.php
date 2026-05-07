<?php
declare(strict_types=1);

function services_all_active(): array {
  $stmt = db()->query('SELECT id, name, location, is_active, open_time, close_time, avg_service_minutes FROM services ORDER BY name');
  return $stmt->fetchAll();
}

function service_get(int $service_id): ?array {
  $stmt = db()->prepare('SELECT * FROM services WHERE id = ?');
  $stmt->execute([$service_id]);
  $s = $stmt->fetch();
  return $s ?: null;
}

function service_create(string $name, string $location, string $open_time, string $close_time, int $avg_service_minutes): int {
  $stmt = db()->prepare('INSERT INTO services (name, location, open_time, close_time, avg_service_minutes, is_active, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())');
  $stmt->execute([$name, $location, $open_time, $close_time, $avg_service_minutes]);
  return (int)db()->lastInsertId();
}

function service_set_active(int $service_id, bool $active): void {
  $stmt = db()->prepare('UPDATE services SET is_active = ? WHERE id = ?');
  $stmt->execute([$active ? 1 : 0, $service_id]);
}
