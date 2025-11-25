<?php
// api/auth/signup.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

// Basic Validation
if (
    !isset($data->email) || 
    !isset($data->password) || 
    !isset($data->name) || 
    !isset($data->role)
) {
    sendError("Missing required fields.");
}

$email = trim($data->email);
$password = $data->password;
$name = trim($data->name);
$role = $data->role;
$phone = isset($data->phone) ? trim($data->phone) : null;
$address = isset($data->address) ? trim($data->address) : null;

// Validate Role
if (!in_array($role, ['student', 'employer'])) {
    sendError("Invalid role.");
}

// Check if email exists
$query = "SELECT id FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":email", $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    sendError("Email already exists.");
}

// Generate Username (Simple logic: name + random number)
$username = strtolower(str_replace(' ', '', $name)) . rand(1000, 9999);
// Ensure username is unique (simplified for now, ideally loop to check)
$checkUser = $db->prepare("SELECT id FROM users WHERE username = :username");
$checkUser->bindParam(":username", $username);
$checkUser->execute();
if ($checkUser->rowCount() > 0) {
    $username .= rand(10, 99);
}

// Hash Password
$password_hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $db->beginTransaction();

    // Insert into users table
    $query = "INSERT INTO users (username, email, password_hash, name, role, phone, address, status) VALUES (:username, :email, :password_hash, :name, :role, :phone, :address, 'active')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password_hash", $password_hash);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":role", $role);
    $stmt->bindParam(":phone", $phone);
    $stmt->bindParam(":address", $address);

    if ($stmt->execute()) {
        $user_id = $db->lastInsertId();

        // Insert into specific role table
        if ($role == 'student') {
            $query = "INSERT INTO students (user_id) VALUES (:user_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();
        } elseif ($role == 'employer') {
            $employer_type = isset($data->employerType) ? $data->employerType : 'individual';
            $query = "INSERT INTO employers (user_id, employer_type) VALUES (:user_id, :employer_type)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":employer_type", $employer_type);
            $stmt->execute();
        }

        $db->commit();
        sendSuccess("User registered successfully.", ["user_id" => $user_id]);
    } else {
        $db->rollBack();
        sendError("Unable to register user.");
    }
} catch (Exception $e) {
    $db->rollBack();
    sendError("System error: " . $e->getMessage());
}
