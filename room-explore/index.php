<?php
require_once '../db_connection.php';
session_start();

// Fetch all rooms from the database
$conn = db_connect();
$stmt = $conn->prepare("SELECT * FROM Rooms");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../output.css">
    <title>Browse Rooms</title>
</head>
<body class="bg-zinc-900 text-zinc-100">
    <?php include '../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-zinc-100 mb-6">Available Rooms</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($rooms as $room): ?>
                <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden border border-zinc-700 transition-transform duration-300 hover:scale-105">
                    <?php if ($room['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" 
                             class="w-full h-48 object-cover" 
                             alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                    <?php else: ?>
                        <img src="../assets/default-room.jpg" 
                             class="w-full h-48 object-cover" 
                             alt="Default Room Image">
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-zinc-100 mb-2">
                            <?php echo htmlspecialchars($room['room_name']); ?>
                        </h3>
                        
                        <div class="flex items-center text-zinc-400 mb-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Capacity: <?php echo htmlspecialchars($room['capacity']); ?> people</span>
                        </div>
                        
                        <?php if ($room['equipment']): ?>
                            <div class="flex items-center text-zinc-400 mb-4">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span><?php echo htmlspecialchars($room['equipment']); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <a href="/booking/" 
                           class="inline-block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-300">
                            Book Room
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="js/rooms.js"></script>
</body>
</html>
