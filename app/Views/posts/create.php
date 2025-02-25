<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Reddit-Style Create Post Page -->
<div class="create-post-container">
    <h2 class="text-center">Create a New Post</h2>

    <div class="post-type-selector">
        <button type="button" class="post-type-btn active" data-type="text">📝 Text</button>
        <button type="button" class="post-type-btn" data-type="image">📷 Image</button>
        <button type="button" class="post-type-btn" data-type="link">🔗 Link</button>
    </div>

    <form action="<?= site_url('posts/store') ?>" method="post" enctype="multipart/form-data">
        <!-- Post Title -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required placeholder="Enter a title...">
        </div>

        <!-- Post Content -->
        <div class="post-content-section" id="text-content">
            <label for="content" class="form-label">Text Content</label>
            <textarea name="content" class="form-control text-content-field" rows="5" placeholder="Write something..."></textarea>
        </div>


        <!-- Image Upload -->
        <div class="post-content-section hidden" id="image-content">
            <label for="image" class="form-label">Upload Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
            <div class="image-preview mt-2">
                <img id="imagePreview" src="#" alt="Image Preview" class="hidden">
            </div>
            <textarea name="content" class="form-control text-content-field" rows="5" placeholder="Write something..."></textarea>
        </div>

        <!-- Link Submission -->
        <div class="post-content-section hidden" id="link-content">
            <label for="link" class="form-label">Share a Link</label>
            <input type="url" name="link" class="form-control" placeholder="https://example.com">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Post</button>
    </form>
</div>

<script>

    document.querySelector("form").addEventListener("submit", function(event) {
        console.log("Form submitted!");
    });

    document.querySelectorAll('.post-type-btn').forEach(button => {
        button.addEventListener('click', () => {
            // Remove active state from all buttons
            document.querySelectorAll('.post-type-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Hide all content sections
            document.querySelectorAll('.post-content-section').forEach(section => section.classList.add('hidden'));

            // Show the selected type
            let type = button.getAttribute('data-type');
            document.getElementById(type + '-content').classList.remove('hidden');
        });
    });

    // Preview selected image
    function previewImage(event) {
        const image = event.target.files[0];
        if (image) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('imagePreview');
                preview.src = reader.result;
                preview.classList.remove('hidden');
                preview.style.maxWidth = "100%";
                preview.style.borderRadius = "8px";
            };
            reader.readAsDataURL(image);
        }
    }
</script>

<style>
    .hidden {
        display: none;
    }
    .image-preview img {
        max-width: 100%;
        border-radius: 8px;
    }
</style>

<?= $this->endSection() ?>
