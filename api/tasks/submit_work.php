<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
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

$data = json_decode(file_get_contents("php://input"));

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

    if ($role !== 'student') {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Only students can submit work."]);
        exit;
    }

    if (!empty($data->task_id) && !empty($data->message)) {
        // Verify student is hired for this task
        $checkQuery = "SELECT id FROM task_applications 
                       WHERE task_id = :task_id AND student_id = :student_id AND status = 'accepted'";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(":task_id", $data->task_id);
        $checkStmt->bindParam(":student_id", $user_id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() == 0) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "You are not hired for this task."]);
            exit;
        }

        $query = "INSERT INTO task_submissions 
                (task_id, student_id, message, files, status, submitted_at) 
                VALUES 
                (:task_id, :student_id, :message, :files, 'pending_review', NOW())";

        $stmt = $db->prepare($query);

        $files = isset($data->files) ? json_encode($data->files) : null;

        $stmt->bindParam(":task_id", $data->task_id);
        $stmt->bindParam(":student_id", $user_id);
        $stmt->bindParam(":message", $data->message);
        $stmt->bindParam(":files", $files);

        if ($stmt->execute()) {
            // Update task status to pending_review
            $updateTask = "UPDATE tasks SET status = 'pending_review' WHERE id = :task_id";
            $updateStmt = $db->prepare($updateTask);
            $updateStmt->bindParam(":task_id", $data->task_id);
            $updateStmt->execute();

            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Work submitted successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "Unable to submit work."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Incomplete data."]);
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Access denied.", "error" => $e->getMessage()]);
}
?>