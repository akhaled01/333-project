<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../db_connection.php';

$pdo = db_connect();

function FetchRooms($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM Rooms");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($result)) {
        return ["error" => true, "message" => "No rooms found"];
    }
    return ["error" => false, "rooms" => $result];
}

function FetchComments($pdo, $room_id) {
    $stmt = $pdo->prepare("SELECT c.comment, c.created_at, u.name 
                           FROM Comments c 
                           JOIN Users u ON c.user_id = u.user_id 
                           WHERE c.room_id = ? 
                           ORDER BY c.created_at DESC");
    $stmt->execute([$room_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $room_id = $_POST['room_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');

    if (!$user_id || !$room_id || empty($comment)) {
        $error = "All fields are required to leave a comment.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO Comments (user_id, room_id, comment) VALUES (?, ?, ?)");
        $success = $stmt->execute([$user_id, $room_id, $comment]);

        if ($success) {
            $message = "New comment added on Room ID $room_id by User ID $user_id.";
            $stmt = $pdo->prepare("INSERT INTO Notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $message]); 

            $successMessage = "Your comment has been added successfully.";
        } else {
            $error = "Failed to add your comment. Please try again.";
        }
    }
}



$rooms = FetchRooms($pdo);
$selectedRoomId = $_GET['room_id'] ?? null;
$comments = $selectedRoomId ? FetchComments($pdo, $selectedRoomId) : [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Browsing</title>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    .room-card {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .room-details {
        margin-top: 20px;
    }

    .comment {
        margin-bottom: 15px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
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
        <h2>Room Browsing</h2>
        <div>
            <?php if ($rooms["error"]) : ?>
            <p><?= $rooms["message"] ?></p>
            <?php else : ?>
            <?php foreach ($rooms["rooms"] as $room) : ?>
            <div class="room-card">
                <h3><?= htmlspecialchars($room['room_name']) ?></h3>
                <p>Capacity: <?= htmlspecialchars($room['capacity']) ?></p>
                <p>Equipment: <?= htmlspecialchars($room['equipment'] ?? "None") ?></p>
                <a href="?room_id=<?= $room['room_id'] ?>">View Details</a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($selectedRoomId) : ?>
        <div class="room-details">
            <h3>Room Details</h3>
            <h4>Comments</h4>
            <?php if (!empty($comments)) : ?>
            <?php foreach ($comments as $comment) : ?>
            <div class="comment">
                <strong><?= htmlspecialchars($comment['name']) ?></strong> <em>(<?= $comment['created_at'] ?>)</em>
                <p><?= htmlspecialchars($comment['comment']) ?></p>
            </div>
            <?php endforeach; ?>
            <?php else : ?>
            <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <h4>Leave a Comment</h4>
            <?php if (isset($error)) : ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if (isset($successMessage)) : ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="hidden" name="room_id" value="<?= htmlspecialchars($selectedRoomId) ?>">
                <textarea name="comment" rows="3" placeholder="Write your comment here..." required></textarea><br>
                <button type="submit">Submit</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>

</html>
