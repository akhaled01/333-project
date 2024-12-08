<?php

require '../db_connection.php';
$pdo = db_connect();
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['booking_id'])) {
        echo cancelBooking($data['booking_id'], $_SESSION['user_id']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    }
    exit;
}

function cancelBooking($bookingId, $userId) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            UPDATE Bookings 
            SET status = 'cancelled' 
            WHERE booking_id = :booking_id AND user_id = :user_id
        ");
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':user_id' => $userId,
        ]);

        if ($stmt->rowCount() > 0) {
            return json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
        } else {
            return json_encode(['success' => false, 'message' => 'Cancellation failed.']);
        }
    } catch (PDOException $e) {
        return json_encode(['success' => false, 'message' => 'An error occurred while cancelling the booking.']);
    }
}
?>