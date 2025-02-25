<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="dashboard-container">
    <h2>📊 Admin Dashboard</h2>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#statistics-tab">📈 Statistics</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#management-tab">🛠 Management</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- 📊 Statistics Tab -->
        <div class="tab-pane fade show active" id="statistics-tab">
            <div class="dashboard-stats">
                <div class="stat-box">📝 <strong><?= $totalPosts ?></strong> Posts</div>
                <div class="stat-box">💬 <strong><?= $totalComments ?></strong> Comments</div>
                <div class="stat-box">⬆ <strong><?= $totalUpvotes ?></strong> Upvotes</div>
                <div class="stat-box">⬇ <strong><?= $totalDownvotes ?></strong> Downvotes</div>
                <div class="stat-box">👥 <strong><?= $totalUsers ?></strong> Users</div>
                <div class="stat-box">🌎 <strong><?= $totalCommunities ?></strong> Communities</div>
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

        <!-- 🛠 Management Tab -->
        <div class="tab-pane fade" id="management-tab">

            <!-- Manage Users -->
            <div class="management-section">
                <h3>Manage Users</h3>
                <div class="management-list">
                    <?php foreach ($users as $user): ?>
                        <div class="management-card">
                            <div class="management-info">
                                <span><strong><?= esc($user['username']) ?></strong></span>
                                <span class="management-meta">Role: <?= esc($user['role']) ?></span>
                            </div>
                            <button class="delete-btn delete-user" data-id="<?= $user['id'] ?>">Delete</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Manage Posts -->
            <div class="management-section">
                <h3>Manage Posts</h3>
                <div class="management-list">
                    <?php foreach ($posts as $post): ?>
                        <div class="management-card">
                            <div class="management-info">
                                <a href="<?= site_url('posts/' . $post['id']) ?>"><?= esc($post['title']) ?></a>
                                <span class="management-meta">By <?= esc($post['username']) ?></span>
                            </div>
                            <button class="delete-btn delete-post" data-id="<?= $post['id'] ?>">Delete</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Manage Comments -->
            <div class="management-section">
                <h3>Manage Comments</h3>
                <div class="management-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="management-card">
                            <div class="management-info">
                                <span><?= esc(substr($comment['content'], 0, 80)) ?>...</span>
                                <span class="management-meta">By <?= esc($comment['username']) ?></span>
                            </div>
                            <button class="delete-btn delete-comment" data-id="<?= $comment['id'] ?>">Delete</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Manage Communities -->
            <div class="management-section">
                <h3>Manage Communities</h3>
                <div class="management-list">
                    <?php foreach ($communities as $community): ?>
                        <div class="management-card">
                            <div class="management-info">
                                <a href="<?= site_url('communities/view/' . $community['id']) ?>">r/<?= esc($community['name']) ?></a>
                            </div>
                            <button class="delete-btn delete-community" data-id="<?= $community['id'] ?>">Delete</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let chartInstance;
    let currentFilter = "daily";

    function fetchChartData(filter) {
        fetch("<?= site_url('dashboard/getChartData?filter=') ?>" + filter)
            .then(response => response.json())
            .then(data => {
                console.log("Chart Data:", data);
                updateChart(data.posts);
            })
            .catch(error => console.error("Error fetching chart data:", error));
    }

    function updateChart(data) {
        const ctx = document.getElementById('postsChart').getContext('2d');

        if (chartInstance) {
            chartInstance.destroy();
        }

        if (!data || data.length === 0) {
            console.warn("No data available for chart.");
            return;
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
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Posts' }, beginAtZero: true }
                }
            }
        });
    }

    document.querySelectorAll('.delete-user, .delete-post, .delete-comment, .delete-community').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.classList.contains('delete-user') ? "User" :
                this.classList.contains('delete-post') ? "Post" :
                    this.classList.contains('delete-comment') ? "Comment" : "Community";

            if (confirm(`Are you sure you want to delete this ${type}?`)) {
                fetch("<?= site_url('dashboard/delete') ?>" + type, {
                    method: "POST",
                    body: JSON.stringify({ id: this.dataset.id }),
                    headers: { "Content-Type": "application/json" }
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(`${type} Deleted:`, data);
                        if (data.success) {
                            this.closest('.management-card').remove();
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch(error => console.error(`Error deleting ${type}:`, error));
            }
        });
    });

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            fetchChartData(currentFilter);
        });
    });

    fetchChartData(currentFilter);
</script>

<style>
    .dashboard-container {
        max-width: 1000px;
        margin: auto;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-box {
        background: #252525;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        font-size: 18px;
        color: #d7dadc;
        border: 1px solid #3a3a3a;
        transition: background 0.3s;
    }

    .stat-box:hover {
        background: #323232;
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .filter-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background: #3a3a3a;
        color: white;
    }

    .filter-btn.active {
        background: #ff4500;
    }

    .management-section {
        margin-top: 20px;
    }

    .table th, .table td {
        color: white;
    }

    .btn-danger {
        font-size: 12px;
    }
         /* General Management Section */
     .management-section {
         background: #1a1a1b;
         padding: 15px;
         border-radius: 8px;
         margin-top: 20px;
         border: 1px solid #3a3a3a;
     }

    .management-section h3 {
        color: #ff4500;
        margin-bottom: 15px;
        font-size: 20px;
    }

    /* Card-based layout for management items */
    .management-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .management-card {
        background: #252525;
        padding: 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #3a3a3a;
        transition: background 0.3s ease;
    }

    .management-card:hover {
        background: #333;
    }

    .management-info {
        display: flex;
        flex-direction: column;
    }

    .management-info a {
        color: #ff4500;
        font-weight: bold;
        text-decoration: none;
    }

    .management-info a:hover {
        text-decoration: underline;
    }

    .management-meta {
        font-size: 13px;
        color: #b0b3b8;
    }

    .delete-btn {
        background-color: #d9534f;
        border: none;
        color: white;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }

    .delete-btn:hover {
        background-color: #c9302c;
    }

    .chart-container {
        width: 100%;
        max-height: 400px; /* ✅ Limits the height of the graph */
        overflow: hidden;   /* ✅ Prevents it from expanding too much */
        position: relative;
    }

    #postsChart {
        max-height: 350px !important; /* ✅ Ensures the chart stays within a reasonable height */
    }
</style>


<?= $this->endSection() ?>
