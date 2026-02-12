# Consultancy Management System
## Project Features Overview

---

# 1. What Is This Project?

- **Consultancy Management System** – Laravel-based platform for education consultancies
- Manages the full student journey: inquiry → application → documents → visa → payment → departure
- **Multiple portals** for Admin, Staff, Students, Counselors, Teachers, Employees, and HR
- **Public** inquiry form and university listing

---

# 2. User Roles & Portals

| Role | Portal | Purpose |
|------|--------|---------|
| **Admin (1)** | Consultancy + Users + Universities | Full system control |
| **Editor (2)** | Consultancy | Manage students, applications, tasks (no user management) |
| **Counselor (8)** | Counselor Portal | View assigned students, applications, tasks, messages |
| **Student (4)** | Student Portal | Dashboard, documents, applications, payments, courses, messages |
| **Employee (5)** | Employee Portal | Check-in/out, attendance, profile |
| **Teacher (6)** | Teacher Portal | Courses, attendance, daily log, payments |
| **HR (7)** | HR Portal | Employees, attendance overview |

---

# 3. Public Features

- **Home** – Landing page
- **Public inquiry form** – Submit inquiry (name, email, message, etc.) → Thank-you page
- **Consultancy notified by email** when a new inquiry is submitted
- **Public university list** – Browse universities (no login)
- **Language switch** – EN/JP (or configured locales)

---

# 4. Consultancy Admin – Core Modules

- **Dashboard** – Overview stats (students, applications, visa, payments, tasks)
- **Students** – CRUD, filters (status, counselor), course enrollment, documents, applications, payments, communications
- **Inquiries** – List, view, convert inquiry → student
- **Applications** – Create/edit per student & university; status workflow (draft → submitted → under_review → interview_scheduled → accepted/rejected → enrolled)
- **Visa** – Visa applications linked to students & applications; status tracking
- **Documents** – Upload, verify, reject; document checklist; student uploads visible
- **Payments** – Create, edit, record payments; link to student/application
- **Tasks** – Create tasks for students; assign to staff; complete/follow-up
- **Communications** – Log communications (incoming/outgoing); follow-up dates
- **Counselors** – Manage counselors; assign to students
- **Courses** – Courses CRUD; assign teachers; student enrollment (direct or approve from portal request)
- **Employees** – Employee CRUD; check-in/check-out; attendance table with checkout update
- **Reports** – Students, Applications, Financial, Visa
- **Consultancy profile** – Company profile (name, email, logo, etc.)

---

# 5. Student Journey & Status

- **Student status** (one per student): inquiry → registered → applied → accepted → **document_collection** → visa_processing → visa_approved → departed (and variants)
- **Application status** (per application): draft → documents_preparing → submitted → under_review → interview_scheduled → accepted/rejected → enrolled
- **Document collection** – Step between “Accepted” and “Visa processing”; when application is set to “Documents preparing,” student moves to document_collection and a task is created
- **Emails** – Student notified on application create/update, COE applied, interview scheduled, task created, document verify/reject, payment, communications

---

# 6. Student Portal Features

- **Dashboard** – Progress steps (Applied → Accepted → Document collection → Visa processing → Visa approved → Departed), documents summary, applications, payments, tasks, messages
- **Profile** – View and update contact details
- **Documents** – Upload documents; see status (pending/verified/rejected)
- **Applications** – View application list and status
- **Payments** – View payment history and status
- **Tasks** – View assigned tasks; update status
- **Courses** – Request enrollment (pending_verification); after admin approval & payment, enrolled; withdraw or cancel request
- **Messages** – View communications; send message to consultancy (consultancy gets email)

---

# 7. Course Enrollment Flow

- **Student** – Requests enrollment from portal → status **pending_verification**
- **Admin** – Sees “Pending verification” on student’s Courses section; can **Approve** (only if student has at least one completed payment) or **Reject**
- **Direct enroll** – Admin can enroll a student directly (no verification step)
- **Student** – Can cancel a pending request; can withdraw from an enrolled course

