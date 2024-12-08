<?php
session_start();
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $error = '';

    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        try {
            $conn = db_connect();
            
            // Debug: Log the query and email
            error_log('Login attempt for email: ' . $email);
            
            $stmt = $conn->prepare("SELECT user_id, name, password, role, avatar_url FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug: Log user data
            error_log('User data from DB: ' . print_r($user, true));

            if ($user && password_verify($password, $user['password'])) {
                // Clear any existing session data
                session_unset();
                
                // Set new session data
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];  
                $_SESSION['avatar_url'] = $user['avatar_url'] ?? '../uploads/avatars/default.png';
                
                // Debug: Log the session data after setting
                error_log('Session data after login: ' . print_r($_SESSION, true));

                if ($user['role'] === 'admin') {
                    header("Location: ../admin/");
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } catch(PDOException $e) {
            error_log('Database error during login: ' . $e->getMessage());
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Check for registration success message
$registration_success = isset($_GET['registered']) && $_GET['registered'] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Room Booking System</title>
    <link rel="stylesheet" href="../output.css">
</head>
<body class="bg-zinc-900 min-h-screen">
    <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="bg-zinc-800 px-8 py-10 shadow-md rounded-lg border border-zinc-700">
                <div class="sm:mx-auto sm:w-full sm:max-w-md">
                    <h2 class="text-center text-3xl font-bold tracking-tight text-zinc-100">
                        Sign in to your account
                    </h2>
                    <p class="mt-2 text-center text-sm text-zinc-400">
                        Don't have an account?
                        <a href="signup.php" class="font-medium text-blue-400 hover:text-blue-300">Sign up</a>
                    </p>
                </div>

                <?php if ($registration_success): ?>
                    <div class="mt-6 rounded-md bg-green-900 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-200">Registration successful!</h3>
                                <p class="mt-2 text-sm text-green-300">Please login with your credentials.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="mt-6 rounded-md bg-red-900 p-4">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-200"><?php echo htmlspecialchars($error); ?></h3>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="mt-8 space-y-6" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-300">Email address</label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" required
                                    class="block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3"
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-zinc-300">Password</label>
                            <div class="mt-1">
                                <input id="password" name="password" type="password" required
                                    class="block w-full rounded-md bg-zinc-700 border-zinc-600 text-zinc-100 placeholder-zinc-400 focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox"
                                class="h-4 w-4 rounded bg-zinc-700 border-zinc-600 text-blue-600 focus:ring-blue-500">
                            <label for="remember_me" class="ml-2 block text-sm text-zinc-300">Remember me</label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>