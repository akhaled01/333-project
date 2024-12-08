<?php
require '../db_connection.php';

header('Content-Type: application/json');

try {
    // Get the input data from the request
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['comment_id'], $input['response'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Missing parameters.']);
        exit;
    }

    $commentId = $input['comment_id'];
    $response = $input['response'];

    $pdo = db_connect();

    // Update the admin response in the Comments table
    $stmt = $pdo->prepare("
        UPDATE Comments
        SET admin_response = :response, updated_at = NOW()
        WHERE id = :comment_id
    ");
    $stmt->execute([
        ':response' => $response,
        ':comment_id' => $commentId
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Response submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit response.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred.', 'error' => $e->getMessage()]);
}
?>
