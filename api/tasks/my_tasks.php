<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../config/database.php';
include_once '../utils/auth_check.php';

$database = new Database();
$db = $database->getConnection();

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

if ($role == 'employer') {
    $query = "SELECT t.*, (SELECT COUNT(*) FROM task_applications WHERE task_id = t.id) as applicants_count 
              FROM tasks t 
              WHERE employer_id = :id 
              ORDER BY created_at DESC";
} elseif ($role == 'student') {
    $query = "SELECT t.*, ta.status as application_status, ta.bid_amount, ta.created_at as applied_at 
              FROM tasks t 
              JOIN task_applications ta ON t.id = ta.task_id 
              WHERE ta.student_id = :id
              ORDER BY ta.created_at DESC";
} else {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Invalid role."]);
        exit;
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $user_id);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $tasks]);
?>