<?php

// Check if the user is logged in, if not then redirect him to login page
/*if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}*/

/*// Get room is an endpoint for the js for the booking functionality here we just need to get the rooms
ob_start(); // Start output buffering

require 'booking/get_rooms.php';

$rooms = json_decode(getRooms($pdo), true);
ob_end_clean(); // End buffering and discard output (no echo will be shown)*/

require 'db_connection.php';

$pdo = db_connect();

function FetchRooms($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM Rooms");
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no rooms found, return error message
    if (empty($result)){
        //http_response_code(404);
        return (["error"=> true, "message" => "No rooms found"]);
    }

    // Return rooms
    return (["error"=> false, "rooms" => $result]);
}

$rooms = FetchRooms($pdo);

?>

<style>
        body {
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ccc;
        }

        .room-card {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .room-card img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .room-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
        }

        .details-text {
            max-width: 60%;
        }

        .details-photo {
            max-width: 35%;
            border: 2px solid rgb(144, 133, 133);
            text-align: center;
            padding: 10px;
        }

        .details-photo img {
            width: 100%;
            height: auto;
        }

        .btn {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .room-details {
                flex-direction: column;
                text-align: center;
            }

            .details-text {
                max-width: 100%;
            }

            .details-photo {
                max-width: 100%;
                margin-top: 20px;
            }
        }
    </style>

    <div class="container mt-4">
        <h2 class="py-5 text-xl font-semibold text-gray-800">Room Browsing</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <!-- Use php to display the rooms-->
            <?php
                if ($rooms["error"]) {
                    echo "<p>No rooms found</p>";
                } else {
                    $roomsArray = $rooms["rooms"];
                    foreach ($roomsArray as $room) {
                        echo "
                            <div class='col'>
                                <div class='room-card'>
                                    <img src='class.jpg' alt='Room photo'>
                                    <h5 class='mt-2'>{$room['room_name']}</h5>
                                    <button class='btn btn-outline-primary' onclick='viewRoomDetails(\"{$room['room_name']}\", \"{$room['capacity']}\", \"{$room['equipment']}\", \"{$room['time_slots']}\", \"room1.jpg\")'>View Details</button>
                                </div>
                            </div>
                        ";
                    }
                }
            ?>
        </div>

        <div id="room-details" class="mt-5" style="display: none;">
            <h3>Room Details</h3>
            <div class="room-details">
                <div class="details-text">
                    <p><strong>Room Name:</strong> <span id="room-name"></span></p>
                    <p><strong>Capacity:</strong> <span id="room-capacity"></span></p>
                    <p><strong>Equipments:</strong> <span id="room-equipment"></span></p>
                    <p><strong>Available Time Slots:</strong> <span id="room-time"></span></p>
                    <p>
                        <label for="booking-date">Date:</label>
                        <input type="date" id="booking-date">
                    </p>
                    <p>
                        <label for="booking-time">Time:</label>
                        <input type="time" id="booking-time">
                    </p>
                </div>
                <div class="details-photo">
                    <img id="room-photo" src="" alt="Room photo">
                </div>
            </div>
            <button class="btn btn-success mt-3">Book Room</button>
        </div>
    </div>

    <script>
        function viewRoomDetails(name, capacity, equipment, time, photo) {
            document.getElementById('room-name').innerText = name;
            document.getElementById('room-capacity').innerText = capacity;
            document.getElementById('room-equipment').innerText = equipment;
            document.getElementById('room-time').innerText = time;
            document.getElementById('room-photo').src = photo;
            document.getElementById('room-details').style.display = 'block';
        }
    </script>