-- KADAM Database Demo Data
-- Version 1.0

USE `kadam_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `name`, `role`, `status`, `profile_image`, `phone`, `address`, `is_verified`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'student1', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice Student', 'student', 'active', 'assets/images/avatars/alice.jpg', '1234567890', '123 College St, Cityville', 1, '2023-10-26 10:00:00', '2023-01-15 09:00:00', '2023-10-26 10:00:00'),
(2, 'student2', 'student2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob Learner', 'student', 'active', 'assets/images/avatars/bob.jpg', '0987654321', '456 University Ave, Townsville', 1, '2023-10-25 14:30:00', '2023-02-20 11:00:00', '2023-10-25 14:30:00'),
(3, 'employer1', 'employer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tech Corp Inc.', 'employer', 'active', 'assets/images/logos/techcorp.png', '1122334455', '789 Tech Park, Silicon Valley', 1, '2023-10-27 09:15:00', '2023-01-10 08:00:00', '2023-10-27 09:15:00'),
(4, 'employer2', 'employer2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Smallbiz', 'employer', 'active', 'assets/images/avatars/sarah.jpg', '5544332211', '321 Main St, Smalltown', 1, '2023-10-24 16:45:00', '2023-03-05 13:00:00', '2023-10-24 16:45:00'),
(5, 'admin1', 'admin@kadam.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'admin', 'active', NULL, NULL, NULL, 1, '2023-10-27 11:00:00', '2023-01-01 00:00:00', '2023-10-27 11:00:00'),
(6, 'student3', 'student3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Charlie Design', 'student', 'pending_verification', NULL, '1231231234', '789 Art Ln, Creativetown', 0, NULL, '2023-10-20 10:00:00', '2023-10-20 10:00:00'),
(7, 'employer3', 'employer3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Global Solutions', 'employer', 'active', 'assets/images/logos/global.png', '9879879876', '101 World Plaza, Metropolis', 1, '2023-10-26 15:00:00', '2023-04-12 09:30:00', '2023-10-26 15:00:00');

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`user_id`, `title`, `bio`, `education_level`, `institution`, `graduation_year`, `portfolio_url`, `resume_path`, `total_earned`, `tasks_completed_count`, `average_rating`) VALUES
(1, 'Computer Science Student', 'Passionate about coding and web development.', 'Undergraduate', 'City University', 2024, 'https://alice.portfolio.com', 'assets/docs/resumes/alice.pdf', 500.00, 5, 4.80),
(2, 'Marketing Major', 'Creative thinker with a knack for social media.', 'Undergraduate', 'State College', 2025, 'https://bob.marketing.com', 'assets/docs/resumes/bob.pdf', 250.00, 3, 4.50),
(6, 'Graphic Design Student', 'Visual storyteller and illustrator.', 'Undergraduate', 'Art Institute', 2026, 'https://charlie.design.com', NULL, 0.00, 0, 0.00);

--
-- Dumping data for table `employers`
--

INSERT INTO `employers` (`user_id`, `employer_type`, `industry`, `company_size`, `founded_year`, `website_url`, `description`, `total_spent`, `hires_count`, `average_rating`) VALUES
(3, 'company', 'Technology', '50-200', 2010, 'https://techcorp.com', 'Leading provider of software solutions.', 15000.00, 20, 4.90),
(4, 'individual', 'Retail', '1-10', 2018, 'https://sarahshop.com', 'Owner of a local boutique shop.', 1200.00, 5, 4.70),
(7, 'company', 'Consulting', '500+', 2005, 'https://globalsolutions.com', 'Global management consulting firm.', 50000.00, 50, 4.60);

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `permissions`) VALUES
(5, '{"all": true}');

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `name`, `slug`) VALUES
(1, 'Web Development', 'web-development'),
(2, 'Graphic Design', 'graphic-design'),
(3, 'Content Writing', 'content-writing'),
(4, 'Social Media Marketing', 'social-media-marketing'),
(5, 'Data Entry', 'data-entry'),
(6, 'Python', 'python'),
(7, 'JavaScript', 'javascript'),
(8, 'Photoshop', 'photoshop');

--
-- Dumping data for table `student_skills`
--

INSERT INTO `student_skills` (`student_id`, `skill_id`) VALUES
(1, 1),
(1, 6),
(1, 7),
(2, 3),
(2, 4),
(6, 2),
(6, 8);

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `employer_id`, `title`, `description`, `category`, `budget`, `deadline`, `difficulty_level`, `status`, `attachments`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 3, 'Build a Landing Page', 'Need a responsive landing page for our new product launch.', 'web', 500.00, '2023-11-15', 'intermediate', 'open', NULL, 150, '2023-10-20 09:00:00', '2023-10-20 09:00:00'),
(2, 3, 'Fix Python Script Bugs', 'Debug an existing data processing script.', 'data', 200.00, '2023-10-30', 'expert', 'in_progress', NULL, 80, '2023-10-22 14:00:00', '2023-10-25 10:00:00'),
(3, 4, 'Design a Logo', 'Need a modern logo for my boutique shop.', 'design', 150.00, '2023-11-05', 'beginner', 'open', NULL, 200, '2023-10-23 11:00:00', '2023-10-23 11:00:00'),
(4, 4, 'Write Blog Posts', 'Write 5 blog posts about fashion trends.', 'content', 100.00, '2023-11-10', 'intermediate', 'open', NULL, 120, '2023-10-24 15:00:00', '2023-10-24 15:00:00'),
(5, 7, 'Market Research Data Entry', 'Enter data from survey forms into Excel.', 'data', 50.00, '2023-11-01', 'beginner', 'completed', NULL, 300, '2023-10-15 08:00:00', '2023-10-18 16:00:00');

--
-- Dumping data for table `task_skills`
--

INSERT INTO `task_skills` (`task_id`, `skill_id`) VALUES
(1, 1),
(1, 7),
(2, 6),
(3, 2),
(3, 8),
(4, 3),
(5, 5);

--
-- Dumping data for table `task_applications`
--

INSERT INTO `task_applications` (`id`, `task_id`, `student_id`, `message`, `bid_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'I have experience building responsive landing pages. Check my portfolio.', 500.00, 'pending', '2023-10-21 10:00:00', '2023-10-21 10:00:00'),
(2, 2, 1, 'I can fix those bugs quickly.', 200.00, 'accepted', '2023-10-23 09:00:00', '2023-10-25 10:00:00'),
(3, 3, 6, 'I love designing logos! Here is my idea...', 150.00, 'pending', '2023-10-24 12:00:00', '2023-10-24 12:00:00'),
(4, 5, 2, 'I am fast at typing.', 50.00, 'accepted', '2023-10-16 09:00:00', '2023-10-16 10:00:00');

