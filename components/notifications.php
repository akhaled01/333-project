<?php
require_once __DIR__ . '/../db_connection.php';


function getUnreadNotificationsCount($userId) {
    $conn = db_connect();
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM Notifications 
        WHERE user_id = ? AND is_read = FALSE
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

function getNotifications($userId) {
    $conn = db_connect();
    $stmt = $conn->prepare("
        SELECT n.*, r.room_name, c.comment, c.admin_response
        FROM Notifications n
        JOIN Rooms r ON n.room_id = r.room_id
        JOIN Comments c ON n.comment_id = c.comment_id
        WHERE n.user_id = ?
        ORDER BY n.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ...
}

// Mark notifications as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark_read' && isset($_POST['notification_id'])) {
        $conn = db_connect();
        $stmt = $conn->prepare("
            UPDATE Notifications 
            SET is_read = TRUE 
            WHERE notification_id = ? AND user_id = ?
        ");
        $stmt->execute([$_POST['notification_id'], $_SESSION['user_id']]);
        exit;
    } elseif ($_POST['action'] === 'mark_all_read') {
        $conn = db_connect();
        $stmt = $conn->prepare("
            UPDATE Notifications 
            SET is_read = TRUE 
            WHERE user_id = ? AND is_read = FALSE
        ");
        $stmt->execute([$_SESSION['user_id']]);
        exit;
    }
}

$unreadCount = 0;
$notifications = [];
if (isset($_SESSION['user_id'])) {
    $unreadCount = getUnreadNotificationsCount($_SESSION['user_id']);
    $notifications = getNotifications($_SESSION['user_id']);
}
?>

<!-- Notifications Dropdown -->
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open; if(open) markAllNotificationsRead();" 
            class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium relative">
        <i class="fas fa-bell"></i>
        <?php if ($unreadCount > 0): ?>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center notification-counter">
                <?php echo $unreadCount; ?>
            </span>
        <?php endif; ?>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         class="absolute right-0 mt-2 w-80 bg-zinc-800 rounded-md shadow-lg py-1 z-50 border border-zinc-700"
         style="display: none;">
        <?php if (empty($notifications)): ?>
            <div class="px-4 py-2 text-sm text-zinc-400">
                No notifications
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="px-4 py-3 hover:bg-zinc-700 <?php echo !$notification['is_read'] ? 'bg-zinc-700/50' : ''; ?>"
                     data-notification-id="<?php echo $notification['notification_id']; ?>">
                    <div class="text-sm text-zinc-300">
                        <strong>Reply to your comment in <?php echo htmlspecialchars($notification['room_name']); ?></strong>
                    </div>
                    <div class="text-xs text-zinc-400 mt-1">
                        Your comment: <?php echo htmlspecialchars(substr($notification['comment'], 0, 50)) . '...'; ?>
                    </div>
                    <div class="text-xs text-indigo-400 mt-1">
                        Admin reply: <?php echo htmlspecialchars(substr($notification['admin_response'], 0, 50)) . '...'; ?>
                    </div>
                    <div class="text-xs text-zinc-500 mt-1">
                        <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function markAllNotificationsRead() {
    fetch('/components/notifications.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=mark_all_read'
    }).then(() => {
        // Remove the notification counter
        const counter = document.querySelector('.notification-counter');
        if (counter) counter.remove();
        
        // Remove unread styling from all notifications
        document.querySelectorAll('[data-notification-id]').forEach(notification => {
            notification.classList.remove('bg-zinc-700/50');
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Mark individual notifications as read when clicked
    document.querySelectorAll('[data-notification-id]').forEach(notification => {
        notification.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            fetch('/components/notifications.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=mark_read&notification_id=${notificationId}`
            }).then(() => {
                // Remove unread styling
                this.classList.remove('bg-zinc-700/50');
                
                // Update counter
                const counter = document.querySelector('.notification-counter');
                if (counter) {
                    const count = parseInt(counter.textContent) - 1;
                    if (count <= 0) {
                        counter.remove();
                    } else {
                        counter.textContent = count;
                    }
                }
            });
        });
    });
});</script>
