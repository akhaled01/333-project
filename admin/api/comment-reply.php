<?php
require_once '../../db_connection.php';
require_once '../../utils.php';

// Start session and check admin privileges
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['comment_id']) || !isset($data['reply'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $pdo = db_connect();
    // Update the comment with admin's response
    $query = "UPDATE Comments SET admin_response = ?, admin_id = ?, is_resolved = TRUE WHERE comment_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        $data['reply'],
        $_SESSION['user_id'],
        $data['comment_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Comment not found']);
    }
} catch (PDOException $e) {
    error_log("Database error in comment-reply.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
