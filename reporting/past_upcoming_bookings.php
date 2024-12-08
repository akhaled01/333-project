<?php


require '../db_connection.php';
$pdo = db_connect();

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

// Function to get all past bookings of a user
function getPastUpcomingBookings($condition) {
    global $pdo;

    // Instead of doing two separate queries for past and upcoming bookings, we can use a single query with a condition
    if ($condition == 'past') {
        $stmt = $pdo->prepare("
            SELECT b.*, r.room_name
            FROM Bookings b
            JOIN Rooms r ON b.room_id = r.room_id
            WHERE b.user_id = :user_id 
            AND (
                b.date < CURDATE()
                OR (b.date = CURDATE() AND b.time < CURTIME())
            )
            ORDER BY b.date DESC, b.time DESC
        ");
    } elseif ($condition == 'upcoming') {
        $stmt = $pdo->prepare("
            SELECT b.*, r.room_name
            FROM Bookings b
            JOIN Rooms r ON b.room_id = r.room_id
            WHERE b.user_id = :user_id 
            AND (
                b.date > CURDATE()
                OR (b.date = CURDATE() AND b.time >= CURTIME())
            )
            ORDER BY b.date ASC, b.time ASC
        ");
    } else {
        return array("error"=> true, "message" => "Invalid condition");
    }

    $stmt->execute([':user_id' => $_SESSION['user_id']]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($result)){
        return array("error"=> true, "message" => "No past bookings found");
    }

    // Change the saved time format to 12-hour format to make it more user-friendly
    foreach($result as $row => $booking){
        $result[$row]['time'] = date('g:i A', strtotime($booking['time'])) . ' - ' . date('g:i A', strtotime($booking['time'] . ' +50 minutes'));
    }

    return $result;
}

?>