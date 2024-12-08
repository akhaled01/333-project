<?php
// Admin comments management page

require '../db_connection.php';
$pdo = db_connect();

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("location: ../auth/login.php");
    exit;
}

// Fetch comments from the database
$stmt = $pdo->prepare("SELECT * FROM comments WHERE response IS NULL");
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-comments">
    <h1>Manage Comments</h1>
    <table>
        <tr>
            <th>User</th>
            <th>Comment</th>
            <th>Action</th>
        </tr>
        <?php foreach ($comments as $comment) { ?>
        <tr>
            <td><?php echo $comment['user_name']; ?></td>
            <td><?php echo $comment['comment']; ?></td>
            <td>
                <a href="respond_to_comment.php?comment_id=<?php echo $comment['comment_id']; ?>">Respond</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
