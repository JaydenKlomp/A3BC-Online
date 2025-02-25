<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="profile-container">
    <!-- Left Sidebar -->
    <div class="profile-sidebar">
        <div class="profile-banner">
            <img src="<?= base_url('images/banners/' . ($user['banner'] ?? 'default-banner.jpg')) ?>" alt="Banner">
        </div>
        <div class="profile-info">
            <img class="profile-avatar" src="<?= base_url('images/profilepicture/' . ($user['profile_picture'] ?? 'default.jpg')) ?>" alt="Profile">
            <h2><?= esc($user['username']) ?></h2>
            <?php if ($user['role'] === 'admin'): ?>
                <span class="badge bg-danger">Admin</span>
            <?php elseif ($user['role'] === 'moderator'): ?>
                <span class="badge bg-warning text-dark">Moderator</span>
            <?php endif; ?>

            <p class="profile-bio"><?= esc($user['bio']) ?: "No bio available." ?></p>

            <hr>
            <p><strong>Karma:</strong> <?= esc($user['karma']) ?></p>
            <p><strong>Followers:</strong> <?= esc($user['followers']) ?></p>
            <p><strong>Joined:</strong> <?= date('F j, Y', strtotime($user['account_created'])) ?></p>

            <!-- ✅ Show "Edit Profile" Button If the User is Viewing Their Own Profile -->
            <?php if (session()->get('username') === $user['username']): ?>
                <hr>
                <a href="<?= site_url('settings') ?>" class="btn btn-primary btn-sm mt-2">
                    ✏ Edit Profile
                </a>
            <?php endif; ?>
        </div>
    </div>


    <!-- Right Side: Posts & Comments -->
    <div class="profile-content">
        <div class="profile-tabs">
            <button class="tab-btn active" onclick="showTab('posts')">📢 Posts</button>
            <button class="tab-btn" onclick="showTab('comments')">💬 Comments</button>
        </div>

        <!-- 📢 Posts Section -->
        <div id="posts" class="tab-content active">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card-profile">
                        <h3>
                            <a href="<?= site_url('posts/' . $post['id']) ?>">
                                <?= esc($post['title']) ?>
                            </a>
                        </h3>
                        <p class="post-excerpt"><?= esc(substr($post['content'], 0, 200)) ?>...</p>
                        <span class="post-meta">🕒 <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No posts found.</p>
            <?php endif; ?>
        </div>

        <!-- 💬 Comments Section -->
        <div id="comments" class="tab-content">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="post-card-profile">
                        <!-- ✅ Post Title (White, Not Clickable) -->
                        <h3 class="post-title-profile">
                            <?= esc($comment['post_title']) ?>
                        </h3>

                        <!-- ✅ Comment Content (Orange, Clickable) -->
                        <p class="comment-highlight">
                            <a href="<?= site_url('posts/' . $comment['post_id']) ?>#comment-<?= $comment['id'] ?>">
                                <?= esc(substr($comment['content'], 0, 150)) ?>...
                            </a>
                        </p>

                        <span class="post-meta">🕒 <?= date('F j, Y', strtotime($comment['created_at'])) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No comments found.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    function showTab(tab) {
        // Hide all tab-content divs
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });

        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.classList.remove('active');
        });

        // Show the selected tab and mark the button as active
        document.getElementById(tab).style.display = 'block';
        document.querySelector(`[onclick="showTab('${tab}')"]`).classList.add('active');
    }

    // Ensure the first tab is visible when the page loads
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none'; // Hide all initially
        });

        document.getElementById('posts').style.display = 'block'; // Show posts by default
        document.querySelector("[onclick=\"showTab('posts')\"]").classList.add('active');
    });

</script>

<?= $this->endSection() ?>
