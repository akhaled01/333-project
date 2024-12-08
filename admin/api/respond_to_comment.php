<?php
require '../db_connection.php';

header('Content-Type: application/json');

$pdo = db_connect();
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = $data['comment_id'];
    $responseText = $data['response_text'];

    // Update comment with admin response
    $stmt = $pdo->prepare("UPDATE Comments SET admin_response = :response_text WHERE id = :comment_id");
    if ($stmt->execute([':response_text' => $responseText, ':comment_id' => $commentId])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update comment response']);
    }
}
?>
