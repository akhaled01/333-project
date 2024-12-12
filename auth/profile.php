<?php
session_start();
require_once '../db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$success_message = '';
$error = '';

try {
    $conn = db_connect();

    // Fetch user data including avatar_url
    $stmt = $conn->prepare("SELECT name, email, avatar_url FROM Users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user_data) {
        $_SESSION['user_name'] = $user_data['name'];
        $_SESSION['avatar_url'] = $user_data['avatar_url'];
    }

    // Handle form submission for profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $avatar_url = $_POST['avatar_url'] ?? $user_data['avatar_url'];

        if (empty($name) || empty($email)) {
            $error = "Name and email are required";
        } else {
            // First, verify current password if trying to change password
            if (!empty($current_password)) {
                $stmt = $conn->prepare("SELECT password FROM Users WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!password_verify($current_password, $user['password'])) {
                    $error = "Current password is incorrect";
                } elseif ($new_password !== $confirm_password) {
                    $error = "New passwords do not match";
                } elseif (strlen($new_password) < 6) {
                    $error = "New password must be at least 6 characters long";
                }
            }

            if (empty($error)) {
                if (!empty($new_password)) {
                    // Update name, email, and password
                    $stmt = $conn->prepare("UPDATE Users SET name = ?, email = ?, password = ?, avatar_url = ? WHERE user_id = ?");
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt->execute([$name, $email, $hashed_password, $avatar_url, $_SESSION['user_id']]);
                } else {
                    // Update only name and email
                    $stmt = $conn->prepare("UPDATE Users SET name = ?, email = ?, avatar_url = ? WHERE user_id = ?");
                    $stmt->execute([$name, $email, $avatar_url, $_SESSION['user_id']]);
                }

                $_SESSION['user_name'] = $name;
                $_SESSION['avatar_url'] = $avatar_url;
                $success_message = "Profile updated successfully!";
            }
        }
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Study Room Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script type="module">
        import {
            previewImage,
            uploadProfilePicture
        } from './js/profileUpload.js';

        window.handleImageUpload = async function(input) {
            if (input.files && input.files[0]) {
                try {
                    // Preview the image
                    previewImage(input);

                    // Upload to Supabase
                    const publicUrl = await uploadProfilePicture(input.files[0]);

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

<body class="bg-zinc-900 text-zinc-100">
    <!-- Navigation -->
    <?php include '../components/navbar.php'; ?>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <?php if (!empty($success_message)): ?>
                <div class="mb-4 rounded-md bg-green-900 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-200"><?php echo htmlspecialchars($success_message); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-4 rounded-md bg-red-900 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-200"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden border border-zinc-700">
                <div class="px-8 py-6 border-b border-zinc-700">
                    <h2 class="text-2xl font-bold text-zinc-100">Profile Settings</h2>
                </div>

                <form method="POST" class="px-8 py-6 space-y-6">
                    <input type="hidden" id="avatar_url" name="avatar_url" value="<?php echo htmlspecialchars($user_data['avatar_url'] ?? ''); ?>">

                    <div class="space-y-4">
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-zinc-300">Profile Picture</label>
                            <div class="mt-1 flex items-center">
                                <div class="relative">
                                    <img id="preview"
                                        src="<?php echo !empty($user_data['avatar_url']) ? htmlspecialchars($user_data['avatar_url']) : '/uploads/avatars/default.png'; ?>"
                                        alt="Profile preview"
                                        class="h-24 w-24 rounded-full object-cover border-2 border-zinc-600">
                                    <input type="file"
                                        id="avatar"
                                        accept="image/*"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        onchange="handleImageUpload(this)">
                                </div>
                                <span class="ml-4 text-sm text-zinc-400">Click to change profile picture</span>
                            </div>
                            <div id="upload-status"></div>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-zinc-300">Full Name</label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3"
                                value="<?php echo htmlspecialchars($user_data['name']); ?>">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-300">Email address</label>
                            <input type="email" name="email" id="email" required
                                class="mt-1 block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3"
                                value="<?php echo htmlspecialchars($user_data['email']); ?>">
                        </div>

                        <div class="pt-4 border-t border-zinc-700">
                            <h3 class="text-lg font-medium text-zinc-100 mb-4">Change Password</h3>

                            <div class="space-y-4">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-zinc-300">Current Password</label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="mt-1 block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3">
                                </div>

                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-zinc-300">New Password</label>
                                    <input type="password" name="new_password" id="new_password"
                                        class="mt-1 block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3">
                                </div>

                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-zinc-300">Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                        class="mt-1 block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit"
                            class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-800">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>