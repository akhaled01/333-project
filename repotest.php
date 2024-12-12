<?php
    require '../db_connection.php';
    $pdo = db_connect();
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("location: ../auth/login.php");
        exit;
    }

// Fetch user information from session
$EmailOfTheUser = $_SESSION['users'];
$NameOfTheUser = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown User';
$Id = $_SESSION['user_id']; // Set $Id from session

// database connection
require_once 'db_connection.php';

// Functions to fetch data
function getRoomUsageStats($pdo) {
    $query = "SELECT r.room_name, COUNT(b.booking_id) AS total_bookings
              FROM Rooms r
              LEFT JOIN Bookings b ON r.room_id = b.room_id
              GROUP BY r.room_id
              ORDER BY total_bookings DESC";
    return $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingBookings($pdo, $Id) {
    $query = "SELECT r.room_name, b.date, b.time
              FROM Bookings b
              JOIN Rooms r ON b.room_id = r.room_id
              WHERE b.user_id = :user_id AND b.date >= CURDATE()
              ORDER BY b.date, b.time";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $Id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPastBookings($pdo, $Id) {
    $query = "SELECT r.room_name, b.date, b.time
              FROM Bookings b
              JOIN Rooms r ON b.room_id = r.room_id
              WHERE b.user_id = :user_id AND b.date < CURDATE()
              ORDER BY b.date DESC, b.time DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $Id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch data
$roomUsageStats = getRoomUsageStats($pdo);
$upcomingBookings = getUpcomingBookings($pdo, $Id);
$pastBookings = getPastBookings($pdo, $Id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporting & Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include("index.php"); ?>
    <div class="container mt-4">
       
        <p>Welcome!, <strong><?= htmlspecialchars($NameOfTheUser) ?></strong></p>

        <!-- Room Usage Statistics -->
        <section>
            <h2>Room Usage Statistics</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roomUsageStats as $room): ?>
                        <tr>
                            <td><?= htmlspecialchars($room['room_id']) ?></td>
                            <td><?= htmlspecialchars($room['total_bookings']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <canvas id="roomUsageChart"></canvas>
        </section>

        <!-- Upcoming Bookings -->
        <section class="mt-5">
            <h2>Upcoming Bookings</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($upcomingBookings)): ?>
                        <tr><td colspan="3">There is no upcoming bookings.</td></tr>
                    <?php else: ?>
                        <?php foreach ($upcomingBookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['room_id']) ?></td>
                                <td><?= htmlspecialchars($booking['date']) ?></td>
                                <td><?= htmlspecialchars($booking['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!-- Past Bookings -->
        <section class="mt-5">
            <h2>Past Bookings</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pastBookings)): ?>
                        <tr><td colspan="3">There is no past bookings.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pastBookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['room_id']) ?></td>
                                <td><?= htmlspecialchars($booking['date']) ?></td>
                                <td><?= htmlspecialchars($booking['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        const ctx = document.getElementById('roomUsageChart').getContext('2d');
        const roomUsageData = <?= json_encode($roomUsageStats) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: roomUsageData.map(item => item.room_id),
                datasets: [{
                    label: 'Bookings',
                    data: roomUsageData.map(item => item.total_bookings),
                    backgroundColor: 'rgba(255, 255, 255, 0.7)',
                    borderColor: 'rgba(0, 0, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <?php include("base.php"); ?>
</body>
</html>
