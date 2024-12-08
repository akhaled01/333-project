<?php

include '../db_connection.php';
$pdo = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    exit;
}

echo json_encode(getTop5BookedRooms());

function getTop5BookedRooms() {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT room_id, COUNT(*) as count
        FROM Bookings
        GROUP BY room_id
        ORDER BY count DESC
        LIMIT 5 
    ");

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $rooms = []; 
    $bookings = [];
    foreach($result as $row => $room) {
        // Get the room name using the room_id
        $stmt = $pdo->prepare("
            SELECT room_name
            FROM Rooms
            WHERE room_id = :room_id
        ");
        $stmt->execute([':room_id' => $room['room_id']]);
        $roomName = $stmt->fetch(PDO::FETCH_ASSOC);
        $rooms[] = $roomName['room_name'];

        $bookings[] = $room['count'];
    }

    return ['rooms' => $rooms, 'bookings' => $bookings];
}
?>