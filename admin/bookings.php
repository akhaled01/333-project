<?php
require_once 'auth_middleware.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - Admin Panel</title>
    <link rel="stylesheet" href="../output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="./js/components/Bookings.js" type="module" defer></script>
</head>

<body class="bg-zinc-900 antialiased text-zinc-100">
    <div class="flex min-h-screen">
        <div class="flex flex-1 flex-col">
            <?php include '../components/navbar.php'; ?>
            <main class="flex-1 overflow-y-auto bg-zinc-900 p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-zinc-800 rounded-lg shadow-sm border border-zinc-700 overflow-hidden">
                        <div class="flex justify-between items-center p-6 border-b border-zinc-700">
                            <h2 class="text-xl font-bold text-zinc-100">Current Bookings</h2>
                        </div>
                        
                        <bookings-table class="w-full"></bookings-table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
