<?php
session_start();
require_once __DIR__ . '/../db_connection.php';

$pdo = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notification_id = $_POST['notification_id'] ?? null;

    if (!$notification_id) {
        $_SESSION['error'] = "Notification ID is required.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE Notifications SET is_read = TRUE WHERE notification_id = ?");
        $stmt->execute([$notification_id]);

        $_SESSION['success'] = "Notification marked as read.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to mark notification as read: " . $e->getMessage();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
