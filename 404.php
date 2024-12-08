<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="./output.css">
</head>
<body class="bg-zinc-900 text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <h1 class="text-6xl font-bold text-zinc-500 mb-4">404</h1>
        <div class="w-24 h-24 mx-auto mb-8">
            <svg class="w-full h-full text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 20h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-semibold mb-4">Page Not Found</h2>
        <p class="text-zinc-400 mb-8">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="/" class="inline-flex items-center px-6 py-3 text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
            Go Back Home
        </a>
    </div>
</body>
</html>
