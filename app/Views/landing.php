<?php
require_once APPPATH . 'Helpers/TimeHelper.php';
?>
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Welcome Section -->
<div class="landing-header text-center">
    <h1>Welcome to A3BC Online</h1>
    <p class="lead">Join discussions, share your thoughts, and upvote the best content.</p>
    <a href="<?= site_url('posts/create') ?>" class="btn btn-primary">Create a Post</a>
    <a href="<?= site_url('posts') ?>" class="btn btn-secondary">Explore Posts</a>
</div>

<div class="row-landing row mt-5">
    <!-- Left Column: Featured Posts -->
    <div class="col-md-8">
        <h3>📌 Featured Posts</h3>
        <div class="featured-posts">
            <?php if (!empty($featured_posts)) : ?>
                <?php foreach ($featured_posts as $post) : ?>
                    <div class="post-card featured-post">
                        <div class="post-votes">
                            <button class="vote-btn upvote" data-post-id="<?= $post['id'] ?>">▲</button>
                            <div class="vote-count" id="vote-count-<?= $post['id'] ?>">
                                <?= $post['upvotes'] - $post['downvotes'] ?>
                            </div>
                            <button class="vote-btn downvote" data-post-id="<?= $post['id'] ?>">▼</button>
                        </div>
                        <div class="post-content">
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
                                    <?= ($role === 'admin') ? '<span style="color: gold;">[ADMIN] ' . $username . '</span>' : $username; ?>
                                </strong>

                                • <?= time_elapsed_string($post['created_at']) ?>
                            </p>

                            <a href="<?= site_url('posts/' . $post['id']) ?>" class="btn btn-sm btn-primary">Read More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-muted">No featured posts yet. Create one!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Trending Posts & Live Discussions -->
    <div class="col-md-4">
        <!-- Trending Posts -->
        <div class="sidebar-section">
            <h4>🔥 Trending Posts</h4>
            <?php if (!empty($trending_posts)) : ?>
                <?php foreach ($trending_posts as $trending) : ?>
                    <p>
                        <a href="<?= site_url('posts/' . $trending['id']) ?>">
                            <?= esc($trending['title']) ?>
                        </a>
                    </p>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-muted">No trending posts yet.</p>
            <?php endif; ?>
        </div>

        <!-- Live Discussions (Latest Comments) -->
        <div class="sidebar-section">
            <h4>💬 Live Discussions</h4>
            <?php if (!empty($latest_comments)) : ?>
                <?php foreach ($latest_comments as $comment) : ?>
                    <p>
                        <a href="<?= site_url('posts/' . $comment['post_id']) ?>">
                            <?= substr(esc($comment['content']), 0, 80) ?>...
                        </a>
                    </p>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="text-muted">No discussions yet.</p>
            <?php endif; ?>
        </div>

        <!-- Community Stats -->
        <div class="sidebar-section">
            <h4>📊 Community Stats</h4>
            <p>Total Posts: <strong><?= $total_posts ?></strong></p>
            <p>Total Comments: <strong><?= $total_comments ?></strong></p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
