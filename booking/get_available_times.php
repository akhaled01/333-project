<?php

    
    //WHEN YOU OPEN THIS PLEASE FIX THE this function so it doesn't display past time if the date is today slots OR at least prevent the user from booking them
    


require '../db_connection.php';
$pdo = db_connect();

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['room_id'], $data['date'])) {
        $roomID = $data['room_id'];
        $date = $data['date'];

        echo getAvailableTimeSlots($roomID, $date);
    } else {
        echo json_encode(['error' => true, 'message' => 'Missing required parameters.']);
    }
    exit;
}

function getAvailableTimeSlots($roomID, $date){
    global $pdo;
    
    $availableSlots = [];
    // Set the time slots intrvals (we'll use it to get the available time slots)

    $start_time = strtotime('07:00 AM'); 

    // This part is to prevent the user from booking past time slots if the date is today
    if ($date === date('Y-m-d')) {
        $nextHour = strtotime('+1 hour', strtotime(date('H:00')));
        $start_time = max($nextHour, $start_time); // Make sure that the start time is at least 7:00 AM and not less
    }

    $end_time = strtotime('05:00 PM');
    $interval = 60 * 60; // 60 minutes interval

    // Get all the booked times for the room on the given date
    $stmt = $pdo->prepare("SELECT time FROM Bookings WHERE room_id = :room_id AND date = :date AND status != 'cancelled'");
    $stmt->execute(['room_id' => $roomID, 'date' => $date]);

    $bookedTimes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get only the time from the result and convert it to an array and match it with the time format we want
    $bookedTimes = array_map(function($booking) {
        return date('H:i', strtotime($booking['time'])); // Format the database time to 'H:i'
    }, $bookedTimes);
    

    // Loop through the time slots and check if the time slot is available and add it 
    for($time = $start_time; $time <= $end_time; $time += $interval){
        $timeSlot = date('H:i', $time); // Format the time to hour:minute
        if(!in_array($timeSlot, $bookedTimes)){
            $availableSlots[] = $timeSlot;
        }
    }

    // Return the available time slots with a 10 minutes break in between example: 07:00 - 07:50
    for($i=0; $i<count($availableSlots); $i++){
        $startTime = strtotime($availableSlots[$i]);
        $endTime = $startTime + (50 * 60); // Start time + 50 minutes

        $availableSlots[$i] = date('g:i A', $startTime) . ' - ' . date('g:i A', $endTime); // The date('g:i A') convert time to am and pm (g=12-hour format, i=minutes, A= am or pm)
    }

    //If no time slots are available, return an error message
    if(empty($availableSlots)){
        return json_encode(array("error"=> true, "message" => "No time slots available for the selected date."));
    }
    
    return json_encode($availableSlots);
}
?>