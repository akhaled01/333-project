<?php
require_once '../db_connection.php';
session_start();

// Mark notifications as read when user visits room-explore
if (isset($_SESSION['user_id'])) {
    $conn = db_connect();
    $stmt = $conn->prepare("
        UPDATE Notifications 
        SET is_read = TRUE 
        WHERE user_id = ? AND is_read = FALSE
    ");
    $stmt->execute([$_SESSION['user_id']]);
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_comment') {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Please login to comment']);
            exit;
        }

        $roomId = $_POST['room_id'];
        $comment = trim($_POST['comment']);

        if (empty($comment)) {
            echo json_encode(['error' => 'Comment cannot be empty']);
            exit;
        }

        try {
            $conn = db_connect();
            $stmt = $conn->prepare("
                INSERT INTO Comments (user_id, room_id, comment, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$_SESSION['user_id'], $roomId, $comment]);

            // Return the new comment data
            echo json_encode([
                'success' => true,
                'comment' => $comment,
                'date' => date('Y-m-d H:i')
            ]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to add comment']);
            exit;
        }
    }
}

// Fetch all rooms from the database with their comments
$conn = db_connect();
$stmt = $conn->prepare("
    SELECT 
        r.*,
        JSON_ARRAYAGG(
            JSON_OBJECT(
                'comment_id', c.comment_id,
                'comment', c.comment,
                'admin_response', c.admin_response,
                'created_at', DATE_FORMAT(c.created_at, '%Y-%m-%d %H:%i'),
                'admin_id', c.admin_id
            )
        ) as comments_data
    FROM Rooms r
    LEFT JOIN Comments c ON r.room_id = c.room_id
    GROUP BY r.room_id
");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process the comments data
foreach ($rooms as &$room) {
    $commentsData = json_decode($room['comments_data'], true);
    $room['comment_count'] = 0;

    if (is_array($commentsData) && $commentsData[0]['comment_id'] !== null) {
        $room['comment_count'] = count($commentsData);
        usort($commentsData, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        $room['comments_data'] = $commentsData;
    } else {
        $room['comments_data'] = [];
    }
}
unset($room);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Explorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-zinc-900 text-white">
    <?php include '../components/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Available Rooms</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($rooms as $room): ?>
                <div class="bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
                    <?php if (!empty($room['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                            class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="w-full h-48 bg-zinc-700 flex items-center justify-center">
                            <i class="fas fa-door-open text-4xl text-zinc-500"></i>
                        </div>
                    <?php endif; ?>

                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">
                            <?php echo htmlspecialchars($room['room_name']); ?>
                        </h2>
                        <?php if (isset($room['description']) && !empty($room['description'])): ?>
                            <p class="text-zinc-400 mb-4">
                                <?php echo htmlspecialchars($room['description']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Room Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-zinc-300">
                                <i class="fas fa-users w-5 text-indigo-400"></i>
                                <span class="ml-2">Capacity: <?php echo htmlspecialchars($room['capacity']); ?> people</span>
                            </div>

                            <?php if (isset($room['equipment']) && !empty($room['equipment'])): ?>
                                <div class="flex items-start text-zinc-300">
                                    <i class="fas fa-tools w-5 text-indigo-400 mt-1"></i>
                                    <div class="ml-2">
                                        <span class="block mb-1">Equipment:</span>
                                        <ul class="list-disc list-inside text-sm text-zinc-400 space-y-1 ml-2">
                                            <?php
                                            $equipment = explode(',', $room['equipment']);
                                            foreach ($equipment as $item): ?>
                                                <li><?php echo htmlspecialchars(trim($item)); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex justify-between items-center">
                            <button onclick="openFeedbackDialog(<?php echo $room['room_id']; ?>)"
                                class="text-indigo-400 hover:text-indigo-300 flex items-center gap-2">
                                <i class="fas fa-comments"></i>
                                <span>Feedback (<?php echo $room['comment_count']; ?>)</span>
                            </button>
                            <a href="/booking/index.php"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Feedback Dialog -->
    <dialog id="feedback-dialog"
        class="bg-zinc-800 text-white rounded-lg shadow-xl p-0 w-full max-w-2xl mx-auto backdrop:bg-zinc-900/90">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold" id="dialog-room-name"></h3>
                <button onclick="closeFeedbackDialog()" class="text-zinc-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="feedback-content" class="space-y-4 max-h-[60vh] overflow-y-auto"></div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mt-6 pt-4 border-t border-zinc-700">
                    <form id="comment-form" class="space-y-3">
                        <textarea
                            id="comment-input"
                            class="w-full px-3 py-2 bg-zinc-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            rows="3"
                            placeholder="Share your feedback..."
                            required></textarea>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="mt-6 pt-4 border-t border-zinc-700 text-center">
                    <p class="text-zinc-400">Please <a href="/login.php" class="text-indigo-400 hover:text-indigo-300">login</a> to leave feedback</p>
                </div>
            <?php endif; ?>
        </div>
    </dialog>

    <script>
        const feedbackDialog = document.getElementById('feedback-dialog');
        const dialogRoomName = document.getElementById('dialog-room-name');
        const feedbackContent = document.getElementById('feedback-content');
        const commentForm = document.getElementById('comment-form');

        // Store room data for easy access
        const roomData = <?php echo json_encode($rooms); ?>;
        let currentRoomId = null;

        function openFeedbackDialog(roomId) {
            currentRoomId = roomId;
            const room = roomData.find(r => r.room_id === roomId);
            if (!room) return;

            dialogRoomName.textContent = room.room_name;
            feedbackContent.innerHTML = '';

            if (!room.comments_data || room.comments_data.length === 0) {
                feedbackContent.innerHTML = '<p class="text-zinc-400">No feedback yet.</p>';
            } else {
                room.comments_data.forEach(comment => {
                    addCommentToDialog(
                        comment.comment,
                        comment.created_at,
                        comment.admin_response,
                        comment.comment_id
                    );
                });
            }

            feedbackDialog.showModal();
        }

        function addCommentToDialog(comment, date, adminResponse = null, commentId = null) {
            const commentHtml = `
                <div class="bg-zinc-700/50 rounded-lg p-4 space-y-3" ${commentId ? `data-comment-id="${commentId}"` : ''}>
                    <div class="flex justify-between items-start">
                        <div class="space-y-1">
                            <p class="text-zinc-200">${comment}</p>
                            <p class="text-xs text-zinc-500">${date}</p>
                        </div>
                    </div>
                    ${adminResponse ? `
                        <div class="ml-4 mt-2 border-l-2 border-indigo-500 pl-4">
                            <p class="text-indigo-400 text-sm">Admin Response:</p>
                            <p class="text-zinc-300">${adminResponse}</p>
                        </div>
                    ` : ''}
                </div>
            `;

            if (feedbackContent.innerHTML === '<p class="text-zinc-400">No feedback yet.</p>') {
                feedbackContent.innerHTML = '';
            }
            feedbackContent.insertAdjacentHTML('beforeend', commentHtml);
        }

        function closeFeedbackDialog() {
            feedbackDialog.close();
            currentRoomId = null;
            if (commentForm) {
                commentForm.reset();
            }
        }

        // Handle comment submission
        if (commentForm) {
            commentForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const commentInput = document.getElementById('comment-input');
                const comment = commentInput.value.trim();

                if (!comment) return;

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=add_comment&room_id=${currentRoomId}&comment=${encodeURIComponent(comment)}`
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Add the new comment to the dialog
                        addCommentToDialog(comment, data.date);

                        // Update the comment count in the room card
                        const commentCountEl = document.querySelector(`button[onclick="openFeedbackDialog(${currentRoomId})"] span`);
                        const currentCount = parseInt(commentCountEl.textContent.match(/\d+/)[0]);
                        commentCountEl.textContent = `Feedback (${currentCount + 1})`;

                        // Clear the form
                        commentForm.reset();

                        // If this was the first comment, remove the "No feedback yet" message
                        if (feedbackContent.innerHTML === '<p class="text-zinc-400">No feedback yet.</p>') {
                            feedbackContent.innerHTML = '';
                        }
                    } else {
                        alert(data.error || 'Failed to add comment');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to submit comment');
                }
            });
        }

        // Close dialog when clicking outside
        feedbackDialog.addEventListener('click', (e) => {
            if (e.target === feedbackDialog) {
                closeFeedbackDialog();
            }
        });
    </script>
</body>

</html>