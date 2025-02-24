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
            <?php if ($user['role'] === 'admin'): ?>
                <span class="badge bg-danger">Admin</span>
            <?php elseif ($user['role'] === 'moderator'): ?>
                <span class="badge bg-warning text-dark">Moderator</span>
            <?php endif; ?>

            <p class="profile-bio"><?= esc($user['bio']) ?: "No bio available." ?></p>
            <hr>
            <p><strong>Karma:</strong> <?= $user['karma'] ?></p>
            <p><strong>Followers:</strong> <?= $user['followers'] ?></p>
            <p><strong>Joined:</strong> <?= date('F j, Y', strtotime($user['account_created'])) ?></p>
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

        <!-- 💬 Comments Section (verborgen bij start) -->
        <div id="comments" class="tab-content">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card">
                        <p>
                            <a href="<?= site_url('posts/' . $comment['post_id']) ?>#comment-<?= $comment['id'] ?>">
                                <?= esc(substr($comment['content'], 0, 150)) ?>...
                            </a>
                        </p>
                        <span class="comment-meta">🕒 <?= date('F j, Y', strtotime($comment['created_at'])) ?></span>
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
        // Verberg alle tab-content divs
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });

        // Verwijder de active class van alle tab-knoppen
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.classList.remove('active');
        });

        // Toon de geselecteerde tab en markeer de knop als actief
        document.getElementById(tab).style.display = 'block';
        document.querySelector(`[onclick="showTab('${tab}')"]`).classList.add('active');
    }

    // **Zorg ervoor dat de eerste tab altijd zichtbaar is**
    document.addEventListener("DOMContentLoaded", function () {
        showTab('posts');
    });
</script>

<?= $this->endSection() ?>
