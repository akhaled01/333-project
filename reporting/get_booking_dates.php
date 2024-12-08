<?php
session_start();
require '../db_connection.php';
$pdo = db_connect();

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    exit;
}

echo json_encode(getBookingDates());

function getBookingDates() {
    global $pdo;

    // Get the current date
    $currentDate = date('Y-m-d');
    $currentMonth = date('Y-m');

    // Calculate start and end dates for 6 months
    $startDate = date('Y-m-01', strtotime('-3 months', strtotime($currentDate)));
    $endDate = date('Y-m-t', strtotime('+3 months', strtotime($currentDate)));

    // Query to group bookings into 6-month periods
    $query = $pdo->prepare("
        SELECT DATE_FORMAT(`date`, '%Y-%m') AS month, COUNT(*) AS bookings
        FROM Bookings
        WHERE date BETWEEN :startDate AND :endDate
        GROUP BY date
        ORDER BY date ASC
    ");
    $query->execute([':startDate' => $startDate, ':endDate' => $endDate]);
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for Chart.js
    $months = [];
    $bookings = [];

    // Fill the data arrays empty at the beginning
    for ($i = -3; $i <= 3; $i++) {
        $month = date('Y-m', strtotime("$i months", strtotime($currentMonth)));
        $months[] = $month;
        $bookings[] = 0; 
    }

    // Fill the data arrays with the fetched data
    foreach ($data as $row) {
        $monthIndex = array_search($row['month'], $months);
        if ($monthIndex !== false) {
            $bookings[$monthIndex] = $row['bookings'];  // Update the booking count
        }
    }

    return ['months' => $months, 'bookings' => $bookings];
    }
?>
