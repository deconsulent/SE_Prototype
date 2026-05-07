-- QueueLess schema (MySQL / MariaDB)
-- Create database first:
--   CREATE DATABASE queueless CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--   USE queueless;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  role ENUM('USER','STAFF','ADMIN') NOT NULL DEFAULT 'USER',
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  location VARCHAR(220) NOT NULL,
  open_time TIME NOT NULL,
  close_time TIME NOT NULL,
  avg_service_minutes INT NOT NULL DEFAULT 5,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS queue_tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT NOT NULL,
  user_id INT NOT NULL,
  queue_date DATE NOT NULL,
  ticket_no INT NOT NULL,
  status ENUM('WAITING','CALLED','SERVED','CANCELLED','NOSHOW') NOT NULL DEFAULT 'WAITING',
  priority INT NOT NULL DEFAULT 0,
  joined_at DATETIME NOT NULL,
  called_at DATETIME NULL,
  served_at DATETIME NULL,
  eta_minutes_at_join INT NOT NULL DEFAULT 0,

  CONSTRAINT fk_qt_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  CONSTRAINT fk_qt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_service_date_status (service_id, queue_date, status),
  INDEX idx_service_date_ticket (service_id, queue_date, ticket_no)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_feedback_ticket FOREIGN KEY (ticket_id) REFERENCES queue_tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Optional appointments table (for a later iteration)
CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT NOT NULL,
  user_id INT NOT NULL,
  appointment_time DATETIME NOT NULL,
  status ENUM('BOOKED','CANCELLED','COMPLETED') NOT NULL DEFAULT 'BOOKED',
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_appt_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
  CONSTRAINT fk_appt_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_service_time (service_id, appointment_time)
) ENGINE=InnoDB;
