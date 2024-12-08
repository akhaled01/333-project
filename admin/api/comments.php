<?php
require_once '../../db_connection.php';
require_once '../../utils.php';

// Start session and check admin privileges
session_start();
// checkAdminPrivileges();

// Get all comments with user and room information
$query = "
    SELECT 
        c.comment_id,
        c.comment,
        COALESCE(c.admin_response, '') as admin_response,
        COALESCE(c.is_resolved, 0) as is_resolved,
        c.created_at,
        u.name as user_name,
        r.room_name,
        COALESCE(admin.name, '') as admin_name,
        COALESCE(c.admin_id, 0) as admin_id
    FROM Comments c
    INNER JOIN Users u ON c.user_id = u.user_id
    INNER JOIN Rooms r ON c.room_id = r.room_id
    LEFT JOIN Users admin ON c.admin_id = admin.user_id
    WHERE u.user_id IS NOT NULL AND r.room_id IS NOT NULL
    ORDER BY c.created_at DESC
";

try {
    $pdo = db_connect();
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process the comments
    $processed_comments = array_map(function($comment) {
        // Convert empty strings back to null for JSON
        $comment['admin_response'] = $comment['admin_response'] ?: null;
        $comment['admin_name'] = $comment['admin_name'] ?: null;
        $comment['admin_id'] = $comment['admin_id'] ?: null;
        
        // Convert is_resolved to boolean
        $comment['is_resolved'] = (bool)$comment['is_resolved'];
        
        return $comment;
    }, $comments);

    header('Content-Type: application/json');
    echo json_encode($processed_comments);
} catch (PDOException $e) {
    error_log("Database error in comments.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while fetching comments']);
}