--
-- Dumping data for table `task_submissions`
--

INSERT INTO `task_submissions` (`id`, `task_id`, `student_id`, `message`, `files`, `status`, `submitted_at`, `reviewed_at`) VALUES
(1, 5, 2, 'Here is the Excel file with all the data.', '{"file": "assets/submissions/data_entry.xlsx"}', 'approved', '2023-10-18 14:00:00', '2023-10-18 16:00:00');

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `task_id`, `payer_id`, `payee_id`, `amount`, `status`, `transaction_type`, `payment_method`, `created_at`) VALUES
(1, 5, 7, 2, 50.00, 'completed', 'payment', 'credit_card', '2023-10-18 16:05:00');

--
-- Dumping data for table `verification_documents`
--

INSERT INTO `verification_documents` (`id`, `user_id`, `document_type`, `file_path`, `status`, `rejection_reason`, `uploaded_at`, `verified_at`) VALUES
(1, 1, 'id_card', 'assets/docs/verification/alice_id.jpg', 'approved', NULL, '2023-01-15 09:05:00', '2023-01-16 10:00:00'),
(2, 6, 'id_card', 'assets/docs/verification/charlie_id.jpg', 'pending', NULL, '2023-10-20 10:05:00', NULL);

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `task_id`, `content`, `is_read`, `created_at`) VALUES
(1, 3, 1, 1, 'Hi Alice, can you share more examples of your work?', 0, '2023-10-21 11:00:00'),
(2, 1, 3, 1, 'Sure, please check this link...', 0, '2023-10-21 11:15:00');

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'application_status', 'Your application for "Fix Python Script Bugs" was accepted.', '/dashboard/student/tasks.html?id=2', 0, '2023-10-25 10:00:00'),
(2, 3, 'new_application', 'New application received for "Build a Landing Page".', '/dashboard/employer/tasks.html?id=1', 0, '2023-10-21 10:00:00');

COMMIT;
