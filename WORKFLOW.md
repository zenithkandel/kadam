# KADAM Platform Workflow

This document outlines the user journey and workflow for the KADAM platform, covering the Student, Employer, and Admin roles.

## 1. Public Access & Authentication

### Landing Page (`index.html`)
- The entry point for all users.
- Provides information about the platform, categories, and how it works.
- **Actions**:
  - Navigate to "Log In" or "Sign Up".

### Sign Up (`signup.html`)
- Users create an account by selecting a role: **Student** or **Employer**.
- **Student Registration**:
  - Required fields: Name, Email, Phone, Username, Address, Password.
- **Employer Registration**:
  - Types: Individual or Company.
  - Required fields: Name, Email, Phone, Address, Password.
- **Outcome**: Successful registration redirects to the **Verification** page.

### Verification (`verification.html`)
- Users must verify their identity before accessing the dashboard.
- **Student Verification**:
  - Upload Profile Picture.
  - Add Skills & Upload Evidence (Certificates/Portfolios).
  - Upload CV/Resume.
  - Upload Government ID.
  - *Under 18*: Requires Guardian Approval Letter.
- **Employer Verification**:
  - *Individual*: Profile Picture, ID, Signed Agreements.
  - *Company*: Company Logo, Official Contact Info, Registration Documents.
- **Outcome**: Submission redirects to the respective **Dashboard**.

### Log In (`login.html`)
- Users access their accounts using Email and Password.
- **Redirection Logic**:
  - Emails containing "admin" &rarr; **Admin Dashboard**.
  - Emails containing "employer" or "hr" &rarr; **Employer Dashboard**.
  - All other emails &rarr; **Student Dashboard**.

---

## 2. Student Workflow

**Entry Point**: `dashboard/student/dashboard.html`

The student dashboard uses an iframe-based layout to load content dynamically without refreshing the page.

### Core Features:
1.  **Overview (`overview.html`)**
    - View summary statistics (Earnings, Completed Tasks).
    - See recommended jobs and recent activity.

2.  **Explore Jobs (`explore.html`)**
    - Search and filter available jobs by category or price.
    - **Action**: Click "Apply Now" to open a modal.
    - **Apply Modal**: Submit Cover Letter, Attachments, Proposed Rate, and Estimated Time.

3.  **My Tasks (`my-tasks.html`)**
    - Track task status: Active, Completed, Applications.
    - **Action**: Click "Submit Work" on active tasks.
    - **Submit Modal**: Add work description, upload files, and provide external links.

4.  **Profile (`profile.html`)**
    - View public profile (Avatar, Stats, Portfolio, Skills).
    - **Action**: Click "Edit Profile" to update details via a modal.

5.  **Settings (`settings.html`)**
    - Manage account preferences, notifications, and security.

---

## 3. Employer Workflow

**Entry Point**: `dashboard/employer/dashboard.html`

### Core Features:
1.  **Overview (`overview.html`)**
    - View active postings, new applications, and pending reviews.
    - Quick action to "Post New Task".

2.  **Post a Task (`post-task.html`)**
    - Form to create a new job listing (Title, Description, Budget, Deadline, Skills).

3.  **My Postings (`my-postings.html`)**
    - Manage all job postings (Active, Drafts, Closed).
    - **Actions**:
        - **Edit Task**: Opens a modal to modify task details.
        - **View Applicants**: Opens a modal to see a list of candidates.
        - **Close/Delete Task**: Remove listings.

4.  **Applications (`applications.html`)**
    - Centralized view of all incoming applications.
    - **Actions**:
        - **Hire**: Opens a modal to confirm hiring and send an offer.
        - **Reject**: Opens a modal to reject an application with optional feedback.
        - **Shortlist/Message**: Manage candidate status.

5.  **Company Profile (`profile.html`)**
    - Manage company branding and information.

6.  **Settings (`settings.html`)**
    - Billing history, account management, and danger zone (delete account).

---

## 4. Admin Workflow

**Entry Point**: `dashboard/admin/dashboard.html`

### Core Features:
1.  **Overview (`overview.html`)**
    - High-level system statistics (Total Users, Active Tasks, Revenue).
    - Recent system activity logs.

2.  **User Management (`users.html`)**
    - List of all registered users (Students and Employers).
    - **Actions**:
        - **Edit User**: Update user details or role via modal.
        - **Ban User**: Suspend user access via modal.
        - **Verify**: Approve pending verification requests.

3.  **Task Monitoring (`tasks.html`)**
    - Oversee all tasks on the platform to ensure compliance.
    - Filter by status (Active, Completed, Reported).

4.  **Reports (`reports.html`)**
    - Generate and view system reports (Financial, User Growth, etc.).

5.  **System Settings (`settings.html`)**
    - Configure platform-wide settings (Fees, Maintenance Mode, etc.).
