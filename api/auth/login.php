<?php
// api/auth/login.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';
include_once '../utils/jwt.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    sendError("Missing email or password.");
}

$email = trim($data->email);
$password = $data->password;

$query = "SELECT id, username, email, password_hash, name, role, status, profile_image FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($password, $row['password_hash'])) {
        
        if ($row['status'] == 'banned') {
            sendError("Your account has been suspended.");
        }

        // Generate JWT
        $payload = [
            "iss" => "http://localhost/projects/kadam",
            "aud" => "http://localhost/projects/kadam",
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24), // 24 hours
            "data" => [
                "id" => $row['id'],
                "username" => $row['username'],
                "email" => $row['email'],
                "role" => $row['role']
            ]
        ];

        $jwt = JWT::encode($payload);

        // Update last login
        $updateQuery = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(":id", $row['id']);
        $updateStmt->execute();

        // Remove password from response
        unset($row['password_hash']);

        sendSuccess("Login successful.", [
            "token" => $jwt,
            "user" => $row
        ]);
    } else {
        sendError("Invalid password.");
    }
} else {
    sendError("User not found.");
}
?>