---

# 8. Documents & Notifications

- **Document checklist** – Configurable required document types
- **Student uploads** → Consultancy notified by email
- **Verify / Reject / Remove** → Student and consultancy notified
- **Document status** – Pending, Verified, Rejected

---

# 9. Applications – Automation & Emails

- **Application created** → Email to student
- **Application updated** → Email to student
- **Interview** – When status is interview_scheduled or interview date/mode/notes are set → **Interview email** to student (date, mode, notes)
- **Documents preparing** → Student status set to document_collection; **task created** for student; emails to student and consultancy
- **Accepted/Enrolled** → Student status synced to accepted (if not already further along)
- **COE applied** → Email to student

---

# 10. Visa & Payments

- **Visa applications** – Create from students with status accepted/document_collection/visa_processing; link to application
- **Visa created/updated** → Email to student
- **Payments** – Types (e.g. application_fee, tuition_fee); status: pending, partial, completed
- **Payment recorded** (e.g. marked completed) → Email to student

---

# 11. Tasks & Communications

- **Tasks** – Title, description, type, priority, due date; assign to user; link to student/application
- **Task created** → Email to student, assignee, and consultancy
- **Task completed** – Mark complete; follow-up
- **Communications** – Log type, direction (incoming/outgoing), subject, content; optional follow-up date
- **Outgoing email to student** – Option to send email to student when logging communication
- **Student sends message from portal** → Consultancy notified by email

---

# 12. Employee & Teacher Attendance

- **Employee portal** – Check In / Check Out (today); attendance history by month
- **Consultancy** – Employee list; Check-in/Check-out for an employee; **Attendance table** with **Update checkout** (time input + Update) for rows that have check-in
- **Teacher portal** – Check In / Check Out for today; **Mark Attendance** form (date, status, check-in/out, hours); attendance history; daily log; payments view

---

# 13. Counselor & Editor Portals

- **Counselor** – Dashboard; list/view assigned students and applications; update application status; tasks (view/complete); messages (view/send)
- **Editor** – Dashboard; inquiries (view); applications (view); tasks (view/complete); no student/application create or delete

---

# 14. HR Portal

- **Dashboard** – Attendance overview, recent attendance
- **Employees** – List with attendance summary
- **Attendance** – View attendance by month/year across employees

---

# 15. Admin & Configuration

- **Users** – CRUD; assign role (Admin, Editor, Counselor, Student, Employee, Teacher, HR)
- **Universities** – CRUD (admin); public listing and show page
- **Consultancy profile** – Company info, contact email (used for notifications)
- **Theme** – Light/dark switch (admin)
- **Localization** – Language switcher (e.g. EN/JP)

---

# 16. Reports

- **Students** – Filter by status; list with key info
- **Applications** – Filter by status; application metrics
- **Financial** – Payments summary; by type/status
- **Visa** – Visa application status overview

---

# 17. Tech Stack (Summary)

- **Backend:** Laravel (PHP 8+)
- **Frontend:** Blade, Tailwind CSS, Alpine.js (where used)
- **Auth:** Laravel Breeze (login, register, email verification, password reset)
- **Roles:** Middleware by role (e.g. role:1,2 for consultancy)
- **Email:** Laravel Mail (Mailable classes for each notification type)

---

# 18. Summary

- **One platform** for consultancy operations, student self-service, and staff portals
- **End-to-end workflow:** Inquiry → Student → Applications → Documents → Visa → Payments
- **Automated emails** for key events (application, interview, documents, tasks, payments, communications)
- **Course enrollment** with verification and payment check
- **Attendance** for employees and teachers with check-in/check-out and table-based checkout update
- **Multiple roles** with dedicated dashboards and permissions

---

# Thank You

**Consultancy Management System** – Feature overview  
Questions?
