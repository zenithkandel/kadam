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

    if ($role !== 'employer') {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Only employers can accept applications."]);
        exit;
    }

    if (!empty($data->application_id)) {
        // Verify application belongs to a task posted by this employer
        $checkQuery = "SELECT ta.id, ta.task_id 
                       FROM task_applications ta 
                       JOIN tasks t ON ta.task_id = t.id 
                       WHERE ta.id = :application_id AND t.employer_id = :employer_id";
        
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(":application_id", $data->application_id);
        $checkStmt->bindParam(":employer_id", $user_id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() == 0) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Access denied or application not found."]);
            exit;
        }

        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $task_id = $row['task_id'];

        $db->beginTransaction();

        // Update application status
        $updateAppQuery = "UPDATE task_applications SET status = 'accepted' WHERE id = :application_id";
        $updateAppStmt = $db->prepare($updateAppQuery);
        $updateAppStmt->bindParam(":application_id", $data->application_id);
        $updateAppStmt->execute();

        // Update task status
        $updateTaskQuery = "UPDATE tasks SET status = 'in_progress' WHERE id = :task_id";
        $updateTaskStmt = $db->prepare($updateTaskQuery);
        $updateTaskStmt->bindParam(":task_id", $task_id);
        $updateTaskStmt->execute();

        $db->commit();

        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Application accepted. Task is now in progress."]);

    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Application ID is required."]);
    }

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Access denied.", "error" => $e->getMessage()]);
}
?>