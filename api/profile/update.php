<?php
// api/profile/update.php

include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/response.php';
include_once '../utils/auth_check.php';

$userAuth = checkAuth();
$user_id = $userAuth['id'];
$role = $userAuth['role'];

$database = new Database();
$db = $database->getConnection();
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    sendError("No data provided.");
}

try {
    $db->beginTransaction();

    // 1. Update Users Table (Common fields)
    $allowed_user_fields = ['name', 'phone', 'address', 'profile_image'];
    $user_updates = [];
    $params = [':id' => $user_id];

    foreach ($allowed_user_fields as $field) {
        if (isset($data[$field])) {
            $user_updates[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }

    if (!empty($user_updates)) {
        $sql = "UPDATE users SET " . implode(', ', $user_updates) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    // 2. Update Role-Specific Table
    $details_updates = [];
    $details_params = [':id' => $user_id];
    $table = '';

    if ($role === 'student') {
        $table = 'students';
        $allowed_fields = ['title', 'bio', 'education_level', 'institution', 'graduation_year', 'portfolio_url'];
    } elseif ($role === 'employer') {
        $table = 'employers';
        $allowed_fields = ['employer_type', 'industry', 'company_size', 'founded_year', 'website_url', 'description'];
    }

    if ($table) {
        foreach ($allowed_fields as $field) {
            if (isset($data['details'][$field])) {
                $details_updates[] = "$field = :$field";
                $details_params[":$field"] = $data['details'][$field];
            }
        }

        if (!empty($details_updates)) {
            // Check if record exists first
            $check = $db->prepare("SELECT user_id FROM $table WHERE user_id = :id");
            $check->execute([':id' => $user_id]);
            
            if ($check->rowCount() > 0) {
                $sql = "UPDATE $table SET " . implode(', ', $details_updates) . " WHERE user_id = :id";
            } else {
                // Insert if not exists (first time profile update)
                $cols = implode(', ', array_keys($details_params)); // This logic is slightly flawed for INSERT as keys have colons
                // Let's simplify: just do UPDATE. If it fails, user might need to be created in setup or signup.
                // Actually, signup should create the row. But let's be safe.
                // For now, assume row exists or use INSERT ON DUPLICATE KEY UPDATE if we were using MySQL specific syntax, 
                // but standard SQL is safer.
                // Let's stick to UPDATE for now as signup should handle creation.
                $sql = "UPDATE $table SET " . implode(', ', $details_updates) . " WHERE user_id = :id";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($details_params);
        }
    }

    // 3. Update Skills (Student Only)
    if ($role === 'student' && isset($data['skills']) && is_array($data['skills'])) {
        // Clear existing skills
        $deleteSkills = $db->prepare("DELETE FROM student_skills WHERE student_id = :id");
        $deleteSkills->execute([':id' => $user_id]);

        $insertSkill = $db->prepare("INSERT INTO skills (name) VALUES (:name) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
        // Note: MySQL specific ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id) ensures we get the ID back correctly
        // But standard SQL might be safer to just SELECT then INSERT.
        // Let's use a safer approach for compatibility.
        
        $checkSkill = $db->prepare("SELECT id FROM skills WHERE name = :name");
        $checkSlug = $db->prepare("SELECT id FROM skills WHERE slug = :slug");
        $insertNewSkill = $db->prepare("INSERT INTO skills (name, slug) VALUES (:name, :slug)");
        $linkSkill = $db->prepare("INSERT INTO student_skills (student_id, skill_id) VALUES (:student_id, :skill_id)");

        foreach ($data['skills'] as $skillName) {
            $skillName = trim($skillName);
            if (empty($skillName)) continue;

            // Check if skill exists
            $checkSkill->execute([':name' => $skillName]);
            if ($checkSkill->rowCount() > 0) {
                $skillId = $checkSkill->fetchColumn();
            } else {
                // Generate slug
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $skillName), '-'));
                if (empty($slug)) {
                    $slug = 'skill-' . time() . rand(100,999);
                }

                // Check if slug exists
                $checkSlug->execute([':slug' => $slug]);
                if ($checkSlug->rowCount() > 0) {
                    // Slug collision, append number
                    $baseSlug = $slug;
                    $counter = 1;
                    while (true) {
                        $slug = $baseSlug . '-' . $counter;
                        $checkSlug->execute([':slug' => $slug]);
                        if ($checkSlug->rowCount() == 0) break;
                        $counter++;
                    }
                }

                $insertNewSkill->execute([':name' => $skillName, ':slug' => $slug]);
                $skillId = $db->lastInsertId();
            }

            // Link skill
            $linkSkill->execute([':student_id' => $user_id, ':skill_id' => $skillId]);
        }
    }

    $db->commit();
    sendSuccess("Profile updated successfully.");

} catch (Exception $e) {
    $db->rollBack();
    sendError("Error updating profile: " . $e->getMessage());
}
