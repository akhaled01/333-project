<?php

include "../../../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $name = $input["name"] ?? null;
    $capacity = $input["capacity"] ?? null;
    $equipment = $input["equipment"] ?? null;
    $image_url = $input["image_url"] ?? null;

    if (empty($name) || empty($capacity) || empty($equipment)) {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all fields."
        ]);
        exit;
    }

    try {
        $db = db_connect();
        $query = "INSERT INTO Rooms (room_name, capacity, equipment, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$name, $capacity, $equipment, $image_url]);

        echo json_encode([
            "success" => true,
            "message" => "Room created successfully!"
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
