// Call this when a user clicks on a notification or after displaying them
$stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
