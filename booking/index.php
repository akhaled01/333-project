<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

/*

// This is the old html design

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>333 Project Website!</title>
    <link rel="stylesheet" href="../output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body class="bg-zinc-900 text-zinc-100">
    <!-- Navigation -->
    <nav class="bg-zinc-800 border-b border-zinc-700">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-zinc-100">333 Project Website!</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page header -->
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-zinc-100">Booking System</h1>
            <p class="mt-2 text-sm text-zinc-400">Here you can book rooms and cancel your booking!</p>
        </div>

        <!-- Main content -->
        <div class="px-4 py-6 sm:px-0 space-y-4">
            <div class="flex flex-col md:flex-row md:space-x-3 w-full">
                <div class="flex-1 max-w-xl px-10">
                    <h1 class="py-5 text-xl font-semibold text-gray-800">
                        Booking System
                    </h1>
                    <form id="booking-form" class="space-y-6">
                        <div>
                            <label for="room" class="block text-sm font-medium text-gray-700">Select Room:</label>
                            <select id="room" name="room"
                                class="mt-2 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </select>
                        </div>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Booking Date:</label>
                            <input type="date" id="date" name="booking_date" require
                                class="mt-2 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <script>
                            // JavaScript to only make user able to select today's date or future dates
                            const today = new Date().toISOString().split('T')[0]; // Get today's date (it usually comes with time, so split it)
                            document.getElementById('date').setAttribute('min', today); // Set the html min attribute to today's date
                        </script>
                        <div>
                            <button id="view-times-btn" type="button"
                                class="bg-[#11101D] text-white py-3 px-6 rounded-md hover:bg-[#2c2a36] focus:outline-none focus:ring-2 focus:ring-[#11101D] focus:ring-opacity-50">
                                View Available Times
                            </button>
                        </div>
                    </form>
                    <div id="available-times-container" class="hidden space-y-4">
                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700">Select Time:</label>
                            <select id="time" name="time"
                                class="mt-2 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <!-- Times will be populated dynamically -->
                            </select>
                        </div>
                        <button id="book-btn"
                            class="bg-[#11101D] text-white py-3 px-6 rounded-md hover:bg-[#2c2a36] focus:outline-none focus:ring-2 focus:ring-[#11101D] focus:ring-opacity-50">
                            Book
                        </button>
                    </div>
                    <div>
                    </div>
                </div>
                <div id="userBookings" class="flex-1 px-10">
                    <h1 class="py-5 text-xl font-semibold text-gray-800">Your Bookings</h1>
                    <div class="overflow-hidden rounded-lg border border-gray-300 overflow-x-auto">
                        <table class="table-auto border-collapse w-full">
                            <thead>
                                <tr class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                                    <th class="border border-gray-300 px-4 py-2">Room</th>
                                    <th class="border border-gray-300 px-4 py-2">Date</th>
                                    <th class="border border-gray-300 px-4 py-2">Time</th>
                                    <th class="border border-gray-300 px-4 py-2">Status</th>
                                    <th class="border border-gray-300 px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody" class="text-sm text-gray-800">
                                <!-- Populate bookings dynamically using JS -->
                                <tr class="hover:bg-gray-50">
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
<script src="booking/booking.js"></script>
</html>*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room</title>
    <link rel="stylesheet" href="../output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../output.css">
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-zinc-900 text-zinc-100">
    <!-- Navigation -->
    <?php include '../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Page header -->
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-zinc-100">Book & Cancel Bookings</h1>
            <p class="mt-2 text-sm text-zinc-400">Here you can book rooms and cancel your booking!</p>
        </div>

        <!-- Main content -->
        <div class="flex flex-col md:flex-row md:space-x-10 p-5 hidde-overflow">
            <!-- Booking Form -->
            <div class="w-full md:w-1/2">
                <h2 class="text-xl font-semibold text-zinc-100 p-2 pb-5">Booking System</h2>
                <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700">
                    <div class="p-6">
                        <form id="booking-form" class="space-y-6">
                            <div>
                                <label for="room" class="block text-sm font-medium text-zinc-300">Select Room:</label>
                                <select id="room" name="room"
                                    class="mt-2 block w-full p-2 border border-zinc-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-zinc-700 text-zinc-100">
                                </select>
                            </div>
                            <div>
                                <label for="date" class="block text-sm font-medium text-zinc-300">Booking Date:</label>
                                <input type="date" id="date" name="booking_date" required
                                    class="mt-2 block w-full p-2 border border-zinc-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-zinc-700 text-zinc-100">
                            </div>
                            <script>
                                // JavaScript to only allow selection of today's date or future dates
                                const today = new Date().toISOString().split('T')[0];
                                document.getElementById('date').setAttribute('min', today);
                            </script>
                            <div>
                                <button id="view-times-btn" type="button"
                                    class="bg-zinc-700 text-white py-3 px-6 rounded-md hover:bg-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    View Available Times
                                </button>
                            </div>
                        </form>
                        <div id="available-times-container" class="hidden space-y-4 mt-4">
                            <div>
                                <label for="time" class="block text-sm font-medium text-zinc-300">Select Time:</label>
                                <select id="time" name="time"
                                    class="mt-2 block w-full p-2 border border-zinc-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-zinc-700 text-zinc-100">
                                </select>
                            </div>
                            <div>
                                <button id="book-btn"
                                    class="bg-zinc-700 text-white py-3 px-6 rounded-md hover:bg-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Book
                                </button>
                            </div>
                        </div>
                        <div id="bookingResult" class="hidden mt-4 p-4 rounded-md text-sm text-zinc-100"></div>
                    </div>
                </div>
            </div>
            <!-- User Bookings -->
            <div class="w-full md:w-1/2">
                <h2 class="text-xl font-semibold text-zinc-100 pt-5">Your Upcoming Bookings</h2>
                <p class="mt-2 text-sm text-zinc-400 pb-5">You can press on cancel to cancel a booking!</p>
                <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700">
                    <div class="overflow-hidden rounded-lg overflow-x-auto">
                        <table class="table-auto border-collapse w-full text-zinc-100">
                            <thead>
                                <tr class="bg-zinc-700 text-left text-sm font-medium text-zinc-100">
                                    <th class="border border-zinc-700 px-4 py-2">Room</th>
                                    <th class="border border-zinc-700 px-4 py-2">Date</th>
                                    <th class="border border-zinc-700 px-4 py-2">Time</th>
                                    <th class="border border-zinc-700 px-4 py-2">Status</th>
                                    <th class="border border-zinc-700 px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody" class="text-sm">
                                <!-- Populate bookings dynamically using JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="cancelBookingResult" class="hidden mt-4 p-4 rounded-md text-sm text-zinc-100"></div>
            </div>
        </div>
    </div>
</body>
<script src="./booking.js"></script>

</html>