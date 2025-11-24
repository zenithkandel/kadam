<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../config/database.php';
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$database = new Database();
$db = $database->getConnection();

$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$authHeader) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No token provided."]);
    exit;
}

$jwt = str_replace('Bearer ', '', $authHeader);
$secret_key = "YOUR_SECRET_KEY"; // In production, use environment variable

$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : null;

if (!$task_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Task ID is required."]);
    exit;
}

try {
    $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    $user_id = $decoded->data->id;
    $role = $decoded->data->role;

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

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Access denied.", "error" => $e->getMessage()]);
}
?>