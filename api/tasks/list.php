<?php
// api/tasks/list.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';

$database = new Database();
$db = $database->getConnection();

$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

$query = "SELECT t.*, u.name as employer_name, e.company_size, e.industry 
          FROM tasks t 
          JOIN users u ON t.employer_id = u.id 
          JOIN employers e ON u.id = e.user_id 
          WHERE t.status = 'open'";

if ($category) {
    $query .= " AND t.category = :category";
}

if ($search) {
    $query .= " AND (t.title LIKE :search OR t.description LIKE :search)";
}

$query .= " ORDER BY t.created_at DESC";

$stmt = $db->prepare($query);

if ($category) {
    $stmt->bindParam(":category", $category);
}

if ($search) {
    $searchTerm = "%{$search}%";
    $stmt->bindParam(":search", $searchTerm);
}

$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

sendSuccess("Tasks retrieved successfully.", $tasks);
?>