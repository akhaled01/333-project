<?php
session_start();
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(403);
        exit('Unauthorized');
    }

    $commentId = $_POST['comment_id'] ?? null;
    $roomId = $_POST['room_id'] ?? null;
    $userId = $_POST['user_id'] ?? null;

    if (!$commentId || !$roomId || !$userId) {
        http_response_code(400);
        exit('Missing required parameters');
    }

    try {
        $conn = db_connect();
        
        // Create notification
        $stmt = $conn->prepare("
            INSERT INTO Notifications (user_id, comment_id, room_id)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $commentId, $roomId]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        exit('Database error: ' . $e->getMessage());
    }
}
