<?php
// api/config/setup_db.php

include_once 'database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // 1. Users Table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        role ENUM('student', 'employer', 'admin') NOT NULL,
        status ENUM('active', 'banned', 'pending_verification') DEFAULT 'active',
        profile_image VARCHAR(255),
        phone VARCHAR(20),
        address TEXT,
        is_verified TINYINT(1) DEFAULT 0,
        last_login DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($sql);
    echo "Table 'users' created or already exists.<br>";

    // 2. Students Table
    $sql = "CREATE TABLE IF NOT EXISTS students (
        user_id INT PRIMARY KEY,
        title VARCHAR(100),
        bio TEXT,
        education_level VARCHAR(50),
        institution VARCHAR(100),
        graduation_year YEAR,
        portfolio_url VARCHAR(255),
        resume_path VARCHAR(255),
        total_earned DECIMAL(10, 2) DEFAULT 0.00,
        tasks_completed_count INT DEFAULT 0,
        average_rating DECIMAL(3, 2) DEFAULT 0.00,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'students' created or already exists.<br>";

    // 3. Employers Table
    $sql = "CREATE TABLE IF NOT EXISTS employers (
        user_id INT PRIMARY KEY,
        employer_type ENUM('individual', 'company') DEFAULT 'individual',
        industry VARCHAR(100),
        company_size VARCHAR(50),
        founded_year YEAR,
        website_url VARCHAR(255),
        description TEXT,
        total_spent DECIMAL(10, 2) DEFAULT 0.00,
        hires_count INT DEFAULT 0,
        average_rating DECIMAL(3, 2) DEFAULT 0.00,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'employers' created or already exists.<br>";

    // 4. Admins Table
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        user_id INT PRIMARY KEY,
        permissions JSON,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'admins' created or already exists.<br>";

    // 5. Tasks Table
    $sql = "CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employer_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category ENUM('Web Development', 'Graphic Design', 'Content Writing', 'Digital Marketing', 'Data Entry', 'Other') NOT NULL,
        budget DECIMAL(10, 2) NOT NULL,
        deadline DATE NOT NULL,
        difficulty_level ENUM('beginner', 'intermediate', 'expert') NOT NULL,
        status ENUM('open', 'in_progress', 'pending_review', 'completed', 'cancelled', 'moderated') DEFAULT 'open',
        attachments JSON,
        views_count INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'tasks' created or already exists.<br>";

    // 6. Task Applications Table
    $sql = "CREATE TABLE IF NOT EXISTS task_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        task_id INT NOT NULL,
        student_id INT NOT NULL,
        message TEXT,
        bid_amount DECIMAL(10, 2),
        status ENUM('pending', 'accepted', 'rejected', 'withdrawn') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'task_applications' created or already exists.<br>";

    // 7. Task Submissions Table
    $sql = "CREATE TABLE IF NOT EXISTS task_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        task_id INT NOT NULL,
        student_id INT NOT NULL,
        message TEXT,
        files JSON,
        status ENUM('pending_review', 'approved', 'rejected') DEFAULT 'pending_review',
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        reviewed_at DATETIME,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'task_submissions' created or already exists.<br>";

    // 8. Skills Table
    $sql = "CREATE TABLE IF NOT EXISTS skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) UNIQUE NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL
    )";
    $db->exec($sql);
    echo "Table 'skills' created or already exists.<br>";

    // 9. Student Skills Pivot
    $sql = "CREATE TABLE IF NOT EXISTS student_skills (
        student_id INT NOT NULL,
        skill_id INT NOT NULL,
        PRIMARY KEY (student_id, skill_id),
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'student_skills' created or already exists.<br>";

    // 10. Task Skills Pivot
    $sql = "CREATE TABLE IF NOT EXISTS task_skills (
        task_id INT NOT NULL,
        skill_id INT NOT NULL,
        PRIMARY KEY (task_id, skill_id),
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'task_skills' created or already exists.<br>";

    // 11. Messages Table
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        task_id INT,
        content TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL
    )";
    $db->exec($sql);
    echo "Table 'messages' created or already exists.<br>";

    // 12. Notifications Table
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        type VARCHAR(50) NOT NULL,
        message VARCHAR(255) NOT NULL,
        link VARCHAR(255),
        is_read TINYINT(1) DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $db->exec($sql);
    echo "Table 'notifications' created or already exists.<br>";

    echo "Database setup completed successfully.";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>