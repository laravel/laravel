import Chart from 'chart.js/auto';

document.addEventListener("DOMContentLoaded", function () {
    const ctx1 = document.getElementById('hoursChart').getContext('2d');
    const ctx2 = document.getElementById('projectsChart').getContext('2d');

    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
            datasets: [{
                label: 'Timer pr. måned',
                data: [12, 19, 3, 5, 2, 3, 20, 30, 40, 10, 5, 12], // Udskift med reelle data fra backend
                borderColor: 'blue',
                tension: 0.4
            }]
        }
    });

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
            datasets: [{
                label: 'Aktive Projekter',
                data: [5, 10, 15, 7, 8, 12, 14, 16, 18, 20, 22, 25], // Udskift med reelle data
                borderColor: 'green',
                tension: 0.4
            }]
        }
    });
});

