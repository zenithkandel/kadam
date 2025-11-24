<?php
// api/tasks/my_tasks.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';
include_once '../utils/jwt.php';

$token = JWT::getBearerToken();
if (!$token) sendError("Unauthorized.", 401);
$decoded = JWT::decode($token);
if (!$decoded) sendError("Invalid token.", 401);

$user_id = $decoded['data']['id'];
$role = $decoded['data']['role'];

$database = new Database();
$db = $database->getConnection();

if ($role == 'employer') {
    $query = "SELECT * FROM tasks WHERE employer_id = :id ORDER BY created_at DESC";
} elseif ($role == 'student') {
    // For students, maybe show tasks they applied to?
    // For now, let's just return empty or handle differently
    // But the file name implies "My Tasks" which usually means tasks I own/manage
    // For students, it might be "My Applications"
    $query = "SELECT t.*, ta.status as application_status 
              FROM tasks t 
              JOIN task_applications ta ON t.id = ta.task_id 
              WHERE ta.student_id = :id";
} else {
    sendError("Invalid role.");
}

$stmt = $db->prepare($query);
$stmt->bindParam(":id", $user_id);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendSuccess("Tasks retrieved.", $tasks);
?>