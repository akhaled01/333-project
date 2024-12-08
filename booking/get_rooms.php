<?php
require '../db_connection.php';

$pdo = db_connect();

echo getRooms($pdo);

// Function to get all rooms
function getRooms($pdo) {

    $stmt = $pdo->prepare("SELECT * FROM Rooms");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no rooms found, return error message
    if (empty($result)){
        //http_response_code(404);
        return json_encode(["error"=> true, "message" => "No rooms found"]);
    }

    // Return rooms
    return json_encode($result);
}
?>