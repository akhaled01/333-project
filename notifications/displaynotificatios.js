// Function to display notifications
function displayNotifications(notifications) {
    const notificationArea = document.getElementById('notificationArea');
    notificationArea.innerHTML = ''; // Clear current notifications

    notifications.forEach(notification => {
        const notificationDiv = document.createElement('div');
        notificationDiv.classList.add('notification');
        notificationDiv.innerHTML = `
            <p>${notification.message}</p>
            <span>${notification.created_at}</span>
        `;
        notificationArea.appendChild(notificationDiv);
    });
}

// Poll for notifications every 10 seconds
setInterval(async () => {
    const response = await fetch('./get_notifications.php');
    const data = await response.json();
    
    if (data.error) {
        console.error(data.error);
        return;
    }

    if (data.length > 0) {
        displayNotifications(data);
    }
}, 10000); // Poll every 10 seconds
