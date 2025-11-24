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
        echo json_encode(["success" => false, "message" => "Only employers can post tasks."]);
        exit;
    }

    if (
        !empty($data->title) &&
        !empty($data->description) &&
        !empty($data->category) &&
        !empty($data->budget) &&
        !empty($data->deadline) &&
        !empty($data->difficulty_level)
    ) {
        $query = "INSERT INTO tasks 
                (employer_id, title, description, category, budget, deadline, difficulty_level, status, created_at) 
                VALUES 
                (:employer_id, :title, :description, :category, :budget, :deadline, :difficulty_level, 'open', NOW())";

        $stmt = $db->prepare($query);

        $stmt->bindParam(":employer_id", $user_id);
        $stmt->bindParam(":title", $data->title);
        $stmt->bindParam(":description", $data->description);
        $stmt->bindParam(":category", $data->category);
        $stmt->bindParam(":budget", $data->budget);
        $stmt->bindParam(":deadline", $data->deadline);
        $stmt->bindParam(":difficulty_level", $data->difficulty_level);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Task created successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["success" => false, "message" => "Unable to create task."]);
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