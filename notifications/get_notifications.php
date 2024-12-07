require '../db_connection.php';

$pdo = db_connect();
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT n.notification_id, n.message, n.created_at 
                       FROM notifications n 
                       WHERE n.user_id = :user_id AND n.is_read = 0 ORDER BY n.created_at DESC");
$stmt->execute([':user_id' => $userId]);

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notifications);
