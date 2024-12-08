require '../db_connection.php';
$pdo = db_connect();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['room_id'], $data['comment'])) {
        $userId = $_SESSION['user_id'];
        $roomId = $data['room_id'];
        $comment = $data['comment'];

        $stmt = $pdo->prepare("INSERT INTO comments (user_id, room_id, comment) VALUES (:user_id, :room_id, :comment)");
        $stmt->execute([
            ':user_id' => $userId,
            ':room_id' => $roomId,
            ':comment' => $comment
        ]);

        echo json_encode(['success' => true, 'message' => 'Comment submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
    }
}
