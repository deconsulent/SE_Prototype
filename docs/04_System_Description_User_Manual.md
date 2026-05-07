# System Description / User Manual — QueueLess

## 1. What QueueLess does
QueueLess is a virtual queue management system:
- Users join queues and track status + ETA
- Staff call next and record outcomes
- Admin configures services and checks basic daily stats

---

## 2. How to install (local)
1. Create DB `queueless`
2. Import `sql/schema.sql` and `sql/seed.sql`
3. Copy `config/config.example.php` to `config/config.php` and update DB settings
4. Open `public/index.php` in the browser

---

## 3. Using QueueLess

### USER flow
1. Register or use demo account.
2. Open **User Dashboard**
3. Pick a service and click **Join queue**
4. Open **Live Ticket Status** to see:
   - Ticket number
   - Status (WAITING / CALLED)
   - People ahead + ETA

### STAFF flow
1. Login as STAFF
2. Choose a service from the dropdown
3. Click **Call next** to call the next WAITING ticket
4. Use action buttons:
   - Served
   - No-show
   - Cancel

### ADMIN flow
1. Login as ADMIN
2. Create a new service (name/location/hours/avg time)
3. Activate/deactivate services
4. Open staff view for a service

---

## 4. Troubleshooting
- **Blank page / errors:** ensure PHP error display is enabled, and DB credentials are correct.
- **No services shown:** import seed data or create services as ADMIN.
- **Login not working:** confirm the seed SQL ran successfully.

---

## 5. Future improvements
- Real push notifications (email/SMS)
- WebSockets for realtime updates
- Appointments with check-in and priority
- Better ETA model (moving average per staff member, peak-time adjustment)
