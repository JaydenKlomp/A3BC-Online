<?php
require_once APPPATH . 'Helpers/TimeHelper.php';
/** @var int $currentSort */
?>

<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Main Content (Centered Posts) -->
        <main class="col-md-6">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="mb-0">For You</h2>

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

        <!-- Sidebar (Far Right) -->
        <aside class="col-md-3 d-none d-md-block">
            <div class="sidebar">
                <h4>📌 Communities</h4>
                <p><a href="#">r/general</a></p>
                <p><a href="#">r/technology</a></p>
                <p><a href="#">r/gaming</a></p>
                <p><a href="#">r/movies</a></p>
                <p><a href="#">r/science</a></p>
                <a href="#" class="sidebar-button btn btn-sm btn-primary w-100 mt-2">Create Community</a>
            </div>
        </aside>
    </div>
</div>

<script>
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function (e) {
            e.preventDefault();
            let sortType = this.getAttribute('data-sort');
            document.getElementById('currentSort').innerText = this.innerText;
            window.location.href = "<?= site_url('posts?sort=') ?>" + sortType;
        });
    });
</script>

<style>
    .post-card {
        display: flex;
        align-items: center;
        background-color: #282828;
        border: 1px solid #383838;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
    }




</style>

<?= $this->endSection() ?>
