<?php
require_once __DIR__ . '/../../../db_connection.php';
header('Content-Type: application/json');

try {
    $db = db_connect();
    
    $stmt = $db->prepare("
        SELECT 
            b.booking_id,
            b.user_id,
            b.room_id,
            b.date,
            b.time,
            b.admin_id,
            b.status,
            r.room_name,
            r.image_url,
            u.name as user_name,
            u.email as user_email,
            u.role as user_role,
            a.name as admin_name,
            a.email as admin_email
        FROM Bookings b
        JOIN Rooms r ON b.room_id = r.room_id
        JOIN Users u ON b.user_id = u.user_id
        LEFT JOIN Users a ON b.admin_id = a.user_id
        ORDER BY b.date DESC, b.time DESC
    ");
    
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $bookings
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch bookings'
    ]);
}
