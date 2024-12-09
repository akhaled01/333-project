// Function to load all comments
async function loadComments() {
    try {
        const response = await fetch('./api/comments.php');
        const comments = await response.json();
        displayComments(comments);
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

// Function to display comments in the UI
function displayComments(comments) {
    const commentsList = document.getElementById('comments-list');
    commentsList.innerHTML = '';

    comments.forEach(comment => {
        const commentElement = createCommentElement(comment);
        commentsList.appendChild(commentElement);
    });
}

// Function to create a comment element
function createCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'bg-zinc-700 rounded-lg p-4 space-y-2';
    
    const header = document.createElement('div');
    header.className = 'flex justify-between items-start';
    header.innerHTML = `
        <div>
            <h3 class="text-sm font-medium text-zinc-100">${escapeHtml(comment.user_name)}</h3>
            <p class="text-xs text-zinc-400">Room: ${escapeHtml(comment.room_name)}</p>
            <p class="text-xs text-zinc-400">${new Date(comment.created_at).toLocaleString()}</p>
        </div>
        <button class="reply-button text-sm text-blue-400 hover:text-blue-500" data-comment-id="${comment.comment_id}">
            Reply
        </button>
    `;

    const content = document.createElement('div');
    content.className = 'text-sm text-zinc-200';
    content.textContent = comment.comment;

    div.appendChild(header);
    div.appendChild(content);

    if (comment.admin_response) {
        const replySection = document.createElement('div');
        replySection.className = 'mt-4 pl-4 border-l-2 border-indigo-500';
        replySection.innerHTML = `
            <div class="text-xs text-indigo-400">Admin Response:</div>
            <div class="text-sm text-zinc-200 mt-1">${escapeHtml(comment.admin_response)}</div>
        `;
        div.appendChild(replySection);
    }

    return div;
}

// Function to handle reply submission
async function submitReply(commentId, replyText) {
    try {
        const response = await fetch('./api/reply.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_id: commentId,
                reply: replyText
            })
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        if (result.success) {
            // Reload comments to show the new reply
            loadComments();
            return true;
        }
        return false;
    } catch (error) {
        console.error('Error submitting reply:', error);
        return false;
    }
}

// Helper function to escape HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Load comments when page loads
    loadComments();

    // Handle reply button clicks
    document.getElementById('comments-list').addEventListener('click', (e) => {
        if (e.target.classList.contains('reply-button')) {
            const commentId = e.target.dataset.commentId;
            const dialog = document.getElementById('reply-comment-dialog');
            document.getElementById('comment-id').value = commentId;
            dialog.open();
        }
    });

    // Handle reply form submission
    document.getElementById('reply-comment-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const commentId = document.getElementById('comment-id').value;
        const replyText = document.getElementById('reply-text').value;
        
        if (replyText.trim()) {
            const success = await submitReply(commentId, replyText);
            if (success) {
                document.getElementById('reply-comment-dialog').close();
                document.getElementById('reply-comment-form').reset();
            }
        }
    });
});
