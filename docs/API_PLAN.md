# KADAM API Plan

This document outlines the API structure for the KADAM platform, following the design patterns of the SastoMahango project. The APIs will be built using PHP and will communicate via JSON.

## Base URL Structure
*   Public/Common: `/api/`
*   Student: `/api/student/`
*   Employer: `/api/employer/`
*   Admin: `/api/admin/`

---

## 1. Authentication & Common APIs (`/api/`)

### `signup.php`
**Function:** Registers a new user (Student or Employer).
**Method:** `POST`
**Input:**
```json
{
  "role": "student", // or "employer"
  "email": "user@example.com",
  "password": "securePassword123",
  "name": "John Doe",
  "phone": "9800000000",
  "address": "Kathmandu",
  "employer_type": "individual" // Optional, for employers
}
```
**Logic:**
1.  Validate input.
2.  Check if email already exists.
3.  Generate unique username (e.g., using randomuser.me logic or internal logic).
4.  Hash password.
5.  Insert into `users` table.
6.  Insert into `students` or `employers` table based on role.
7.  Start session.
**Output:**
```json
{
  "success": true,
  "message": "Account created successfully",
  "user_id": 1,
  "redirect": "verification.html"
}
```

### `login.php`
**Function:** Authenticates a user.
**Method:** `POST`
**Input:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
**Logic:**
1.  Check credentials in `users` table.
2.  Verify password.
3.  Set `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['status']`.
**Output:**
```json
{
  "success": true,
  "role": "student",
  "redirect": "dashboard/student/dashboard.html"
}
```

### `logout.php`
**Function:** Destroys the user session.
**Method:** `POST` / `GET`
**Output:** `{"success": true}`

### `check_session.php`
**Function:** Returns current session state. Used by frontend to protect routes.
**Method:** `GET`
**Output:**
```json
{
  "logged_in": true,
  "user_id": 1,
  "role": "student",
  "status": "active", // or "pending_verification"
  "name": "John Doe",
  "profile_image": "path/to/img.jpg"
}
```

### `upload_file.php`
**Function:** Generic file uploader for profile pics, verification docs, and task attachments.
**Method:** `POST` (Multipart Form Data)
**Input:** `file` (Binary), `type` (e.g., 'profile', 'document', 'task')
**Output:**
```json
{
  "success": true,
  "file_path": "uploads/documents/doc_123.pdf"
}
```

---

## 2. Student APIs (`/api/student/`)

### `get_profile.php`
**Function:** Fetches student profile details, skills, and stats.
**Method:** `GET`
**Output:** JSON object with user + student table data + skills list.

### `update_profile.php`
**Function:** Updates profile info (bio, skills, education).
**Method:** `POST`
**Input:** JSON with fields to update.

### `search_tasks.php`
**Function:** Search and filter tasks.
**Method:** `GET`
**Params:** `keyword`, `category`, `min_budget`, `difficulty`.
**Output:** Array of task objects.

### `apply_task.php`
**Function:** Apply for a specific task.
**Method:** `POST`
**Input:**
```json
{
  "task_id": 101,
  "message": "I can do this...",
  "bid_amount": 5000
}
```

### `get_my_tasks.php`
**Function:** Get tasks the student is working on or has applied to.
**Method:** `GET`
**Params:** `filter` ('active', 'completed', 'applications').

### `submit_work.php`
**Function:** Submit completed work for a task.
**Method:** `POST`
**Input:** `task_id`, `message`, `attachments` (array of paths).

---

## 3. Employer APIs (`/api/employer/`)

### `post_task.php`
**Function:** Create a new task.
**Method:** `POST`
**Input:**
```json
{
  "title": "Build Website",
  "description": "...",
  "category": "web",
  "budget": 10000,
  "deadline": "2025-12-01",
  "difficulty": "intermediate",
  "skills": ["html", "css"]
}
```

### `get_my_postings.php`
**Function:** Get all tasks posted by the employer.
**Method:** `GET`

### `get_applications.php`
**Function:** Get applications for a specific task.
**Method:** `GET`
**Params:** `task_id`

### `manage_application.php`
**Function:** Accept or Reject an application.
**Method:** `POST`
**Input:** `application_id`, `action` ('accept', 'reject').
**Logic:**
1.  If accepted, update `tasks.status` to 'in_progress'.
2.  Create `task_applications` record update.
3.  Trigger notification to student.

### `complete_task.php`
**Function:** Mark task as completed and release payment (simulation).
**Method:** `POST`
**Input:** `task_id`, `rating`, `review`.

---

## 4. Admin APIs (`/api/admin/`)

### `get_dashboard_stats.php`
**Function:** Returns counts for the admin dashboard (users, tasks, revenue).
**Method:** `GET`

### `get_verification_requests.php`
**Function:** List users with `status` = 'pending_verification' or pending documents.
**Method:** `GET`

### `verify_user.php`
**Function:** Approve or Reject user verification.
**Method:** `POST`
**Input:** `user_id`, `action` ('approve', 'reject'), `remarks`.

### `get_users.php`
**Function:** List all users with filters.
**Method:** `GET`

### `manage_user.php`
**Function:** Ban, Unban, or Edit user.
**Method:** `POST`

---

## 5. Verification APIs (`/api/verification/`)

### `submit_verification.php`
**Function:** Submit verification documents.
**Method:** `POST`
**Input:**
```json
{
  "documents": [
    {"type": "id_card", "path": "..."},
    {"type": "cv", "path": "..."}
  ]
}
```
**Logic:**
1.  Insert into `verification_documents`.
2.  Update `users.status` to 'pending_verification'.

---

## Database & Logic Notes
*   **Session Management:** All protected endpoints must start with `session_start()` and check `$_SESSION['user_id']`.
*   **Input Sanitization:** All inputs must be sanitized to prevent SQL injection (use Prepared Statements).
*   **JSON Handling:** Use `json_decode(file_get_contents('php://input'), true)` to read JSON bodies.
*   **Error Handling:** Return standard JSON error structure: `{"success": false, "message": "Error details"}`.
