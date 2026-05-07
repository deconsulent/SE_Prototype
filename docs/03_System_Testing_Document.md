# System Testing Document — QueueLess

## 1. Test approach
- **Manual functional testing** for MVP flows (register/login/join/call next).
- Basic negative testing (wrong password, invalid CSRF).
- Light performance checks (polling endpoint returns quickly).

## 2. Test environment
- PHP 8.x
- MySQL/MariaDB
- Localhost Apache (XAMPP/WAMP/LAMP)
- Chrome/Firefox

## 3. Test cases (MVP)

### Authentication
- **TC-01 Register new user**
  - Steps: open Register → submit name/email/password
  - Expected: account created, redirect to Login, success message

- **TC-02 Login with correct password**
  - Expected: redirect to dashboard based on role

- **TC-03 Login with wrong password**
  - Expected: error message, no session created

### Queue (USER)
- **TC-04 Join queue**
  - Steps: login as USER → join a service queue
  - Expected: ticket created, live status page opens

- **TC-05 Prevent duplicate join**
  - Steps: join same service again
  - Expected: system returns same active ticket (no new ticket created)

- **TC-06 Ticket status updates**
  - Steps: open live status page
  - Expected: status renders, polling works, ETA appears

### Staff operations
- **TC-07 Call next**
  - Steps: login as STAFF → select service → click Call next
  - Expected: first WAITING ticket becomes CALLED, flash shows which ticket

- **TC-08 Mark ticket served**
  - Expected: ticket status becomes SERVED, removed from active list

- **TC-09 Mark ticket no-show**
  - Expected: ticket status becomes NOSHOW, removed from active list

### Security / robustness
- **TC-10 CSRF protection**
  - Steps: submit POST without CSRF token
  - Expected: 403 / rejected request

- **TC-11 SQL injection attempt**
  - Steps: attempt SQL injection in login form
  - Expected: safe due to prepared statements

## 4. Optional tests (later)
- Appointment booking + priority logic
- WebSocket updates
- Load test with many users
