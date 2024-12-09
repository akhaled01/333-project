<?php
require_once __DIR__ . '/../../../db_connection.php';
header('Content-Type: application/json');
//SQL Query
try {
    $db = db_connect();

    $stmt = $db->prepare("
        SELECT 
            c.comment_id,
            c.user_id,
            c.room_id,
            c.comment,
            c.created_at,
            r.room_name,
            u.username as user_name,
            u.email as user_email
        FROM Comments c
        JOIN Rooms r ON c.room_id = r.room_id
        JOIN Users u ON c.user_id = u.user_id
        ORDER BY c.created_at DESC
    ");

    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $comments
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch comments',
        'error' => $e->getMessage()
    ]);
}
