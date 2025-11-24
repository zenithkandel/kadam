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

try {
    $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
    $user_id = $decoded->data->id;
    $role = $decoded->data->role;

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

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Access denied.", "error" => $e->getMessage()]);
}
?>