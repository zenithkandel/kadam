<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/auth_check.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

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
