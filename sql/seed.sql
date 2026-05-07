-- QueueLess seed data
-- Passwords are for demo only. Change in production.

INSERT INTO users (name, email, role, password_hash, created_at) VALUES
('Admin', 'admin@example.com', 'ADMIN', '$2y$10$aKLnqT.4V7cjBrVFGxlGEO01jCLFUKhVmuPOAfCoG85laE7gFf60u', NOW()),
('Staff', 'staff@example.com', 'STAFF', '$2y$10$g9.Xexta5Sm7TIzet4tr2.8rq1sTmQKpQEkF24FmZcPlUsu0uZE1S', NOW()),
('User', 'user@example.com', 'USER', '$2y$10$ZXzmvOGtwNpeeuCt6oC5n.ayl33HIQh7DGCwJmyrtO9i7tJoWVHdu', NOW());

INSERT INTO services (name, location, open_time, close_time, avg_service_minutes, is_active, created_at) VALUES
('Dean\'s Office', 'Building A, Room 101', '09:00:00', '16:00:00', 7, 1, NOW()),
('Campus Clinic', 'Health Center', '10:00:00', '18:00:00', 10, 1, NOW()),
('Cafeteria Pickup', 'Canteen Entrance', '08:00:00', '20:00:00', 3, 1, NOW());

-- Demo credentials:
-- admin@example.com / admin12345
-- staff@example.com / staff12345
-- user@example.com  / user12345
