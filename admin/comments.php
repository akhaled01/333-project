<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

$pdo = db_connect();
$error = $success = null;


try {
    $stmt = $pdo->prepare("
        SELECT 
            c.comment_id,
            c.user_id,
            c.room_id,
            c.comment,
            c.created_at,
            c.admin_response,
            r.room_name,
            u.name as user_name,
            u.email as user_email
        FROM Comments c
        JOIN Rooms r ON c.room_id = r.room_id
        JOIN Users u ON c.user_id = u.user_id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Failed to fetch comments: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['comment_id'] ?? null;
    $response = trim($_POST['admin_response'] ?? '');

    if (!$comment_id || empty($response)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Comments SET admin_response = ? WHERE comment_id = ?");
            $stmt->execute([$response, $comment_id]);
            $success = "Response added successfully!";
        } catch (Exception $e) {
            $error = "Failed to add response: " . $e->getMessage();
        }
    }
}

$user_id = $_SESSION['user_id']; 

$stmt = $pdo->prepare("SELECT * FROM Notifications WHERE user_id = ? AND is_read = FALSE ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Comments</title>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    .comment-card {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .error {
        color: red;
    }

    .success {
        color: green;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="notifications">

            //here
            <h3>Your Notifications</h3>
            <?php if (!empty($notifications)) : ?>
            <ul>
                <?php foreach ($notifications as $notification) : ?>
                <li>
                    <?= htmlspecialchars($notification['message']) ?>
                    <form method="POST" action="mark_notification_read.php">
                        <input type="hidden" name="notification_id"
                            value="<?= htmlspecialchars($notification['notification_id']) ?>">
                        <button type="submit">Mark as Read</button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else : ?>
            <p>No new notifications.</p>
            <?php endif; ?>
        </div>
        //here

        <h1>Manage Comments</h1>

        <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="comment-card">
            <h3>Room: <?= htmlspecialchars($comment['room_name']) ?></h3>
            <p><strong>User:</strong> <?= htmlspecialchars($comment['user_name']) ?>
                (<?= htmlspecialchars($comment['user_email']) ?>)</p>
            <p><strong>Comment:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
            <p><strong>Submitted On:</strong> <?= htmlspecialchars($comment['created_at']) ?></p>

            <?php if (!empty($comment['admin_response'])): ?>
            <p><strong>Admin Response:</strong> <?= htmlspecialchars($comment['admin_response']) ?></p>
            <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['comment_id']) ?>">
                <textarea name="admin_response" rows="3" placeholder="Write your response..." required></textarea><br>
                <button type="submit">Respond</button>
            </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p>No comments to display.</p>
        <?php endif; ?>
    </div>
</body>

</html>
