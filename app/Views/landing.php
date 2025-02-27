<?php require_once APPPATH . 'Helpers/TimeHelper.php';?>
<?php
/** @var $total_posts */
/** @var $total_comments */
/** @var $total_communities */
/** @var $communities */
?>
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="page-layout">

        <!-- Left Sidebar -->
        <div class="sidebar-left">
            <aside class="sidebar">
                <h4>🏠 Navigation</h4>
                <ul>
                    <li><a href="<?= site_url('/') ?>">Home</a></li>
                    <li><a href="<?= site_url('/popular') ?>">Popular</a></li>
                    <li><a href="<?= site_url('/explore') ?>">Explore</a></li>
                    <li><a href="<?= site_url('/all') ?>">All</a></li>
                </ul>

                <div class="sidebar-section">
                    <h4>🕵️ Recent Communities</h4>
                    <?php if (!empty($recent_communities)) : ?>
                        <ul>
                            <?php foreach ($recent_communities as $community) : ?>
                                <li><a href="<?= site_url('communities/view/' . $community['id']) ?>">r/<?= esc($community['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p class="text-muted">No recent communities.</p>
                    <?php endif; ?>
                </div>

                <div class="sidebar-section">
                    <h4>🌎 All Communities</h4>
                    <ul>
                        <?php foreach ($communities as $community) : ?>
                            <li><a href="<?= site_url('communities/view/' . $community['id']) ?>">r/<?= esc($community['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h4>📚 Resources</h4>
                    <ul>
                        <li><a href="<?= site_url('/about') ?>">About</a></li>
                        <li><a href="<?= site_url('/communities') ?>">Communities</a></li>
                        <li><a href="<?= site_url('/rules') ?>">Rules</a></li>
                    </ul>
                </div>
            </aside>
        </div>

        <!-- Middle Section (Posts - Unchanged from Index.php) -->
        <div class="content-wrapper">
            <main>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="mb-0">For You</h2>
                </div>

                <!-- Sorting Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                        Sort by: <span id="currentSort"><?= esc($currentSort) ?? 'Hot' ?></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item sort-option" data-sort="hot" href="#">Hot</a></li>
                        <li><a class="dropdown-item sort-option" data-sort="new" href="#">New</a></li>
                        <li><a class="dropdown-item sort-option" data-sort="top" href="#">Top</a></li>
                        <li><a class="dropdown-item sort-option" data-sort="rising" href="#">Rising</a></li>
                    </ul>
                </div>

                <?php if (!empty($posts)) : ?>
                    <div class="post-list">
                        <?php foreach ($posts as $post) : ?>
                            <div class="post-card">
                                <!-- Voting System -->
                                <div class="post-votes">
                                    <button class="vote-btn upvote" data-post-id="<?= $post['id'] ?>">▲</button>
                                    <div class="vote-count" id="vote-count-<?= $post['id'] ?>">
                                        <?= $post['upvotes'] - $post['downvotes'] ?>
                                    </div>
                                    <button class="vote-btn downvote" data-post-id="<?= $post['id'] ?>">▼</button>
                                </div>

                                <!-- Post Content -->
                                <div class="post-content-with-image">
                                    <div class="post-text">
                                        <h5>
                                            <a href="<?= site_url('posts/' . $post['id']) ?>">
                                                <?= esc($post['title']) ?>
                                            </a>
                                        </h5>
                                        <p><?= substr(esc($post['content']), 0, 100) ?>...</p>
                                        <p class="post-meta">
                                            <?php if (!empty($post['community_id'])) : ?>
                                                <a href="<?= site_url('communities/view/' . $post['community_id']) ?>" class="community-link">
                                                    r/<?= esc($post['community_name']) ?>
                                                </a>
                                                •
                                            <?php endif; ?>

                                            Posted by
                                            <?php
                                            $username = isset($post['username']) ? esc($post['username']) : 'Anonymous';
                                            $role = isset($post['role']) ? $post['role'] : '';
                                            ?>
                                            <strong>
                                                <a href="<?= site_url('profile/' . $username) ?>" class="user-link">
                                                    <?= ($role === 'admin') ? '<span style="color: gold;">[ADMIN] ' . $username . '</span>' : $username; ?>
                                                </a>
                                            </strong>
                                            • <?= time_elapsed_string($post['created_at']) ?>
                                        </p>

                                    </div>

                                    <!-- Display image if available -->
                                    <?php if (!empty($post['image'])) : ?>
                                        <div class="post-image">
                                            <img src="<?= base_url('images/posts/' . $post['image']) ?>" alt="Post Image">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="text-muted">No posts yet. You could be the first to share!</p>
                <?php endif; ?>
            </main>
        </div>

        <!-- Right Sidebar -->
        <div class="sidebar-right">
            <aside class="sidebar">
                <div class="sidebar-section">
                    <h4>💬 Recent Discussions</h4>
                    <?php if (!empty($latest_comments)) : ?>
                        <ul>
                            <?php foreach ($latest_comments as $comment) : ?>
                                <li>
                                    <a href="<?= site_url('posts/' . $comment['post_id']) ?>">
                                        <?= substr(esc($comment['content']), 0, 80) ?>...
                                    </a>
                                    <p class="discussion-meta">In <a href="<?= site_url('communities/view/' . $comment['community_id']) ?>">r/<?= esc($comment['community_name']) ?></a></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p class="text-muted">No discussions yet.</p>
                    <?php endif; ?>
                </div>

                <div class="sidebar-section">
                    <h4>📊 Community Stats</h4>
                    <p>Total Posts: <strong><?= $total_posts ?></strong></p>
                    <p>Total Comments: <strong><?= $total_comments ?></strong></p>
                    <p>Total Communities: <strong><?= $total_communities ?></strong></p>
                </div>
            </aside>
        </div>
    </div>
</div>


<style>
    /* ✅ Ensure the page uses full width */
    .container-fluid {
        display: flex;
        justify-content: center;
        max-width: 100%;
        padding: 0;
    }

    /* ✅ Wrapper to control layout */
    .page-layout {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    /* ✅ Left Sidebar - Fixed to the Far Left */
    .sidebar-left {
        position: fixed;
        left: 0;
        top: 60px; /* Adjust to match navbar height */
        height: calc(100vh - 60px); /* Full height minus navbar */
        padding: 15px;
        background-color: #1a1a1b;
        border-right: 1px solid #343536;
        overflow-y: auto;
    }

    /* ✅ Right Sidebar - Fixed to the Far Right */
    .sidebar-right {
        position: fixed;
        right: 0;
        top: 60px;
        height: calc(100vh - 60px);
        padding: 15px;
        background-color: #1a1a1b;
        border-left: 1px solid #343536;
        overflow-y: auto;
    }

    /* ✅ Center Content - Keeps Posts in the Middle */
    .content-wrapper {
        flex-grow: 1;
        max-width: 800px; /* Prevent posts from stretching */
        margin: 0 auto;
        padding: 20px;
    }

    /* ✅ Sidebar Content */
    .sidebar {
        color: #d7dadc;
    }

    .sidebar h4 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin-bottom: 8px;
    }

    .sidebar a {
        color: #ff4500;
        text-decoration: none;
    }

    .sidebar a:hover {
        color: #e03e00;
    }

    /* ✅ Sidebar Sections */
    .sidebar-section {
        background: #1a1a1b;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        color: #ffffff;
        border: 1px solid #343536;
    }

    /* ✅ Mobile Adjustments */
    @media (max-width: 1200px) {
        .sidebar-left,
        .sidebar-right {
            position: relative;
            width: 100%;
            height: auto;
            border: none;
        }

        .content-wrapper {
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }
    }

</style>

<script>
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            let sortType = this.getAttribute('data-sort');
            document.getElementById('currentSort').innerText = this.innerText;
            window.location.href = "<?= site_url('?sort=') ?>" + sortType;
        });
    });
</script>

<?= $this->endSection() ?>
