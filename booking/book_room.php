<?php
    require '../db_connection.php';
    $pdo = db_connect();
    session_start();

    // Check if the user is logged in, if not then redirect him to login page
    if (!isset($_SESSION['user_id'])) {
        header("location: ../auth/login.php");
        exit;
    }

    // Handle the form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Decode the json data received (since we are using javascript then the way to access the data is through php://input)
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if the required parameters are set
        if (isset($data['room_id'], $data['booking_date'], $data['booking_time'])) {
            $userID = $_SESSION['user_id'];
            $roomID = $data['room_id'];
            $date = $data['booking_date'];
            $time = $data['booking_time'];
    
            // Change the time format to 24-hour format and extract the start time only (duration is fixed so we don't need an end time)
            $splitTime = explode(' - ', $time);
            $time = $splitTime[0];
            $startTime = date('H:i', strtotime($time));

            // Call the booking function
            $result = bookRoom($userID, $roomID, $date, $startTime, $pdo);
    
            // Return the result as JSON to the JavaScript
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        }
        exit;
    }

    // Function to book a room
    function bookRoom($userID, $roomID, $date, $time, $pdo) {
        // Check if the date is in the past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            return ['success' => false, 'message' => 'You cannot book a room for a past date.'];
        }
        // Check if the time is in the past
        if (strtotime($date) === strtotime(date('Y-m-d')) && strtotime($time) < strtotime(date('H:i'))) {
            return ['success' => false, 'message' => 'You cannot book a room for a past time.'];
        }
        try {
            // Step 1: Check if the room is available
            $stmt = $pdo->prepare("
                SELECT * FROM Bookings 
                WHERE room_id = :room_id 
                AND date = :date
                AND time = :time
            ");

            $stmt->execute([
                ':room_id' => $roomID,
                ':date' => $date,
                ':time' => $time,
            ]);

            // Check if the room is already booked for the selected time
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Room is already booked for the selected time. Please refresh the page to see the updated available times.'];
            }

            // Step 2: Book the room if it is available
            $insert = $pdo->prepare("
                INSERT INTO Bookings (user_id, room_id, date, time, status) 
                VALUES (:user_id, :room_id, :date, :time, 'pending')
            "); // Status is set to pending by default
            $insert->execute([
                ':user_id' => $userID,
                ':room_id' => $roomID,
                ':date' => $date,
                ':time' => $time,
            ]);

            return ['success' => true, 'message' => 'Booking request submitted.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
?>
