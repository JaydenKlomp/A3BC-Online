<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="create-community-container">
    <h2 class="page-title">Create a New Community</h2>

    <div class="form-card">
        <form action="<?= site_url('communities/store') ?>" method="post" enctype="multipart/form-data">
            <!-- Community Name -->
            <div class="input-group">
                <label for="name">Community Name</label>
                <input type="text" name="name" class="input-field" required placeholder="e.g. r/Technology">
            </div>

            <!-- Description -->
            <div class="input-group">
                <label for="description">Description</label>
                <textarea name="description" class="input-field textarea-field" rows="3" required placeholder="Briefly describe your community..."></textarea>
            </div>

            <!-- Banner Upload -->
            <div class="input-group">
                <label for="banner">Upload Banner</label>
                <input type="file" name="banner" class="file-input" accept="image/*">
            </div>

            <!-- Profile Picture Upload -->
            <div class="input-group">
                <label for="profile_picture">Upload Profile Picture</label>
                <input type="file" name="profile_picture" class="file-input" accept="image/*">
            </div>

            <!-- Community Rules -->
            <div class="input-group">
                <label for="rules">Community Rules (One per line)</label>
                <textarea name="rules" class="input-field textarea-field" rows="4" placeholder="1. Be respectful&#10;2. No spam&#10;3. Follow Reddit TOS"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary">Create Community</button>
        </form>
    </div>
</div>

<style>
    /* General Page Styling */
    .create-community-container {
        max-width: 600px;
        margin: 40px auto;
        background: #1a1a1b;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1);
        color: #d7dadc;
    }

    .page-title {
        font-size: 22px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
        color: #ff4500;
    }

    /* Form Card */
    .form-card {
        background: #252525;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #3a3a3a;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .input-field, .file-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #444;
        border-radius: 5px;
        background: #333;
        color: #ffffff;
        font-size: 14px;
    }

    .textarea-field {
        resize: none;
    }

    .btn-primary {
        width: 100%;
        padding: 10px;
        background: #ff4500;
        border: none;
        color: white;
        font-size: 16px;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: #e03e00;
    }
</style>

<?= $this->endSection() ?>
