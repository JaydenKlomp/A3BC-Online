<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <h2 class="auth-title">Log In</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <p class="text-danger"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <form action="<?= site_url('auth/login') ?>" method="post">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Log In</button>
    </form>

    <p class="auth-switch">
        New here? <a href="<?= site_url('register') ?>">Create an account</a>
    </p>
</div>
<?= $this->endSection() ?>
