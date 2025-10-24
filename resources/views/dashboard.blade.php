@extends('layouts.app')

@section('title', 'Dashboard - MIE Admin')
@section('page-title', 'Dashboard')

@section('content')
<!-- Widgets Section -->
        <div class="widgets-container">
            <div class="widget">
                <div class="widget-icon total-users">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M17 20H22V18C22 16.3431 20.6569 15 19 15C18.0444 15 17.1931 15.4468 16.6438 16.1429M17 20H7M17 20V18C17 17.3438 16.8736 16.717 16.6438 16.1429M7 20H2V18C2 16.3431 3.34315 15 5 15C5.95561 15 6.80686 15.4468 7.35625 16.1429M7 20V18C7 17.3438 7.12642 16.717 7.35625 16.1429M7.35625 16.1429C8.0935 14.301 9.89482 13 12 13C14.1052 13 15.9065 14.301 16.6438 16.1429" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="widget-content">
                    <h3>Total Users</h3>
                    <p class="widget-number">1,234</p>
                    <p class="widget-trend positive">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        +12.5% this month
                    </p>
                </div>
            </div>
            <div class="widget">
                <div class="widget-icon invited-users">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="widget-content">
                    <h3>Invited Users</h3>
                    <p class="widget-number">89</p>
                    <p class="widget-trend neutral">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Same as last month
                    </p>
                </div>
            </div>
            <div class="widget">
                <div class="widget-icon total-snaps">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="widget-content">
                    <h3>Total Snaps (30 days)</h3>
                    <p class="widget-number">45,789</p>
                    <p class="widget-trend positive">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        +23.1% this month
                    </p>
                </div>
            </div>
        </div>
        <!-- Usage Graph Section -->
        <div class="card graph-card">
            <div class="card-header">
                <h2>Snap Usage Trends</h2>
                <div class="graph-controls">
                    <select id="timeRange" class="select-control">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
            </div>
            <div class="graph-container">
                <canvas id="usageGraph"></canvas>
            </div>
        </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sample data for the graph
    const dates = Array.from({length: 30}, (_, i) => {
        const date = new Date();
        date.setDate(date.getDate() - (29 - i));
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const usageData = Array.from({length: 30}, () => 
        Math.floor(Math.random() * (2000 - 800 + 1)) + 800
    );
    // Initialize the graph
    const ctx = document.getElementById('usageGraph').getContext('2d');
    const usageGraph = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Daily Snap Usage',
                data: usageData,
                borderColor: '#018b8d',
                backgroundColor: 'rgba(1, 139, 141, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    // Handle time range changes
    document.getElementById('timeRange').addEventListener('change', (e) => {
        const days = parseInt(e.target.value);
        const newDates = Array.from({length: days}, (_, i) => {
            const date = new Date();
            date.setDate(date.getDate() - (days - 1 - i));
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const newData = Array.from({length: days}, () => 
            Math.floor(Math.random() * (2000 - 800 + 1)) + 800
        );
        usageGraph.data.labels = newDates;
        usageGraph.data.datasets[0].data = newData;
        usageGraph.update();
    });
</script>
@endpush
