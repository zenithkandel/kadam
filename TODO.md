# KADAM Project Todo List

Based on `details.txt` and current project state.

## 1. Signup Page (`signup.html`)
- [ ] Add role selection: **Student** vs **Employer**.
- [ ] For Employer, add sub-selection: **Company** vs **Individual**.
- [ ] Show/Hide relevant fields based on selection (e.g., Company Name vs Full Name).

## 2. Account Verification (`verification.html`)
- [ ] Create `verification.html` page.
- [ ] **Student Verification Form**:
    - [ ] Email & Phone verification inputs.
    - [ ] Profile picture upload.
    - [ ] Skills selection (max 15).
    - [ ] Evidence upload.
    - [ ] CV upload.
    - [ ] Age & ID card upload.
    - [ ] Guardian approval (if < 18).
- [ ] **Employer Verification Form**:
    - [ ] **Company**: Company Name, Logo, Letterhead application, Executive/Technical phone.
    - [ ] **Individual**: ID card, Signed agreements.

## 3. Dashboard (`dashboard.html`)
- [ ] Create `dashboard.html`.
- [ ] **Student View**:
    - [ ] Profile summary (Name, Pic, Bio, Level).
    - [ ] Skills & Tags management.
    - [ ] Work demos/portfolio.
    - [ ] Active/Completed tasks.
- [ ] **Employer View**:
    - [ ] Profile summary.
    - [ ] Active Vacancies / Task offers.
    - [ ] Search Tags.

## 4. Task Management
- [ ] Review `request-task.html`.
- [ ] Ensure all categories from `details.txt` are included.
- [ ] Implement logic for "Direct Request" vs "Post to Profile".
- [ ] Create `task-upload.html` if `request-task.html` is only for direct requests (or merge functionality).

## 5. Job Search / Explore (`job-search.html`)
- [ ] Create `job-search.html`.
- [ ] Search filters (Category, Price, Time).
- [ ] Job listing cards.
- [ ] Recommendation feed UI.

## 6. Admin Panel (`admin.html`)
- [ ] Create `admin.html`.
- [ ] Dashboard stats (Total users, tasks, revenue, etc.).
- [ ] Verification management UI.
- [ ] User & Task management UI.

## 7. General / Polish
- [ ] Ensure consistent navigation across all new pages.
- [ ] Check mobile responsiveness.
- [ ] Verify "Dark Mode" works on all new pages.
