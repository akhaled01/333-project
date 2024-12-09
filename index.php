<?php
require_once 'db_connection.php';
session_start();

// Redirect to login if not logged in
if (isset($_SESSION['user_id']) && !isset($_SESSION['avatar_url'])) {
    try {
        $conn = db_connect();
        $stmt = $conn->prepare("SELECT avatar_url FROM Users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['avatar_url'] = $result['avatar_url'] ?? '../uploads/avatars/default.png';
    } catch (PDOException $e) {
        error_log("Error fetching avatar URL: " . $e->getMessage());
    }
} 

if (!isset($_SESSION['user_id'])) {
    header('Location: ./auth/login.php');
    exit;
}

// Fetch upcoming bookings for the user
$conn = db_connect();
$stmt = $conn->prepare("
    SELECT b.*, r.room_name 
    FROM Bookings b 
    JOIN Rooms r ON b.room_id = r.room_id 
    WHERE b.user_id = ? 
    AND b.date >= CURDATE() 
    AND b.status != 'cancelled'
    ORDER BY b.date ASC, b.time ASC 
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$upcoming_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's total active bookings count
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM Bookings 
    WHERE user_id = ? 
    AND date >= CURDATE() 
    AND status != 'cancelled'
");
$stmt->execute([$_SESSION['user_id']]);
$total_bookings = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReserveIt - Room Booking System</title>
    <link rel="stylesheet" href="output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-zinc-900 text-zinc-100">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<script src="//unpkg.com/alpinejs" defer></script>

    <nav class="bg-zinc-800 border-b border-zinc-700">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="./index.php" class="text-xl font-bold text-zinc-100">ReserveIt</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center space-x-3">
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <a href="./admin/index.php" class="text-indigo-400 hover:text-indigo-300 px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-shield-alt mr-2"></i>Admin Panel
                                </a>
                            <?php endif; ?>

                            <?php include __DIR__ . '/components/notifications.php'; ?>

                            <a href="./auth/profile.php" class="flex items-center group">
                                <img src="<?php echo !empty($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : '../uploads/avatars/default.png'; ?>"
                                    alt="Profile"
                                    class="h-8 w-8 rounded-full object-cover border border-zinc-600 group-hover:border-indigo-500 transition-colors">
                                <span class="ml-2 text-zinc-300 group-hover:text-zinc-100">
                                    <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                                </span>
                            </a>
                            <a href="./index.php" class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-home mr-2"></i>Home
                            </a>
                            <a href="./auth/logout.php" class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-3">
                            <a href="./auth/login.php" class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="./auth/signup.php" class="bg-indigo-600 text-white hover:bg-indigo-700 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-user-plus mr-2"></i>Sign Up
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Quick Actions -->
            <div class="bg-zinc-800 p-6 rounded-lg border border-zinc-700 col-span-full lg:col-span-1">
                <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 gap-4">
                    <a href="./room-explore/index.php" class="flex items-center justify-between p-4 bg-zinc-700 rounded-lg hover:bg-zinc-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-search text-blue-400 mr-3"></i>
                            <span>Browse Rooms</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="./booking/index.php" class="flex items-center justify-between p-4 bg-zinc-700 rounded-lg hover:bg-zinc-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-plus text-green-400 mr-3"></i>
                            <span>Book a Room</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="./auth/profile.php" class="flex items-center justify-between p-4 bg-zinc-700 rounded-lg hover:bg-zinc-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-user text-purple-400 mr-3"></i>
                            <span>My Profile</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="./reporting/index.php" class="flex items-center justify-between p-4 bg-zinc-700 rounded-lg hover:bg-zinc-600 transition-colors duration-200">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-red-400 mr-3"></i>
                            <span>Reporting & Analytics</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="bg-zinc-800 p-6 rounded-lg border border-zinc-700 col-span-full lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">Upcoming Bookings</h2>
                    <span class="text-sm text-zinc-400">Total Active: <?php echo $total_bookings; ?></span>
                </div>
                <?php if (empty($upcoming_bookings)): ?>
                    <div class="text-center py-8 text-zinc-400">
                        <i class="fas fa-calendar-times text-4xl mb-3"></i>
                        <p>No upcoming bookings</p>
                        <a href="/room-explore" class="inline-block mt-4 text-blue-400 hover:text-blue-300">Browse available rooms</a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($upcoming_bookings as $booking): ?>
                            <div class="flex items-center justify-between p-4 bg-zinc-700 rounded-lg">
                                <div class="flex-1">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($booking['room_name']); ?></h3>
                                    <div class="text-sm text-zinc-400 mt-1">
                                        <span class="mr-4">
                                            <i class="far fa-calendar mr-1"></i>
                                            <?php echo date('F j, Y', strtotime($booking['date'])); ?>
                                        </span>
                                        <span>
                                            <i class="far fa-clock mr-1"></i>
                                            <?php echo date('g:i A', strtotime($booking['time'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium 
                                    <?php echo $booking['status'] === 'confirmed' ? 'bg-green-900 text-green-200' : 'bg-yellow-900 text-yellow-200'; ?>">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center mt-4">
                            <a href="./reporting/index.php" class="text-blue-400 hover:text-blue-300 text-sm">View all bookings</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>