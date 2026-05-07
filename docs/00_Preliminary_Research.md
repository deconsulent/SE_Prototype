# Preliminary Research — QueueLess

## Software description (4–5 sentences)

QueueLess lets users join a **virtual queue** for a service (e.g., dean’s office, clinic, cafeteria pickup) and see a live ETA.  
Users get notified when they are close to being served, so they can do other things instead of standing in line.  
Staff use a simple dashboard to call the next person, pause/resume service, and view demand peaks.  
The system stores wait-time stats and feedback to improve scheduling and staffing.

## SWOT analysis

**Strengths**
- Real-time value (less waiting)
- Clear metrics for staff/admin
- Simple workflow; works on web/mobile
- Lightweight MVP possible

**Weaknesses**
- ETA accuracy depends on good data
- Requires staff adoption
- Network dependency (web app)

**Opportunities**
- Expand beyond campus (banks, municipal services)
- Add appointment booking
- Integrate with SMS/WhatsApp later

**Threats**
- Low engagement (“people still come early”)
- Privacy concerns (personal data)
- Competition from off-the-shelf queue apps

## Project sustainability (derived from SWOT)

- **Long-term value:** recurring daily use + measurable improvements (wait time KPIs)
- **Growth path:** start with 1–2 services (MVP), then scale across more locations
- **Risk controls:** privacy-by-design, minimal retention, and a manual fallback if the system is down

## SIPOC analysis (high level)

| Suppliers | Inputs | Process | Outputs | Customers |
|---|---|---|---|---|
| Service staff, campus IT/admin, users | Service hours, join requests, service events, device/browser session | Create service → user joins queue → compute ETA → notify user → staff serves next → record duration/feedback | Live queue status, ETA, notifications, analytics | Users, staff, admins |

## Modular diagram (from SIPOC)

- **User & Auth module** (roles: user/staff/admin)  
- **Queue Engine** (ticketing, ETA estimation, priority rules)  
- **Updates** (polling now; WebSockets later)  
- **Notification service** (in-app alerts now; email/SMS later)  
- **Staff dashboard** (call next, mark served/no-show)  
- **Analytics & reporting** (basic daily stats)  
- **Database** (MySQL/MariaDB; optional Redis later)  
- **Integration API** (future: SMS/WhatsApp, calendar/appointments)

## First tech/tool ideas (native PHP stack)

- **Frontend:** HTML + CSS + vanilla JS (polling for live updates)
- **Backend:** PHP 8.x (native PHP pages + small API endpoints)
- **Database:** MySQL or MariaDB
- **Dev tools:** GitHub + GitFlow + CI pipeline (GitHub Actions) + Docker (optional)

