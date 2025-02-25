<?php
$session = session();
$isLoggedIn = $session->has('user_id');
$username = $session->get('username') ?? 'Guest';
$role = $session->get('role') ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'A3BC Online' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <!-- Left Side: Logo + Site Name -->
        <a class="navbar-brand d-flex align-items-center" href="<?= site_url('/') ?>">
            <img src="<?= base_url('images/logo.png') ?>" alt="A3BC" class="site-logo">
            <span class="site-name ms-2">A3BC Online</span>
        </a>

        <!-- Right Side: Create Post + View Communities + Profile -->
        <div class="d-flex align-items-center">
            <a class="btn btn-primary me-3" href="<?= site_url('posts') ?>">For you</a>
            <a class="btn btn-primary me-3" href="<?= site_url('communities') ?>">Communities</a>
            <a class="btn btn-primary me-3" href="<?= site_url('posts/create') ?>">Create Post</a>

            <?php if ($role === 'admin'): ?>
                <a class="btn btn-primary me-3" href="<?= site_url('dashboard') ?>">Dashboard</a>
            <?php endif; ?>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <img src="<?= base_url('images/profilepicture/' . ($session->get('profile_picture') ?? 'default.jpg')) ?>"
                     alt="User" class="profile-pic" id="profileDropdown" data-bs-toggle="dropdown">
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($isLoggedIn): ?>
                        <li class="dropdown-header text-center text-white font-weight-bold">
                            <strong>Hello, <?= htmlspecialchars($username) ?></strong>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= site_url('profile/' . $username) ?>">View Profile</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('settings') ?>">Edit Avatar</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('achievements') ?>">Achievements</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= site_url('settings') ?>">Settings</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Log Out</a></li>

                        <?php if ($role === 'admin'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-success" href="<?= site_url('communities/create') ?>">Create Community</a></li>
                        <?php endif; ?>

                    <?php else: ?>
                        <li><a class="dropdown-item" href="<?= site_url('login') ?>">Log In</a></li>
                        <li><a class="dropdown-item" href="<?= site_url('register') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container mt-4">
    <?= $this->renderSection('content') ?>
</div>

<script src="<?= base_url('js/functions.js') ?>"></script>

</body>
</html>
