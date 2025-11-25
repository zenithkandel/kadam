<?php
// api/auth/me.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';
include_once '../utils/auth_check.php';

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

$database = new Database();
$db = $database->getConnection();

// Fetch basic user info
$query = "SELECT id, username, email, name, role, status, profile_image, phone, address, is_verified, created_at FROM users WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $user_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch role specific info
    if ($role == 'student') {
        $query = "SELECT * FROM students WHERE user_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $student_details = $stmt->fetch(PDO::FETCH_ASSOC);
        $user['details'] = $student_details ? $student_details : null;
    } elseif ($role == 'employer') {
        $query = "SELECT * FROM employers WHERE user_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $employer_details = $stmt->fetch(PDO::FETCH_ASSOC);
        $user['details'] = $employer_details ? $employer_details : null;
    }

    sendSuccess("User profile retrieved.", $user);
} else {
    sendError("User not found.", 404);
}
