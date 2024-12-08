<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Comments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .response-textarea {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Comments</h2>
        <div id="comments-container" class="mt-3">
            <!-- Comments will be loaded dynamically here -->
        </div>
    </div>

    <script>
        // Fetch comments for admin
        function fetchComments() {
            fetch('/api/get_comments_admin.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const commentsContainer = document.getElementById('comments-container');
                        commentsContainer.innerHTML = '';

                        data.comments.forEach(comment => {
                            const commentDiv = document.createElement('div');
                            commentDiv.className = 'card mb-3';
                            commentDiv.innerHTML = `
                                <div class="card-body">
                                    <h5 class="card-title">${comment.room_name} - Comment by ${comment.username}</h5>
                                    <p class="card-text">${comment.comment_text}</p>
                                    <p class="card-text text-muted"><small>Submitted on: ${comment.created_at}</small></p>
                                    ${comment.admin_response ? `<p class="card-text"><strong>Admin Response:</strong> ${comment.admin_response}</p>` : ''}
                                    <div class="response-textarea">
                                        <textarea class="form-control" rows="2" placeholder="Write a response..." data-id="${comment.id}">${comment.admin_response || ''}</textarea>
                                        <button class="btn btn-primary" onclick="submitResponse(${comment.id})">Respond</button>
                                    </div>
                                </div>
                            `;
                            commentsContainer.appendChild(commentDiv);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Submit admin response
        function submitResponse(commentId) {
            const textarea = document.querySelector(`textarea[data-id="${commentId}"]`);
            const responseText = textarea.value.trim();

            if (responseText === '') {
                alert('Response cannot be empty!');
                return;
            }

            fetch('/api/respond_to_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    comment_id: commentId,
                    response_text: responseText,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Response submitted successfully!');
                        fetchComments(); // Reload comments
                    } else {
                        alert('Failed to submit response: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Load comments on page load
        fetchComments();
    </script>
</body>
</html>
