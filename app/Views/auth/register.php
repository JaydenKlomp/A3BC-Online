<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <h2 class="auth-title">Sign Up</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <p class="text-danger"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <form action="<?= site_url('auth/register') ?>" method="post" onsubmit="return validatePassword()">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            <small id="passwordError" class="text-danger"></small> <!-- Error message -->
        </div>
        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
    </form>

    <p class="auth-switch">
        Already have an account? <a href="<?= site_url('login') ?>">Log in</a>
    </p>
</div>

<script>
    function validatePassword() {
        let password = document.getElementById("password").value;
        let confirmPassword = document.getElementById("confirm_password").value;
        let errorMessage = document.getElementById("passwordError");

        if (password !== confirmPassword) {
            errorMessage.innerText = "Passwords do not match.";
            return false; // Prevent form submission
        } else {
            errorMessage.innerText = "";
            return true;
        }
    }
</script>

<?= $this->endSection() ?>
