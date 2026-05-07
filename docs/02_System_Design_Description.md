# System Design Description (SDD) — QueueLess

## 1. Architecture overview

QueueLess uses a simple **3-tier architecture**:

1) **Presentation Layer**
- Native PHP pages (UI): `public/*.php`
- Minimal HTML/CSS and vanilla JS polling

2) **Application Layer**
- PHP modules in `app/`:
  - `auth.php` (login, roles)
  - `services.php` (services CRUD)
  - `queue.php` (ticketing, ETA logic)
  - `analytics.php` (daily summary)
  - `db.php` (PDO connection)

3) **Data Layer**
- MySQL/MariaDB tables: users, services, queue_tickets, feedback (appointments reserved for later)

---

## 2. Modules (from SIPOC → architecture)

- **User & Auth module**: sessions, role-based access.
- **Queue Engine**: create tickets, compute position + ETA, update ticket state.
- **Updates module**: JS polling endpoint for “live status”.
- **Staff dashboard module**: call next, mark served/no-show.
- **Analytics module**: compute daily counts + average wait time.
- **Integration API module**: small endpoints in `public/api/`.

---

## 3. Data model

### users
- `id, name, email, role, password_hash, created_at`

### services
- `id, name, location, open_time, close_time, avg_service_minutes, is_active, created_at`

### queue_tickets
- `id, service_id, user_id, queue_date, ticket_no, status, priority, joined_at, called_at, served_at, eta_minutes_at_join`

**Key constraints**
- Indexes by `service_id + queue_date + status` for fast retrieval.

---

## 4. Key workflows

### 4.1 Join queue (USER)
1. User selects service and submits join request.
2. System checks if user already has an active ticket for this service today.
3. System generates next `ticket_no` and inserts `queue_tickets`.
4. System returns user to live ticket page.

### 4.2 Call next (STAFF)
1. Staff clicks “Call next”.
2. System selects earliest WAITING ticket (highest priority first).
3. Ticket is updated to `CALLED` with `called_at`.

### 4.3 ETA estimation
- Compute: `people_ahead × avg_service_minutes`
- `avg_service_minutes` comes from the service configuration in MVP.

---

## 5. Security design (MVP)

- Password hashing with `password_hash()`
- Login checks via `password_verify()`
- Prepared statements with PDO
- Basic CSRF tokens for POST forms
- Output escaping with `htmlspecialchars()` helper

---

## 6. Deployment (student setup)

- PHP 8 + Apache (XAMPP/WAMP/LAMP)
- MySQL/MariaDB
- Copy `config/config.example.php → config.php`
- Import SQL schema
