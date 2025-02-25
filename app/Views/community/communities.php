<?php require_once APPPATH . 'Helpers/TimeHelper.php';?>
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="container">
    <h2 class="text-center mb-4">🌐 Communities</h2>

    <?php if (session()->get('role') === 'admin'): ?>
        <div class="text-center mb-3">
            <a href="<?= site_url('communities/create') ?>" class="btn btn-success">➕ Create Community</a>
        </div>
    <?php endif; ?>

    <div class="community-list">
        <?php foreach ($communities as $community): ?>
            <div class="community-card">
                <h4>
                    <a href="<?= site_url('communities/view/' . $community['id']) ?>">
                        <?= esc($community['name']) ?>
                    </a>
                </h4>
                <p><?= esc($community['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .community-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
    }

    .community-card {
        background: #2a2a2a;
        border-radius: 8px;
        padding: 15px;
        transition: background 0.3s ease;
        border: 1px solid #3a3a3a;
    }

    .community-card:hover {
        background: #333;
    }

    .community-card h4 a {
        color: #ff4500;
        text-decoration: none;
    }

    .community-card h4 a:hover {
        text-decoration: underline;
    }

    .community-card p {
        color: #d7dadc;
        font-size: 14px;
    }
</style>

<?= $this->endSection() ?>
