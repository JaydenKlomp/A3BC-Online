<?php
/** @var int $totalComments */
/** @var int $totalPosts */
/** @var int $totalDownvotes */
/** @var int $totalUpvotes */
?>

<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="dashboard-container">
    <h2>📊 Dashboard</h2>

    <div class="dashboard-stats">
        <div class="stat-box clickable" data-type="posts">📝 <strong><?= $totalPosts ?></strong> Posts</div>
        <div class="stat-box">💬 <strong><?= $totalComments ?></strong> Comments</div>
        <div class="stat-box">⬆ <strong><?= $totalUpvotes ?></strong> Upvotes</div>
        <div class="stat-box">⬇ <strong><?= $totalDownvotes ?></strong> Downvotes</div>
    </div>

    <div class="chart-container">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="daily">Daily</button>
            <button class="filter-btn" data-filter="weekly">Weekly</button>
            <button class="filter-btn" data-filter="monthly">Monthly</button>
        </div>
        <canvas id="postsChart"></canvas>
    </div>
</div>

<script>
    let chartInstance;
    let currentFilter = "daily";

    function fetchChartData(filter) {
        fetch(`<?= site_url('dashboard/getChartData?filter=') ?>${filter}`)
            .then(response => response.json())
            .then(data => {
                updateChart(data.posts);
            });
    }

    function updateChart(data) {
        const ctx = document.getElementById('postsChart').getContext('2d');

        if (chartInstance) {
            chartInstance.destroy();
        }

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(entry => entry.period),
                datasets: [{
                    label: 'Posts',
                    data: data.map(entry => entry.total),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Posts' }, beginAtZero: true }
                }
            }
        });
    }

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            fetchChartData(currentFilter);
        });
    });

    fetchChartData(currentFilter);
</script>
<?= $this->endSection() ?>
