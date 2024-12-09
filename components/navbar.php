<?php
// Session is already started in the main page
require_once __DIR__ . '/../db_connection.php';

// Fetch fresh avatar URL if user is logged in
if (isset($_SESSION['user_id']) && !isset($_SESSION['avatar_url'])) {
    
    try {
        $conn = db_connect();
        $stmt = $conn->prepare("SELECT avatar_url FROM Users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['avatar_url'] = $result['avatar_url'] ?? '../uploads/avatars/default.png';
    } catch (PDOException $e) {
        error_log("Error fetching avatar URL: " . $e->getMessage());
    }
}
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<script src="//unpkg.com/alpinejs" defer></script>

<nav class="bg-zinc-800 border-b border-zinc-700">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="../index.php" class="text-xl font-bold text-zinc-100">ReserveIt</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center space-x-3">
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="../admin/" class="text-indigo-400 hover:text-indigo-300 px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-shield-alt mr-2"></i>Admin Panel
                            </a>
                        <?php endif; ?>

                        <?php include __DIR__ . '/notifications.php'; ?>

                        <a href="../auth/profile.php" class="flex items-center group">
                            <img src="<?php echo !empty($_SESSION['avatar_url']) ? htmlspecialchars($_SESSION['avatar_url']) : '../uploads/avatars/default.png'; ?>"
                                alt="Profile"
                                class="h-8 w-8 rounded-full object-cover border border-zinc-600 group-hover:border-indigo-500 transition-colors">
                            <span class="ml-2 text-zinc-300 group-hover:text-zinc-100">
                                <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                            </span>
                        </a>
                        <a href="../index.php" class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                        <a href="../auth/logout.php" class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex items-center space-x-3">
                        <a href="../auth/login.php" class="text-zinc-300 hover:text-zinc-100 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <a href="../auth/signup.php" class="bg-indigo-600 text-white hover:bg-indigo-700 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-user-plus mr-2"></i>Sign Up
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>