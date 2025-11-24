<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../utils/jwt.php';

$database = new Database();
$db = $database->getConnection();

$jwt = JWT::getBearerToken();

if (!$jwt) {
    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
    exit;
}

$decoded = JWT::decode($jwt);
if (!$decoded || $decoded['data']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(array("message" => "Access denied. Admin only."));
    exit;
}

try {
    // 1. Count Students
    $query = "SELECT COUNT(*) as count FROM users WHERE role = 'student'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // 2. Count Employers
    $query = "SELECT COUNT(*) as count FROM users WHERE role = 'employer'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $employers_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // 3. Count Active Tasks
    $query = "SELECT COUNT(*) as count FROM tasks WHERE status = 'open'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $active_tasks_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // 4. Total Payouts (Sum of budget for completed tasks)
    $query = "SELECT SUM(budget) as total FROM tasks WHERE status = 'completed'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $total_payouts = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 5. Recent Activity (Newest Users)
    $query = "SELECT id, name, role, created_at FROM users ORDER BY created_at DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(array(
        "success" => true,
        "data" => array(
            "students_count" => $students_count,
            "employers_count" => $employers_count,
            "active_tasks_count" => $active_tasks_count,
            "total_payouts" => $total_payouts,
            "recent_activity" => $recent_users
        )
    ));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Error fetching admin stats: " . $e->getMessage()
    ));
}
?>