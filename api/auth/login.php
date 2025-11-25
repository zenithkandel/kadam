<?php
include_once '../config/cors.php';
session_start();
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $query = "SELECT id, name, email, password_hash, role, status FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $data->email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($data->password, $row['password_hash'])) {
            
            if ($row['status'] == 'banned') {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "Your account has been suspended."]);
                exit;
            }

            // Set Session Variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];

            // Update last login
            $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(":id", $row['id']);
            $updateStmt->execute();

            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Login successful.",
                "role" => $row['role'],
                "redirect" => "../dashboard/" . $row['role'] . "/dashboard.html"
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Invalid password."]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Incomplete data."]);
}
?>