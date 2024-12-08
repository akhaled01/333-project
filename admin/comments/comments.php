<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Comments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        async function fetchComments() {
            try {
                const response = await fetch('../api/get_comments_admin.php');
                const data = await response.json();

                if (data.success) {
                    const commentContainer = document.getElementById('comments');
                    commentContainer.innerHTML = '';

                    data.comments.forEach(comment => {
                        const commentCard = document.createElement('div');
                        commentCard.classList.add('card', 'mb-3');

                        commentCard.innerHTML = `
                            <div class="card-body">
                                <h5 class="card-title">${comment.room_name}</h5>
                                <p class="card-text"><strong>User:</strong> ${comment.username}</p>
                                <p class="card-text">${comment.comment_text}</p>
                                <p class="card-text text-muted"><small>${comment.created_at}</small></p>
                                <p class="card-text"><strong>Admin Response:</strong> ${comment.admin_response ? comment.admin_response : 'No response yet'}</p>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Write a response..." id="response-${comment.id}">
                                    <button class="btn btn-primary" onclick="submitResponse(${comment.id})">Submit</button>
                                </div>
                            </div>
                        `;

                        commentContainer.appendChild(commentCard);
                    });
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error fetching comments:', error);
            }
        }

        async function submitResponse(commentId) {
            const responseInput = document.getElementById(`response-${commentId}`);
            const responseText = responseInput.value.trim();

            if (!responseText) {
                alert('Please write a response before submitting.');
                return;
            }

            try {
                const response = await fetch('../api/respond_to_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ comment_id: commentId, response: responseText })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Response submitted successfully.');
                    fetchComments(); // Refresh comments
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error submitting response:', error);
            }
        }

        window.onload = fetchComments;
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1>Admin - Comments</h1>
        <div id="comments"></div>
    </div>
</body>
</html>
