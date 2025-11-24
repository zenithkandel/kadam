<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_check.php';

$database = new Database();
$db = $database->getConnection();

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : null;

if (!$task_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Task ID is required."]);
    exit;
}

if ($role !== 'employer') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Only employers can view applicants."]);
    exit;
}

    // Verify task belongs to employer
    $checkQuery = "SELECT id FROM tasks WHERE id = :task_id AND employer_id = :employer_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(":task_id", $task_id);
    $checkStmt->bindParam(":employer_id", $user_id);
    $checkStmt->execute();

    if ($checkStmt->rowCount() == 0) {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Access denied or task not found."]);
        exit;
    }

    // Fetch applicants
    $query = "SELECT ta.*, u.name, u.email, u.profile_image, s.education_level, s.institution 
              FROM task_applications ta 
              JOIN users u ON ta.student_id = u.id 
              LEFT JOIN students s ON u.id = s.user_id 
              WHERE ta.task_id = :task_id 
              ORDER BY ta.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":task_id", $task_id);
    $stmt->execute();
    $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $applicants]);
?>