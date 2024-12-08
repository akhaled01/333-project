document.getElementById('commentForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const commentText = document.getElementById('commentText').value;
    const roomId = /* Get the room ID from the page or context */;

    try {
        const response = await fetch('./submit_comment.php', {
            method: 'POST',
            body: JSON.stringify({ room_id: roomId, comment: commentText }),
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await response.json();

        if (data.success) {
            alert('Comment submitted successfully!');
        } else {
            alert('Error submitting comment');
        }
    } catch (error) {
        console.error('Error submitting comment:', error);
    }
});
