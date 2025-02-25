<?php require_once APPPATH . 'Helpers/TimeHelper.php'; ?>
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <!-- Main Content (Centered Posts) -->
        <main class="col-md-6">
            <!-- Community Header -->
            <div class="community-header">
                <!-- Community Banner -->
                <div class="community-banner-container">
                    <img src="<?= base_url('images/communitybanners/' . ($community['banner'] ?? 'default-banner.jpg')) ?>"
                         alt="Community Banner" class="community-banner">
                </div>

                <div class="community-info text-center">
                    <!-- ✅ Fixed Profile Picture Retrieval -->
                    <img src="<?= base_url('images/communitypictures/' . ($community['profile_picture'] ?? 'default-avatar.jpg')) ?>"
                         alt="Community Avatar" class="community-avatar">

                    <h2>r/<?= esc($community['name']) ?></h2>
                    <p><?= esc($community['description']) ?></p>
                    <a href="<?= site_url('posts/create') ?>" class="btn btn-primary">➕ Create Post in r/<?= esc($community['name']) ?></a>
                </div>
            </div>


            <h3 class="mt-4">📢 Recent Posts</h3>

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
                                        <a href="<?= site_url('profile/' . esc($post['username'])) ?>">
                                            <?= esc($post['username']) ?>
                                        </a>
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
                <p class="text-center">No posts yet.</p>
            <?php endif; ?>
        </main>

        <!-- Sidebar (Rules) -->
        <aside class="col-md-3 d-none d-md-block">
            <div class="sidebar">
                <h4>📌 Community Rules</h4>
                <ul>
                    <?php
                    $rules = isset($community['rules']) ? json_decode($community['rules'], true) : [];
                    if (!empty($rules)):
                        foreach ($rules as $rule): ?>
                            <li><?= esc($rule) ?></li>
                        <?php endforeach;
                    else: ?>
                        <p class="text-muted">No rules set.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>
    </div>
</div>

<style>
    /* Community Header */
    .community-header {
        position: relative;
        text-align: center;
        margin-bottom: 20px;
    }

    .community-banner-container {
        width: 100%;
        height: 200px;
        overflow: hidden;
        border-radius: 8px;
    }

    .community-banner {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .community-info {
        margin-top: -50px;
        text-align: center;
    }

    .community-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid #fff;
        background-color: #000;
    }

    /* Sidebar */
    .sidebar {
        background: #252525;
        padding: 15px;
        border-radius: 8px;
    }

    .sidebar h4 {
        color: #ff4500;
    }

    /* Post Styling */
    .post-card {
        display: flex;
        align-items: center;
        background-color: #282828;
        border: 1px solid #383838;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .post-votes {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-right: 15px;
    }

    .vote-btn {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #818384;
    }

    .vote-btn.upvote:hover {
        color: #ff4500;
    }

    .vote-btn.downvote:hover {
        color: #7193ff;
    }

    .vote-count {
        font-size: 16px;
        font-weight: bold;
        color: #d7dadc;
    }

    .post-content-with-image {
        display: flex;
        flex-grow: 1;
        justify-content: space-between;
    }

    .post-text {
        flex-grow: 1;
    }

    .post-image {
        max-width: 120px;
        max-height: 120px;
        margin-left: 10px;
    }

    .post-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .post-meta {
        font-size: 12px;
        color: #b0b3b8;
    }
</style>

<?= $this->endSection() ?>
