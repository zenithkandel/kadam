<?php
// api/profile/get.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';
include_once '../utils/auth_check.php';

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

$database = new Database();
$db = $database->getConnection();

try {
    // Fetch basic user info
    $query = "SELECT id, username, email, name, role, status, profile_image, phone, address, is_verified, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sendError("User not found.", 404);
    }

    // Fetch role-specific info
    $details = null;
    if ($role === 'student') {
        $query = "SELECT * FROM students WHERE user_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $details = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch Skills
        $skillsQuery = "SELECT s.name FROM skills s 
                        JOIN student_skills ss ON s.id = ss.skill_id 
                        WHERE ss.student_id = :id";
        $skillsStmt = $db->prepare($skillsQuery);
        $skillsStmt->bindParam(":id", $user_id);
        $skillsStmt->execute();
        $skills = $skillsStmt->fetchAll(PDO::FETCH_COLUMN);
        $details['skills'] = $skills;

    } elseif ($role === 'employer') {
        $query = "SELECT * FROM employers WHERE user_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $details = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Merge details into user object
    $user['details'] = $details;

    sendSuccess("Profile retrieved successfully.", $user);

} catch (Exception $e) {
    sendError("Error fetching profile: " . $e->getMessage());
}
