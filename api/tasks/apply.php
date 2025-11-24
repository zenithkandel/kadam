<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: POST");
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

$data = json_decode(file_get_contents("php://input"));

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

if ($role !== 'student') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Only students can apply for tasks."]);
    exit;
}

if (
    !empty($data->task_id) &&
    !empty($data->message) &&
    !empty($data->bid_amount)
) {
    // Check if already applied
        $checkQuery = "SELECT id FROM task_applications WHERE task_id = :task_id AND student_id = :student_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(":task_id", $data->task_id);
        $checkStmt->bindParam(":student_id", $user_id);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(["success" => false, "message" => "You have already applied for this task."]);
            exit;
        }

        $query = "INSERT INTO task_applications 
                (task_id, student_id, message, bid_amount, created_at) 
                VALUES 
                (:task_id, :student_id, :message, :bid_amount, NOW())";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":task_id", $data->task_id);
        $stmt->bindParam(":student_id", $user_id);
        $stmt->bindParam(":message", $data->message);
        $stmt->bindParam(":bid_amount", $data->bid_amount);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Application submitted successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "Unable to submit application."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Incomplete data."]);
    }
?>