// admin_comments.php
require '../db_connection.php';
$pdo = db_connect();

$stmt = $pdo->prepare("SELECT c.comment_id, c.comment, c.status, u.username, r.room_name 
                       FROM comments c
                       JOIN users u ON c.user_id = u.user_id
                       JOIN rooms r ON c.room_id = r.room_id
                       WHERE c.status = 'pending'"); // Fetching only pending comments
$stmt->execute();

$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Room</th>
            <th>Comment</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?= $comment['username'] ?></td>
                <td><?= $comment['room_name'] ?></td>
                <td><?= $comment['comment'] ?></td>
                <td><?= ucfirst($comment['status']) ?></td>
                <td>
                    <form action="respond_to_comment.php" method="POST">
                        <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                        <textarea name="response" placeholder="Write a response"></textarea>
                        <button type="submit">Respond</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
