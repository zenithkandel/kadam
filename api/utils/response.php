<?php
// api/utils/response.php

function sendResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

function sendError($message, $statusCode = 400) {
    sendResponse(false, $message, null, $statusCode);
}

function sendSuccess($message, $data = null) {
    sendResponse(true, $message, $data, 200);
}
?>