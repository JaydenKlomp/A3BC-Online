<?php
require_once APPPATH . 'Helpers/TimeHelper.php';
/** @var $trendingPosts */
?>

<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Reddit-style Post View -->
<div class="container mt-4">
    <div class="row">
        <!-- Left Column (Post & Comments) -->
        <div class="col-md-8">
            <div class="post-container p-3 mb-4">
                <!-- Voting System -->
                <?php
                /** @var $post */
                $userVotedUp = isset($_SESSION['user_votes'][$post['id']]) && $_SESSION['user_votes'][$post['id']] === 'upvotes';
                $userVotedDown = isset($_SESSION['user_votes'][$post['id']]) && $_SESSION['user_votes'][$post['id']] === 'downvotes';
                ?>
                <div class="vote-column">
                    <button class="vote-btn upvote <?= $userVotedUp ? 'voted' : '' ?>"
                            data-post-id="<?= $post['id'] ?>"
                            style="color: <?= $userVotedUp ? '#ff4500' : '#818384' ?>">▲</button>
                    <div class="vote-count" id="vote-count-<?= $post['id'] ?>">
                        <?= $post['upvotes'] - $post['downvotes'] ?>
                    </div>
                    <button class="vote-btn downvote <?= $userVotedDown ? 'voted' : '' ?>"
                            data-post-id="<?= $post['id'] ?>"
                            style="color: <?= $userVotedDown ? '#7193ff' : '#818384' ?>">▼</button>
                </div>


                <!-- Post Content -->
                <div class="post-content">
                    <h2 class="post-title"><?= esc($post['title']) ?></h2>
                    <p class="post-meta">
                        Posted by
                        <?php
                        $username = isset($post['username']) ? esc($post['username']) : 'Anonymous';
                        $role = isset($post['role']) ? $post['role'] : '';
                        ?>

                        <strong>
                            <a href="<?= site_url('profile/' . $username) ?>" class="user-link">
                                <?= ($role === 'admin') ? '<span style="color: gold;">[ADMIN] ' . $username . '</span>' : $username; ?>
                            </a>
                        </strong>
                        • <?= time_elapsed_string($post['created_at']) ?>
                    </p>

                    <!-- ✅ Show Image If Post Has One (Styled) -->
                    <?php if (!empty($post['image'])) : ?>
                        <div class="post-image-container">
                            <img src="<?= base_url('images/posts/' . $post['image']) ?>" alt="Post Image" class="post-image-view">
                        </div>
                    <?php endif; ?>

                    <p><?= esc($post['content']) ?></p>

                    <!-- ✅ Delete Post Button with Confirmation -->
                    <?php if (session()->get('user_id') === $post['user_id']) : ?>
                        <form action="<?= site_url('posts/delete/' . $post['id']) ?>" method="post" onsubmit="return confirmDelete(event);">
                            <button type="submit" class="btn btn-danger">🗑 Delete Post</button>
                        </form>
                    <?php endif; ?>

                    <script>
                        function confirmDelete(event) {
                            event.preventDefault(); // Stop form submission
                            let confirmAction = confirm("❗ Are you sure you want to delete this post? This action cannot be undone.");
                            if (confirmAction) {
                                event.target.submit(); // Continue deleting if confirmed
                            }
                        }
                    </script>

                </div>
            </div>

            <!-- Comment Section -->
            <h4>💬 Comments</h4>
            <form action="<?= site_url('posts/comment/add') ?>" method="post">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <textarea name="content" class="write-comment" placeholder="Write a comment..." required></textarea>
                <button type="submit" class="btn btn-primary mt-2">Post Comment</button>
            </form>

            <!-- Recursive Function to Render Nested Comments -->
            <?php function renderComments($comments, $level = 0) { ?>
                <?php foreach ($comments as $comment) : ?>
                    <?php
                    $userVotedUp = isset($_SESSION['user_votes'][$comment['id']]) && $_SESSION['user_votes'][$comment['id']] === 'upvotes';
                    $userVotedDown = isset($_SESSION['user_votes'][$comment['id']]) && $_SESSION['user_votes'][$comment['id']] === 'downvotes';
                    ?>
                    <div class="comment-box" style="margin-left: <?= $level * 20 ?>px;">
                        <div class="comment-votes">
                            <button class="vote-btn upvote <?= $userVotedUp ? 'voted' : '' ?>"
                                    data-comment-id="<?= $comment['id'] ?>"
                                    style="color: <?= $userVotedUp ? '#ff4500' : '#818384' ?>">▲</button>
                            <div class="vote-count" id="comment-vote-count-<?= $comment['id'] ?>">
                                <?= $comment['upvotes'] - $comment['downvotes'] ?>
                            </div>
                            <button class="vote-btn downvote <?= $userVotedDown ? 'voted' : '' ?>"
                                    data-comment-id="<?= $comment['id'] ?>"
                                    style="color: <?= $userVotedDown ? '#7193ff' : '#818384' ?>">▼</button>
                        </div>

                        <!-- Comment Section -->
                        <div class="comment-content">
                            <p class="post-meta">
                                <?php if (!empty($post['community_id'])) : ?>
                                    <a href="<?= site_url('communities/view/' . $post['community_id']) ?>" class="community-link">
                                        r/<?= esc($post['community_name']) ?>
                                    </a>
                                    •
                                <?php endif; ?>

                                Posted by
                                <?php
                                $username = isset($post['username']) ? esc($post['username']) : 'Anonymous';
                                $role = isset($post['role']) ? $post['role'] : '';
                                ?>
                                <strong>
                                    <a href="<?= site_url('profile/' . $username) ?>" class="user-link">
                                        <?= ($role === 'admin') ? '<span style="color: gold;">[ADMIN] ' . $username . '</span>' : $username; ?>
                                    </a>
                                </strong>
                                • <?= time_elapsed_string($post['created_at']) ?>
                            </p>


                            <p><?= esc($comment['content']) ?></p>

                            <!-- Reply Form -->
                            <form class="comment-reply" action="<?= site_url('posts/comment/add') ?>" method="post">
                                <input type="hidden" name="post_id" value="<?= $comment['post_id'] ?>">
                                <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                                <textarea name="content" placeholder="Reply..." required></textarea>
                                <button type="submit" class="btn btn-secondary">Reply</button>
                            </form>

                            <!-- Render Nested Replies -->
                            <?php if (!empty($comment['replies'])) : ?>
                                <?= renderComments($comment['replies'], $level + 1) ?>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php } ?>

            <!-- Render All Comments -->
            <?= /** @var $comments */
            renderComments($comments) ?>


        </div>

        <!-- Right Column (Trending Posts) -->
        <div class="col-md-4">
            <div class="sidebar p-3">
                <h5>🔥 Trending Posts</h5>
                <?php foreach ($trendingPosts as $trending) : ?>
                    <p>
                        <a href="<?= site_url('posts/' . $trending['id']) ?>">
                            <?= esc($trending['title']) ?>
                        </a>
                    </p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
