// Fetch data and create the chart using Chart.js
async function fetchRoomsChartData() {
    try {
        const response = await fetch('get_top_booked_rooms.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Create the chart using Chart.js
        const ctx = document.getElementById('RoomsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar', // Chart type
            data: {
                labels: data.rooms,
                datasets: [{
                    label: 'Bookings',
                    data: data.bookings,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    labels: {
                        font: {
                            size: 18,
                        },
                        color: 'white'
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14,
                            },
                            color: 'white'
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 14,
                            },
                            color: 'white'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error fetching or creating chart:', error);
    }
}

fetchRoomsChartData();

async function fetchDatesChartData() {
    try {
        const response = await fetch('get_booking_dates.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const ctx = document.getElementById('DatesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Bookings',
                    data: data.bookings,
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: 'rgba(54, 162, 235, 1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                    },
                    tooltip: {
                        enabled: true,
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                        },
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Bookings',
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error fetching or creating chart:', error);
    }
}

fetchDatesChartData();