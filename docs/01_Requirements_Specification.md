# Requirements Specification (SRS) — QueueLess

## 1. Introduction

### 1.1 Purpose
This document specifies the **functional** and **non-functional** requirements for QueueLess, a virtual queue and service-desk management web application.

### 1.2 Scope
QueueLess targets environments with queues such as:
- University service desks (dean’s office, student services)
- Clinics or campus health centers
- Cafeteria pickup points

### 1.3 Definitions
- **Service:** A desk/counter/location that serves users (e.g., clinic registration)
- **Ticket:** A record representing a user’s place in a queue
- **ETA:** Estimated time until the user is called
- **Roles:** USER (customer), STAFF (operator), ADMIN (service manager)

### 1.4 References
- Course guidance states the idea needs a software description + SWOT + SIPOC, with sustainability and an architecture derived from them.

---

## 2. Overall Description

### 2.1 Product perspective
QueueLess is a web application with:
- Presentation layer (PHP pages + HTML/CSS)
- Application logic layer (PHP modules)
- Data layer (MySQL/MariaDB)

### 2.2 User classes
- **USER:** joins queues and monitors status
- **STAFF:** calls the next ticket and updates ticket outcomes
- **ADMIN:** configures services and checks basic analytics

### 2.3 Assumptions / constraints
- Must be implementable as a **native PHP stack** MVP
- Must work in a typical student environment (XAMPP/WAMP/LAMP)
- Privacy: collect only the minimum required user data (name/email)

---

## 3. Functional Requirements

### Authentication & roles
- **FR-01** Users can register with name/email/password.
- **FR-02** Users can log in and log out.
- **FR-03** System supports roles: USER, STAFF, ADMIN.

### Service management
- **FR-04** Admin can create a service (name, location, hours, avg service time).
- **FR-05** Admin can activate/deactivate a service.

### Queue management (USER)
- **FR-06** User can view active services.
- **FR-07** User can join a queue for a chosen service.
- **FR-08** User can view ticket status (WAITING/CALLED/etc.), people ahead, and ETA.
- **FR-09** User can have at most one active ticket per service per day.

### Queue operations (STAFF)
- **FR-10** Staff can view today’s queue for a selected service.
- **FR-11** Staff can call the next WAITING ticket.
- **FR-12** Staff can mark a ticket: SERVED / NO-SHOW / CANCELLED.

### Analytics (ADMIN/STAFF)
- **FR-13** The system provides basic daily analytics (waiting, served, avg wait time).

### Optional (next iteration)
- **FR-14** Appointment booking + priority check-in (planned).

---

## 4. Non-Functional Requirements

- **NFR-01 Security:** Password hashing, prepared statements (SQL injection protection), basic CSRF protection.
- **NFR-02 Usability:** Responsive design usable on phone and desktop.
- **NFR-03 Performance:** Ticket status refresh should complete within ~1 second on local network.
- **NFR-04 Reliability:** A manual fallback should be possible if the system is offline (paper tickets).
- **NFR-05 Maintainability:** Code separated into modules: auth, queue, services, analytics.

---

## 5. Acceptance Criteria (MVP)

- A USER can register → log in → join a queue → see live status updates.
- A STAFF can call next and mark tickets served/no-show.
- An ADMIN can create a service and activate/deactivate it.
- No raw passwords are stored in DB.
