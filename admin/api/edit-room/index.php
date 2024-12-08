<?php

include "../../../db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Decode JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Debug logging
    error_log("Received input: " . print_r($input, true));

    // Validate input
    if (!isset($input['id']) || !isset($input['name']) || !isset($input['capacity']) || !isset($input['equipment'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "All fields (id, name, capacity, equipment) are required."
        ]);
        return;
    }

    // Ensure proper data types
    $id = intval($input['id']);
    $name = trim($input['name']);
    $capacity = intval($input['capacity']);
    $equipment = trim($input['equipment']);
    $image_url = isset($input['image_url']) ? trim($input['image_url']) : null;

    try {
        $db = db_connect();
        
        // Debug logging
        error_log("Executing update for room_id: " . $id);
        
        $stmt = $db->prepare("UPDATE Rooms SET room_name = ?, capacity = ?, equipment = ?, image_url = ? WHERE room_id = ?");
        $result = $stmt->execute([
            $name,
            $capacity,
            $equipment,
            $image_url,
            $id
        ]);

        // Debug logging
        error_log("Update result: " . ($result ? "true" : "false"));
        error_log("Rows affected: " . $stmt->rowCount());

        if (!$result) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to update room."
            ]);
            return;
        }

        if ($stmt->rowCount() === 0) {
            // Debug logging
            error_log("No rows affected. Current SQL: UPDATE Rooms SET room_name = '{$name}', capacity = {$capacity}, equipment = '{$equipment}', image_url = " . ($image_url ?? 'null') . " WHERE room_id = {$id}");
            
            // Check if room exists
            $checkStmt = $db->prepare("SELECT room_id FROM Rooms WHERE room_id = ?");
            $checkStmt->execute([$id]);
            
            if ($checkStmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Room not found."
                ]);
                return;
            }

            // Room exists but no changes were made
            echo json_encode([
                "success" => true,
                "message" => "No changes were necessary."
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Room updated successfully."
        ]);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
}
