<?php
    
    require '../db_connection.php';
    $pdo = db_connect();

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("location: ../auth/login.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo getUserBookings($_SESSION['user_id']);
        exit;
    }

    // Function to get all bookings of a user 
    function getUserBookings($userId) {
        global $pdo;

        // Get all the bookings of the user (here we joined the Bookings table with the Rooms table to get the room name)
        $stmt = $pdo->prepare("
            SELECT b.*, r.room_name
            FROM Bookings b
            JOIN Rooms r ON b.room_id = r.room_id
            WHERE b.user_id = :user_id AND b.status != 'cancelled'
            AND (
                b.date > CURDATE()
                OR (b.date = CURDATE() AND b.time >= CURTIME())
            )
            ORDER BY b.date ASC, b.time ASC
        ");
        $stmt->execute([':user_id' => $userId]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)){
            return json_encode(array("error"=> true, "message" => "No bookings found"));
        }

        // Change the saved time format to 12-hour format to make it more user-friendly
        foreach($result as $row => $booking){
            $result[$row]['time'] = date('g:i A', strtotime($booking['time'])) . ' - ' . date('g:i A', strtotime($booking['time'] . ' +50 minutes'));
        }

        return json_encode($result);
    }

?>