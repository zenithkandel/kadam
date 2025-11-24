<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$category = isset($_GET['category']) ? $_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : null;

try {
    $query = "SELECT t.*, u.name as employer_name, e.company_size, e.industry 
            FROM tasks t 
            JOIN users u ON t.employer_id = u.id 
            LEFT JOIN employers e ON u.id = e.user_id 
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

    echo json_encode(["success" => true, "data" => $tasks]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error retrieving tasks.", "error" => $e->getMessage()]);
}
?>