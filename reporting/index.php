<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

include 'past_upcoming_bookings.php';

$pastBookings = getPastUpcomingBookings("past");
$upcomingBookings = getPastUpcomingBookings("upcoming");

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>333 Project Website!</title>
    <link rel="stylesheet" href="../output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Boxicons -->
</head>

<body class="bg-zinc-900 text-zinc-100">
    <!-- Navigation -->
    <?php include '../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page header -->
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-zinc-100">Reporting & Analytics</h1>
            <p class="mt-2 text-sm text-zinc-400">Here you can view our room usage and your past and upcoming bookings!</p>
        </div>

        <!-- Main content -->
        <div class="flex flex-col p-5 hidde-overflow">
            <!-- Room Usage and Popularity Chart -->
            <h2 class="text-xl font-semibold text-zinc-100 p-2 pb-5">Room Usage and Popularity</h2>
            <div class="flex flex-col md:flex-row md:space-x-5 gap-5 md:gap-0">
                <div class="w-full md:w-1/2">
                    <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700 h-[300px] w-full mx-auto">
                        <div class="px-6 py-4 border-b border-zinc-700">
                            <h2 class="text-xl font-semibold text-zinc-100">Our Top Booked Rooms</h2>
                        </div>
                        <div class="p-6">
                            <canvas id="RoomsChart" class="w-full h-full" style="height: 170px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700 h-[300px] w-full mx-auto">
                        <div class="px-6 py-4 border-b border-zinc-700">
                            <h2 class="text-xl font-semibold text-zinc-100">Room Booking Trends Over 6 Months</h2>
                        </div>
                        <div class="p-6">
                            <canvas id="DatesChart" class="w-full h-full" style="height: 170px;"></canvas>
                        </div>
                    </div>
                </div>
                <script src="chart.js"></script>
            </div>
            <!-- Upcoming and Past Bookings -->
            <div class="flex flex-col md:flex-row md:space-x-5">
                <div class="w-full md:w-1/2">
                    <h2 class="text-xl font-semibold text-zinc-100 p-2 py-5">Upcoming Bookings</h2>
                    <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700">
                        <div class="p-6">
                            <?php
                            if (array_key_exists('error', $upcomingBookings) && $upcomingBookings['error'] === true) {
                                echo '<div class="text-center py-8 text-zinc-400">
                                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                                    <p class="text-lg">No Upcoming bookings found!</p>
                                </div>';
                            } else {
                                foreach ($upcomingBookings as $row => $booking) {
                                    echo '<div class="flex justify-between items-center mb-4">';
                                    echo '<div>';
                                    echo '<h3 class="text-lg font-semibold">' . $booking['room_name'] . '</h3>';
                                    echo '<p class="text-sm text-zinc-400">
                                            <i class="bx bx-calendar"></i>' . " " . $booking['date'] . "\t" .
                                        '<i class="bx bx-time"></i>' . " " . $booking['time'] .
                                        '</p>';
                                    echo '<h4>Status: <span class="px-2 py-1 rounded-full text-sm ' .
                                        ($booking['status'] === 'pending' ? 'text-yellow-400' : ($booking['status'] === 'confirmed' ? 'text-green-400' : ($booking['status'] === 'cancelled' ? 'text-red-400' : ''))) .
                                        '">' . $booking['status'] . '</span></h4>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <h2 class="text-xl font-semibold text-zinc-100 p-2 py-5">Past Bookings</h2>
                    <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700">
                        <div class="p-6">
                            <?php
                            if (array_key_exists('error', $pastBookings) && $pastBookings['error'] === true) {
                                echo '<div class="text-center py-8 text-zinc-400">
                                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                                    <p class="text-lg">No past bookings found!</p>
                                </div>';
                            } else {
                                foreach ($pastBookings as $row => $booking) {
                                    echo '<div class="flex justify-between items-center mb-4">';
                                    echo '<div>';
                                    echo '<h3 class="text-lg font-semibold">' . $booking['room_name'] . '</h3>';
                                    echo '<p class="text-sm text-zinc-400">
                                            <i class="bx bx-calendar"></i>' . " " . $booking['date'] . "\t" .
                                        '<i class="bx bx-time"></i>' . " " . $booking['time'] .
                                        '</p>';
                                    echo '<h4>Status: <span class="px-2 py-1 rounded-full text-sm ' .
                                        ($booking['status'] === 'pending' ? 'text-yellow-400' : ($booking['status'] === 'confirmed' ? 'text-green-400' : ($booking['status'] === 'cancelled' ? 'text-red-400' : ''))) .
                                        '">' . $booking['status'] . '</span></h4>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>