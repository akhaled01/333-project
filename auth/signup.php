<?php
session_start();
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $avatar_url = $_POST['avatar_url'] ?? null;
    $error = '';

    // Validate email format
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9-]+\.)*uob\.edu\.bh$/', $email)) {
        $error = "Email must be a valid @uob.edu.bh address";
    } elseif (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        try {
            $conn = db_connect();
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email already registered";
            } else {
                // Insert new user with role 'user'
                $stmt = $conn->prepare("INSERT INTO Users (name, email, password, role, avatar_url) VALUES (?, ?, ?, 'user', ?)");
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute([$name, $email, $hashed_password, $avatar_url]);
                
                // Redirect to login page with success message
                header("Location: login.php?registered=1");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Study Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import { previewImage, uploadProfilePicture } from './js/profileUpload.js';

        window.handleImageUpload = async function(input) {
            if (input.files && input.files[0]) {
                try {
                    // Preview the image
                    previewImage(input);
                    
                    // Upload to Supabase
                    const publicv = await uploadProfilePicture(input.files[0]);
                    
                    // Set the URL in the hidden input
                    document.getElementById('avatar_url').value = publicUrl;
                    
                    // Show success message
                    const uploadStatus = document.getElementById('upload-status');
                    uploadStatus.textContent = 'Image uploaded successfully!';
                    uploadStatus.className = 'mt-2 text-sm text-green-500';
                } catch (error) {
                    // Show error message
                    const uploadStatus = document.getElementById('upload-status');
                    uploadStatus.textContent = 'Failed to upload image: ' + error.message;
                    uploadStatus.className = 'mt-2 text-sm text-red-500';
                }
            }
        };
    </script>
</head>
<body class="bg-zinc-900 min-h-screen">
    <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-4xl">
            <div class="bg-zinc-800 shadow-md rounded-lg border border-zinc-700">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <!-- Left Column - Profile Picture -->
                    <div class="p-8 border-b md:border-b-0 md:border-r border-zinc-700">
                        <div class="text-center">
                            <h2 class="text-2xl font-bold tracking-tight text-white mb-6">Your Profile Picture</h2>
                            <div class="flex flex-col items-center space-y-4">
                                <div class="relative group">
                                    <img id="preview" 
                                         src="/uploads/avatars/default.png" 
                                         alt="Profile preview" 
                                         class="h-40 w-40 rounded-full object-cover border-2 border-zinc-600 group-hover:border-indigo-500 transition-all duration-300">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <div class="bg-black bg-opacity-50 rounded-full h-40 w-40 absolute"></div>
                                        <span class="relative text-white text-sm font-medium">Change Picture</span>
                                    </div>
                                    <input type="file" 
                                           id="avatar" 
                                           accept="image/*" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                           onchange="handleImageUpload(this)">
                                </div>
                                <div id="upload-status" class="text-sm"></div>
                                <p class="text-sm text-zinc-400 mt-2">Click to upload your profile picture</p>
                                <p class="text-xs text-zinc-500">Supported formats: JPEG, PNG, GIF (Max 5MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Sign Up Form -->
                    <div class="p-8">
                        
                        <div class="sm:mx-auto sm:w-full sm:max-w-md">
                            <h2 class="text-2xl font-bold tracking-tight text-white mb-6">Create your account</h2>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                            </div>
                        <?php endif; ?>

                        <form class="space-y-6" method="POST">
                            <input type="hidden" id="avatar_url" name="avatar_url">
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-white">Full Name</label>
                                <input id="name" name="name" type="text" required 
                                       value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                       class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-white">Email address</label>
                                <input id="email" name="email" type="email" required 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3"
                                       placeholder="@uob.edu.bh">
                                <p class="mt-1 text-sm text-zinc-400">Must be a valid @uob.edu.bh email address</p>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-white">Password</label>
                                <input id="password" name="password" type="password" required
                                       class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3">
                                <p class="mt-1 text-sm text-zinc-400">Must be at least 6 characters</p>
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-white">Confirm Password</label>
                                <input id="confirm_password" name="confirm_password" type="password" required
                                       class="mt-1 block w-full rounded-md border-zinc-600 bg-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3">
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    Create Account
                                </button>
                            </div>

                            <div class="text-sm text-center">
                                <a href="login.php" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors duration-200">
                                    Already have an account? Sign in
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
