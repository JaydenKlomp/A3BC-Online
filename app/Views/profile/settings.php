
<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="settings-container">
    <div class="settings-tabs">
        <button class="settings-tab active" onclick="showTab('account')">Account</button>
        <button class="settings-tab" onclick="showTab('profile')">Profile</button>
    </div>

    <!-- 🔹 Account Settings Form -->
    <form action="<?= site_url('settings/updateAccount') ?>" method="post" id="account" class="settings-content active">
        <?= csrf_field() ?>

        <div class="settings-section">
            <h3>Email Address</h3>
            <input type="email" class="settings-input" name="email" value="<?= esc($user['email']) ?>">
        </div>

        <div class="settings-section">
            <h3>Phone Number</h3>
            <input type="text" class="settings-input" name="phone" value="<?= esc($user['phone'] ?? '') ?>">
        </div>

        <div class="settings-section">
            <h3>New Password</h3>
            <input type="password" class="settings-input" name="password">
        </div>

        <button type="submit" class="settings-button">Save Changes</button>

        <div class="settings-section">
            <h3>Delete Account</h3>
            <button type="submit" formaction="<?= site_url('settings/deleteAccount') ?>" class="settings-button delete-button">Delete</button>
        </div>
    </form>

    <!-- 🔹 Profile Settings Form -->
    <form action="<?= site_url('settings/updateProfile') ?>" method="post" enctype="multipart/form-data" id="profile" class="settings-content">
        <?= csrf_field() ?>

        <div class="settings-section">
            <h3>Profile Picture</h3>
            <input type="file" class="settings-input" name="profile_picture">
            <small>Max size: 2MB | Allowed formats: JPG, PNG</small>
        </div>

        <div class="settings-section">
            <h3>Banner</h3>
            <input type="file" class="settings-input" name="banner">
        </div>

        <div class="settings-section">
            <h3>Display Name</h3>
            <input type="text" class="settings-input" name="display_name" value="<?= esc($user['username']) ?>">
        </div>

        <div class="settings-section">
            <h3>About Description</h3>
            <textarea class="settings-input" name="bio"><?= esc($user['bio']) ?></textarea>
        </div>

        <div class="settings-section">
            <h3>Social Links</h3>
            <textarea class="settings-input" name="social_links"><?= esc($user['social_links']) ?></textarea>
        </div>

        <button type="submit" class="settings-button">Save Changes</button>
    </form>
</div>

<script>
    function showTab(tab) {
        document.querySelectorAll('.settings-content').forEach(e => e.classList.remove('active'));
        document.getElementById(tab).classList.add('active');

        document.querySelectorAll('.settings-tab').forEach(e => e.classList.remove('active'));
        event.target.classList.add('active');
    }
</script>

<?= $this->endSection() ?>
