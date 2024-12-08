<?php
session_start();
require_once 'db.php'; 

// Fetch notifications for the user
$user_id = $_SESSION['user_id']; 
$notifications = getUserNotifications($user_id);

// Function to fetch user notifications
function getUserNotifications($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM Notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle "Mark as Read" functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];
    markNotificationAsRead($notification_id);
    header("Location: notifications.php"); // Redirect to the same page to refresh the notification list
    exit();
}

// Function to mark a notification as read
function markNotificationAsRead($notification_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE Notifications SET is_read = 1 WHERE notification_id = ?");
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6 mt-8 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold">Notifications</h2>

        <div class="notification-list space-y-6 mt-6">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification p-4 border rounded-lg shadow-md bg-white <?php echo $notification['is_read'] ? 'bg-gray-200' : ''; ?>">
                    <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                    <span class="text-sm text-gray-400"><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></span>

                    <?php if (!$notification['is_read']): ?>
                        <!-- Form to mark the notification as read -->
                        <form action="notifications.php" method="POST" class="mt-2">
                            <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>" />
                            <button type="submit" class="text-blue-500 hover:underline">Mark as Read</button>
                        </form>
                    <?php else: ?>
                        <span class="text-sm text-green-500">Read</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($notifications) === 0): ?>
            <p class="text-gray-500 mt-4">You have no notifications.</p>
        <?php endif; ?>
    </div>
</body>
</html>
