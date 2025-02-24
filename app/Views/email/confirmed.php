<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <h2 class="auth-title">Email Verified!</h2>
    <p>Your email has been successfully verified. You can now <a href="<?= site_url('login') ?>">log in</a>.</p>
</div>
<?= $this->endSection() ?>
