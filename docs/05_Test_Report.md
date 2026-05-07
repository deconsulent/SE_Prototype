# TESTING REPORT

**Project:** QueueLess Web Application  
**Version:** 1.0  
**Date:** 2026-05-07  
**Test basis:** Current repository state and existing application flows

## 1. Purpose
The purpose of this report is to verify the core behavior of QueueLess, a native PHP queue management web application, and to document usability issues that affect the staff and student experience.

## 2. System Under Test
QueueLess is a virtual queue system with three roles:
- User: registers, logs in, joins queues, and tracks live ticket status
- Staff: views today’s queue, calls the next ticket, and updates ticket status
- Admin: creates and manages services

## 3. Test Environment
- PHP 8.x
- MySQL / MariaDB
- Apache localhost environment
- Chrome and Firefox browsers

## 4. Test Approach
The application was reviewed against the implemented PHP pages, queue logic, and supporting SQL schema. The checks below combine functional testing, negative testing, and a basic UI review.

## 5. Test Scenarios

| ID | Module | Test scenario | Expected result | Actual result | Status |
| --- | --- | --- | --- | --- | --- |
| TS-01 | Authorization | Register a new user | Account is created successfully | User account is created and can log in | Pass |
| TS-02 | Authorization | Log in with valid credentials | User is redirected to the correct dashboard | Redirection works by role | Pass |
| TS-03 | Authorization | Log in with invalid credentials | Login is rejected with an error message | Error is shown, session is not created | Pass |
| TS-04 | Queue join | Join an active service queue | Ticket is created and live status page opens | Ticket is created and redirected correctly | Pass |
| TS-05 | Queue join | Try to join the same service twice while active | System should prevent duplicate active tickets | Existing active ticket is reused | Pass |
| TS-06 | Queue status | Open the live ticket page | Status, people ahead, and ETA are shown | Page shows live queue state and polling | Pass |
| TS-07 | Queue status | Wait for status refresh | Ticket state updates automatically | Polling refresh works on the user ticket page | Pass |
| TS-08 | Queue lifecycle | Staff calls next ticket | First waiting ticket becomes called | Staff action updates the ticket correctly | Pass |
| TS-09 | Queue lifecycle | Mark a ticket as served | Ticket leaves the active queue | Status is updated to SERVED | Pass |
| TS-10 | Queue lifecycle | Mark a ticket as no-show | Ticket leaves the active queue | Status is updated to NOSHOW | Pass |
| TS-11 | Security | Submit POST request without CSRF token | Request is rejected | CSRF protection is enforced | Pass |
| TS-12 | Security | Attempt SQL injection in login form | Input is handled safely | Prepared statements prevent injection | Pass |
| TS-13 | Staff dashboard | Open the staff dashboard and wait for live updates | Queue should refresh without manual reload | Dashboard does not auto-refresh | Fail |
| TS-14 | Usability | Review dashboard visual hierarchy | Dashboard should feel structured and active | Dashboard feels sparse and basic | Fail |
| TS-15 | Queue reuse | User who has been served joins again later | User can obtain a new ticket after the old one is completed | Allowed by queue logic; should be verified in the UI | Pass by code design |

## 6. Detailed Findings

### 6.1 Staff dashboard is not real time
The staff dashboard is rendered server-side and does not refresh automatically. Unlike the user live-status page, it has no polling or push-based update path, so staff must manually reload the page to see new queue activity.

### 6.2 Dashboard presentation is too plain
The current staff screen contains the core controls, but it lacks density, hierarchy, and visual feedback. The result is functional, but it looks empty for a queue management product.

### 6.3 Queue rejoin behavior should be made explicit
The queue engine only blocks active waiting or called tickets. Once a ticket is served, the same student can join again later. The backend supports that behavior, but the UI should communicate it clearly so users do not assume they are blocked.

## 7. Recommendations
- Add automatic refresh to the staff dashboard using polling, Server-Sent Events, or WebSockets.
- Add summary cards for waiting, called, served, and average wait time.
- Add a recent activity panel so queue movement is visible at a glance.
- Increase spacing, add depth, and strengthen hierarchy so the dashboard feels less empty.
- Apply a "Material Soft" styling system with soft surfaces, rounded geometry, subtle shadows, and calm neutral colors.
- Add regression coverage for the served-user rejoin flow.

## 8. Conclusion
QueueLess successfully covers the main queue workflow, but the staff experience still needs work before it feels complete. The key functional gap is the absence of real-time staff updates, and the key visual gap is the sparse dashboard layout.

Overall result: **core functionality is present, but the report and dashboard both need refinement for a polished final presentation**.