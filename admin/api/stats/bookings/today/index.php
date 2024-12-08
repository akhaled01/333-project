<?php
require '../../../../../db_connection.php';

header('Content-Type: application/json');

try {
    $pdo = db_connect();
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Bookings WHERE DATE(date) = CURDATE()");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result === false) {
        throw new PDOException("No data returned");
    }
    
    echo json_encode(['value' => (int)$result['total']]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
}
