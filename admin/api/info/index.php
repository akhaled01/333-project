<?php 

include "../../../db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $db = db_connect();

    $query = "SELECT * FROM Rooms";
    $stmt = $db->query($query);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rooms);
}


