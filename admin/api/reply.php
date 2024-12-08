<?php
require_once '../../db_connection.php';
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$commentId = $data['comment_id'] ?? null;
$reply = $data['reply'] ?? null;

if (!$commentId || !$reply) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

try {
    $conn = db_connect();
    
    // Get the comment's user_id and room_id
    $stmt = $conn->prepare("SELECT user_id, room_id FROM Comments WHERE comment_id = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$comment) {
        http_response_code(404);
        echo json_encode(['error' => 'Comment not found']);
        exit;
    }
    
    // Update the comment with admin response
    $stmt = $conn->prepare("
        UPDATE Comments 
        SET admin_response = ?, 
            admin_id = ?, 
            is_resolved = TRUE 
        WHERE comment_id = ?
    ");
    $stmt->execute([$reply, $_SESSION['user_id'], $commentId]);
    
    // Create notification for the user
    $stmt = $conn->prepare("
        INSERT INTO Notifications (user_id, comment_id, room_id) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$comment['user_id'], $commentId, $comment['room_id']]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Database error in reply.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
