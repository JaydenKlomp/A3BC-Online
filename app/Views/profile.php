<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="profile-container">
    <!-- Left Sidebar -->
    <div class="profile-sidebar">
        <div class="profile-banner">
            <img src="<?= base_url('images/' . $user['banner']) ?>" alt="Banner">
        </div>
        <div class="profile-info">
            <img class="profile-avatar" src="<?= base_url('images/userprofile.png') ?>" alt="Profile">
            <h2><?= esc($user['username']) ?></h2>
            <p class="profile-bio"><?= esc($user['bio']) ?: "No bio available." ?></p>
            <hr>
            <p><strong>Karma:</strong> <?= $user['karma'] ?></p>
            <p><strong>Followers:</strong> <?= $user['followers'] ?></p>
            <p><strong>Joined:</strong> <?= date('F j, Y', strtotime($user['account_created'])) ?></p>
            <hr>
            <h4>🏆 Achievements</h4>
            <ul class="achievements">
                <li>🎨 Picasso</li>
                <li>🔥 Top 10% Poster</li>
                <li>🏅 Elder</li>
            </ul>
            <hr>
            <h4>🔗 Social Links</h4>
            <p><a href="#">GitHub</a></p>
            <p><a href="#">Twitter</a></p>
        </div>
    </div>

    <!-- Right Side: Posts & Comments -->
    <div class="profile-content">
        <div class="profile-tabs">
            <button class="tab-btn active" onclick="showTab('posts')">Posts</button>
            <button class="tab-btn" onclick="showTab('comments')">Comments</button>
        </div>

        <!-- Posts Section -->
        <div id="posts" class="tab-content active">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <h3><a href="<?= site_url('posts/' . $post['id']) ?>"><?= esc($post['title']) ?></a></h3>
                        <p><?= esc(substr($post['content'], 0, 200)) ?>...</p>
                        <span class="post-meta">🕒 <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </div>

        <!-- Comments Section -->
        <div id="comments" class="tab-content">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card">
                        <p><?= esc($comment['content']) ?></p>
                        <span class="comment-meta">🕒 <?= date('F j, Y', strtotime($comment['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(e => e.classList.remove('active'));
        document.querySelector(`#${tab}`).classList.add('active');

        document.querySelectorAll('.tab-btn').forEach(e => e.classList.remove('active'));
        document.querySelector(`[onclick="showTab('${tab}')"]`).classList.add('active');
    }
</script>

<?= $this->endSection() ?>
