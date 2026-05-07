# QueueLess (Native PHP Web App Prototype)

QueueLess is a **virtual queue + service-desk dashboard** prototype. Users can join a queue, see a live ETA, and staff can call the next person.

This project is designed as a **practical, “native PHP stack” web app** (no heavy framework) for a Software Engineering course prototype.

---

## Features (MVP)

**User**
- Register / login
- Browse active services
- Join queue (one active ticket per service per day)
- Live ticket status (polling) with ETA + people-ahead estimate

**Staff**
- Select a service
- View today’s queue
- Call next ticket
- Mark tickets as Served / No-show / Cancelled
- Simple daily stats (served, avg wait)

**Admin**
- Create services (name, location, hours, avg service time)
- Activate / deactivate services

---

## Tech stack (native PHP)

- PHP 8.x (procedural / simple modular includes)
- MySQL / MariaDB
- HTML/CSS + vanilla JS polling (no WebSockets required for MVP)
- PDO prepared statements + password_hash/password_verify

---

## Local setup (XAMPP/WAMP/LAMP)

1. Create DB:
   ```sql
   CREATE DATABASE queueless CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import schema and seed:
   ```bash
   mysql -u root -p queueless < sql/schema.sql
   mysql -u root -p queueless < sql/seed.sql
   ```

3. Configure DB credentials:
   - Copy `config/config.example.php` → `config/config.php`
   - Edit host/user/pass/dbname.

4. Serve the app:
   - Put the project in your web root (e.g., `htdocs/queueless_native_php`)
   - Open `public/index.php` in the browser.
   - Example URL: `http://localhost/queueless_native_php/public/`

---

## Demo accounts

- admin@example.com / admin12345
- staff@example.com / staff12345
- user@example.com  / user12345

---

## Project structure

- `public/` — PHP pages (UI)
- `public/api/` — small JSON/POST endpoints
- `app/` — core logic (db/auth/queue/services/analytics)
- `sql/` — schema + seed
- `assets/` — CSS + JS
- `docs/` — requirements, design, tests, user manual

---

## Notes / limitations (prototype)

- “Realtime” is implemented via **polling** (every ~7–8 seconds). WebSockets can be added later.
- ETA estimation is simple: `ETA ≈ people_ahead × avg_service_minutes`.
- Appointment flow is included in DB schema for a later iteration, but not wired into the UI yet.

---

## License

Student prototype / educational use.
