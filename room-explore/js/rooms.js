document.addEventListener('DOMContentLoaded', function() {
    // Add any interactive features for room browsing here
    const searchInput = document.getElementById('roomSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const roomCards = document.querySelectorAll('.card');
            
            roomCards.forEach(card => {
                const roomName = card.querySelector('.card-title').textContent.toLowerCase();
                const equipment = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
                
                if (roomName.includes(searchTerm) || equipment.includes(searchTerm)) {
                    card.closest('.col-md-4').style.display = '';
                } else {
                    card.closest('.col-md-4').style.display = 'none';
                }
            });
        });
    }

    // Function to toggle older comments visibility
    function toggleOlderComments(button) {
        const roomId = button.dataset.roomId;
        const olderCommentsDiv = document.getElementById(`older-comments-${roomId}`);
        const isHidden = olderCommentsDiv.classList.contains('hidden');
        
        if (isHidden) {
            olderCommentsDiv.classList.remove('hidden');
            button.textContent = button.textContent.replace('View', 'Hide');
        } else {
            olderCommentsDiv.classList.add('hidden');
            button.textContent = button.textContent.replace('Hide', 'View');
        }
    }

    // Handle comment form submissions
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const roomId = this.dataset.roomId;
            const textarea = this.querySelector('textarea');
            const comment = textarea.value.trim();
            
            if (!comment) {
                alert('Please enter a comment');
                return;
            }

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'add_comment',
                        room_id: roomId,
                        comment: comment
                    })
                });

                const data = await response.json();
                
                if (data.error) {
                    alert(data.error);
                    return;
                }

                if (data.success) {
                    // Clear the textarea
                    textarea.value = '';
                    
                    // Reload the page to show the new comment
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to submit comment. Please try again.');
            }
        });
    });

    // Add event listener to toggle older comments buttons
    document.querySelectorAll('.toggle-older-comments').forEach(button => {
        button.addEventListener('click', function() {
            toggleOlderComments(button);
        });
    });
});
