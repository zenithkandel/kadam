# KADAM Database Structure & Field Standardization

This document outlines the database schema and standardized field names for the KADAM platform. All development should adhere to these naming conventions to ensure consistency across the frontend and backend.

## Field Standardization Rules

*   **Primary Keys:** Always `id` (INT AUTO_INCREMENT).
*   **Foreign Keys:** `singular_table_name_id` (e.g., `user_id`, `task_id`).
*   **Timestamps:** `created_at`, `updated_at` (DATETIME).
*   **Booleans:** `is_something` (TINYINT 1 for true, 0 for false).
*   **Status Fields:** Use lowercase string enums (e.g., 'active', 'pending', 'banned').
*   **Currency:** DECIMAL(10, 2).

---

## Database Schema

### 1. Users & Authentication

#### `users`
Base table for all user types.
*   `id` (PK, INT)
*   `username` (VARCHAR, Unique)
*   `email` (VARCHAR, Unique)
*   `password_hash` (VARCHAR)
*   `role` (ENUM: 'student', 'employer', 'admin')
*   `status` (ENUM: 'active', 'banned', 'pending_verification')
*   `profile_image` (VARCHAR, URL/Path)
*   `phone` (VARCHAR)
*   `address` (TEXT)
*   `is_verified` (TINYINT, Default 0)
*   `last_login` (DATETIME)
*   `created_at` (DATETIME)
*   `updated_at` (DATETIME)

#### `students`
Extension table for student-specific data.
*   `user_id` (FK -> users.id, Unique)
*   `full_name` (VARCHAR)
*   `title` (VARCHAR) - e.g., "Computer Science Student"
*   `bio` (TEXT)
*   `education_level` (VARCHAR)
*   `institution` (VARCHAR)
*   `graduation_year` (YEAR)
*   `portfolio_url` (VARCHAR)
*   `resume_path` (VARCHAR)
*   `total_earned` (DECIMAL, Default 0.00)
*   `tasks_completed_count` (INT, Default 0)
*   `average_rating` (DECIMAL(3,2), Default 0.00)

#### `employers`
Extension table for employer-specific data.
*   `user_id` (FK -> users.id, Unique)
*   `company_name` (VARCHAR) - Or individual name if type is individual
*   `employer_type` (ENUM: 'individual', 'company')
*   `industry` (VARCHAR)
*   `company_size` (VARCHAR)
*   `founded_year` (YEAR)
*   `website_url` (VARCHAR)
*   `description` (TEXT)
*   `total_spent` (DECIMAL, Default 0.00)
*   `hires_count` (INT, Default 0)
*   `average_rating` (DECIMAL(3,2), Default 0.00)

#### `admins`
Extension table for admin-specific data.
*   `user_id` (FK -> users.id, Unique)
*   `full_name` (VARCHAR)
*   `permissions` (JSON) - e.g., ["manage_users", "manage_tasks"]

---

### 2. Tasks & Jobs

#### `tasks`
*   `id` (PK, INT)
*   `employer_id` (FK -> users.id)
*   `title` (VARCHAR)
*   `description` (TEXT)
*   `category` (ENUM: 'web', 'design', 'content', 'marketing', 'data', 'other')
*   `budget` (DECIMAL)
*   `deadline` (DATE)
*   `difficulty_level` (ENUM: 'beginner', 'intermediate', 'expert')
*   `status` (ENUM: 'open', 'in_progress', 'pending_review', 'completed', 'cancelled', 'moderated')
*   `attachments` (JSON) - Array of file paths
*   `views_count` (INT, Default 0)
*   `created_at` (DATETIME)
*   `updated_at` (DATETIME)

#### `task_applications`
*   `id` (PK, INT)
*   `task_id` (FK -> tasks.id)
*   `student_id` (FK -> users.id)
*   `message` (TEXT) - Cover letter
*   `bid_amount` (DECIMAL) - Optional, if bidding is allowed
*   `status` (ENUM: 'pending', 'accepted', 'rejected', 'withdrawn')
*   `created_at` (DATETIME)
*   `updated_at` (DATETIME)

#### `task_submissions`
*   `id` (PK, INT)
*   `task_id` (FK -> tasks.id)
*   `student_id` (FK -> users.id)
*   `message` (TEXT)
*   `files` (JSON) - Array of file paths
*   `status` (ENUM: 'pending_review', 'approved', 'rejected')
*   `submitted_at` (DATETIME)
*   `reviewed_at` (DATETIME)

---

### 3. Skills & Categories

#### `skills`
Master list of skills.
*   `id` (PK, INT)
*   `name` (VARCHAR, Unique)
*   `slug` (VARCHAR, Unique)

#### `student_skills`
Pivot table for student skills.
*   `student_id` (FK -> users.id)
*   `skill_id` (FK -> skills.id)

#### `task_skills`
Pivot table for required task skills.
*   `task_id` (FK -> tasks.id)
*   `skill_id` (FK -> skills.id)

---

### 4. Communication & Notifications

#### `messages`
*   `id` (PK, INT)
*   `sender_id` (FK -> users.id)
*   `receiver_id` (FK -> users.id)
*   `task_id` (FK -> tasks.id, Nullable) - Context for the message
*   `content` (TEXT)
*   `is_read` (TINYINT, Default 0)
*   `created_at` (DATETIME)

#### `notifications`
*   `id` (PK, INT)
*   `user_id` (FK -> users.id)
*   `type` (VARCHAR) - e.g., 'application_received', 'task_approved'
*   `message` (VARCHAR)
*   `link` (VARCHAR) - URL to redirect to
*   `is_read` (TINYINT, Default 0)
*   `created_at` (DATETIME)

---

### 5. Reports & Analytics

#### `abuse_reports`
For flagging inappropriate content or users.
*   `id` (PK, INT)
*   `reporter_id` (FK -> users.id)
*   `reported_entity_type` (ENUM: 'user', 'task', 'message')
*   `reported_entity_id` (INT)
*   `reason` (VARCHAR)
*   `description` (TEXT)
*   `status` (ENUM: 'pending', 'investigating', 'resolved', 'dismissed')
*   `created_at` (DATETIME)
*   `resolved_at` (DATETIME)

#### `analytics_reports`
Generated files for admin download.
*   `id` (PK, INT)
*   `name` (VARCHAR)
*   `type` (VARCHAR)
*   `file_path` (VARCHAR)
*   `generated_by` (FK -> users.id, Nullable) - Null if system generated
*   `created_at` (DATETIME)

---

### 6. Financials

#### `transactions`
*   `id` (PK, INT)
*   `task_id` (FK -> tasks.id)
*   `payer_id` (FK -> users.id)
*   `payee_id` (FK -> users.id)
*   `amount` (DECIMAL)
*   `status` (ENUM: 'pending', 'completed', 'failed', 'refunded')
*   `transaction_type` (ENUM: 'payment', 'refund', 'payout')
*   `payment_method` (VARCHAR)
*   `created_at` (DATETIME)

---

## Consistency Checklist

When implementing features, ensure:
1.  **Status Fields:** Always use the defined ENUM values.
2.  **User References:** Always link to `users.id`, not the extension tables (`students`/`employers`) unless specifically querying profile data.
3.  **Dates:** Store all dates in UTC and convert to local time on the frontend.
4.  **Money:** Always handle money as DECIMAL(10,2) to avoid floating point errors.
