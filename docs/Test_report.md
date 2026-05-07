# QUEUING SYSTEM WEB APP
## TEST REPORT
Version 1.0
2026-05-07

---

## Contents
1. Introduction
   1.1. Definitions of the abbreviations
   1.2. Document purpose
   1.3. Related Documents
   1.4. General requirements for test execution
2. Functions that need testing
3. Test Scenarios
   3.1. User Authorization
   3.2. Dashboard & Queue Administration (Staff)
   3.3. Student Queue Execution
   3.4. UI & Material Soft Styling
   3.5. Reporting and Analytics

---

## 1. Introduction

### 1.1. Definitions of the abbreviations
| ABBREVIATION | EXPLANATION |
| :--- | :--- |
| **WS** | WebSockets (used for real-time data transfer) |
| **UI/UX** | User Interface / User Experience |
| **DBMS** | Database Management System |
| **Material Soft** | The specific design system utilizing soft elevations and pastels |

### 1.2. Document purpose
This document describes the study project queuing system web application (hereinafter "Queuing System") test report.
The document is intended for the parties involved in testing and maintenance of the software developed within the project:
* stakeholders of the customer, who are responsible for accepting and evaluating the project deliverables,
* technical specialists of the developer, who are responsible for its design and implementation.

### 1.3. Related Documents
This specification has been developed based on the following documents:
[1] EXERCISE STUDY DRAFT Subject "Web applications Creating"
[2] Software Requirements Specification (Queuing Logic)
[3] Software Design Description (Material Soft Architecture)

### 1.4. General requirements for test execution
In order to test the operation of the system user needs:
* Access to the system
* user name and password
* user rights to the Staff role and Student role
* Multiple browser sessions for real-time concurrency testing

---

## 2. Functions that need testing

| NO. | DESCRIPTION | PPS REQUIREMENT |
| :--- | :--- | :--- |
| 1 | User Authorization | QUEUE-1 |
| 2 | Dashboard & Queue Administration | QUEUE-2, QUEUE-3 |
| 3 | Student Queue Execution | QUEUE-4, QUEUE-5 |
| 4 | UI & Material Soft Styling | QUEUE-6 |
| 5 | Reporting | QUEUE-7 |

---

## 3. Test Scenarios

### 3.1. User Authorization
Open the authorization form.

**3.1.1. Checking if the required fields are filled:**
* Enter information in fields
* Press the "Login" button
**Expected result:** A message is displayed that no mandatory fields have been entered.
**Test result:** Passed

**3.1.2. Checking the user name and password**
* Enter the value “test” in the “Username” field
* enter the value “test” in the “Password” field
* Press the “Login” button
**Expected result:** A message is displayed that an incorrect user name or password has been entered.
**Test result:** Passed

**3.1.3. Successful user authorization**
* Enter the correct user name in the “Username” field
* Enter the correct password in the “Password” field
* Press the “Login” button
**Expected result:** The user is successfully authorized.
**Test result:** Passed

### 3.2. Dashboard & Queue Administration (Staff)
Authorize with a user who has the role "Staff". We open the staff dashboard form.

**3.2.1. Displaying the active queue list data**
* Open Dashboard
**Expected result:** A list of all currently queuing students is displayed.
**Test result:** Passed

**3.2.2. Real-time dashboard synchronization**
* Keep the Staff Dashboard open.
* In a separate session, a Student joins the queue.
**Expected result:** The staff dashboard updates instantly via WebSockets to show the new student without a page reload.
**Test result:** **Failed** (Dashboard does not refresh in real time).

**3.2.3. Marking a student as served**
* In the list, select a queued student and click on the "Mark Served" icon.
**Expected result:** The student is removed from the active queue and moved to the completed records.
**Test result:** Passed

### 3.3. Student Queue Execution
Authorize with a user who has the role "Student". We open the queue entry form.

**3.3.1. Entering the queue**
* Fill in the required request details.
* Press the "Join Queue" button.
**Expected result:** The student is added to the queue and sees their current position.
**Test result:** Passed

**3.3.2. Re-entering the queue after being served**
* Student is marked as "Served" by the staff.
* Student attempts to join the queue again by pressing "Join Queue".
**Expected result:** The student's previous session is cleared, and they successfully join the queue again.
**Test result:** **Failed** (Once a student gets served, they cannot go in the queue again).

### 3.4. UI & Material Soft Styling
Open the system as any role to evaluate visual guidelines.

**3.4.1. Evaluating Empty States**
* Empty the queue completely.
* View the staff dashboard.
**Expected result:** The dashboard displays an organic, friendly empty state graphic or text.
**Test result:** **Failed** (The whole dashboard feels very empty and basic).

**3.4.2. Evaluating Surface and Depth (Elevations)**
* Inspect the queue cards and buttons.
**Expected result:** Elements use the dual-shadow approach (light highlight shadow and soft ambient shadow) on an off-white background.
**Test result:** **Failed** (Dashboard lacks organic depth and modern geometric spacing).

### 3.5. Reporting and Analytics
Authorize with users who have the role of "Staff". Open the reporting form.

**3.5.1. Creating a daily queue report**
* Select the date and click on the export report button.
**Expected result:** A report on the queue performance results (wait times, served count) is downloaded.
**Test result:** Passed
