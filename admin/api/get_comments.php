<?php
require '../db_connection.php';

header('Content-Type: application/json');

try {
    $pdo = db_connect();

    // Fetch comments with room details, user details, and admin responses
    $stmt = $pdo->prepare("
        SELECT c.id, c.room_id, c.comment_text, c.created_at, c.admin_response, u.username, r.room_name
        FROM Comments c
        JOIN Users u ON c.user_id = u.id
        JOIN Rooms r ON c.room_id = r.id
        ORDER BY c.created_at DESC
    ");

    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Send the fetched data as JSON
    echo json_encode(['success' => true, 'comments' => $comments]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch comments.', 'error' => $e->getMessage()]);
}
?>
