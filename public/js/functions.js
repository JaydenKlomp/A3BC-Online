document.addEventListener("DOMContentLoaded", function () {
    // **Comment Voting System**
    document.querySelectorAll('.vote-btn[data-comment-id]').forEach(button => {
        button.addEventListener('click', () => {
            let commentId = button.getAttribute('data-comment-id');
            let voteType = button.classList.contains('upvote') ? 'upvotes' : 'downvotes';
            let voteCountElem = document.getElementById(`comment-vote-count-${commentId}`);

            let action = button.classList.contains("voted") ? "remove" : "add"; // Check if already voted

            fetch('/posts/comment/vote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${commentId}&vote_type=${voteType}&action=${action}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        voteCountElem.innerText = data.upvotes - data.downvotes;

                        // Reset all vote buttons for this comment
                        document.querySelectorAll(`[data-comment-id="${commentId}"]`).forEach(btn => {
                            btn.classList.remove("voted");
                            btn.style.color = "#818384";
                        });

                        // Apply color to the selected button
                        if (action === "add") {
                            button.classList.add("voted");
                            button.style.color = voteType === "upvotes" ? "#ff4500" : "#7193ff";
                        }
                    }
                })
                .catch(error => console.error("Fout bij stemmen:", error));
        });
    });

    // **Post Voting System**
    document.querySelectorAll('.vote-btn[data-post-id]').forEach(button => {
        button.addEventListener('click', () => {
            let postId = button.getAttribute('data-post-id');
            let voteType = button.classList.contains('upvote') ? 'upvotes' : 'downvotes';
            let voteCountElem = document.getElementById(`vote-count-${postId}`);

            let action = button.classList.contains("voted") ? "remove" : "add";

            fetch('/posts/vote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}&vote_type=${voteType}&action=${action}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        voteCountElem.innerText = data.upvotes - data.downvotes;

                        // Reset all vote buttons for this post
                        document.querySelectorAll(`[data-post-id="${postId}"]`).forEach(btn => {
                            btn.classList.remove("voted");
                            btn.style.color = "#818384";
                        });

                        // Apply color to the selected button
                        if (action === "add") {
                            button.classList.add("voted");
                            button.style.color = voteType === "upvotes" ? "#ff4500" : "#7193ff";
                        }
                    }
                })
                .catch(error => console.error("Fout bij stemmen:", error));
        });
    });
});
