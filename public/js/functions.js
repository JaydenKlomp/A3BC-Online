document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.post-container, .post-card').forEach(post => {
        let voteCount = post.querySelector('.vote-count');
        let upvote = post.querySelector('.upvote');
        let downvote = post.querySelector('.downvote');

        if (!voteCount || !upvote || !downvote) return;

        let postId = upvote.getAttribute('data-post-id');

        upvote.addEventListener('click', () => handleVote(postId, "upvotes", upvote, downvote, voteCount));
        downvote.addEventListener('click', () => handleVote(postId, "downvotes", downvote, upvote, voteCount));
    });

    function handleVote(postId, voteType, activeBtn, inactiveBtn, countElem) {
        let action = activeBtn.classList.contains("voted") ? "remove" : "add";

        if (action === "add") {
            activeBtn.classList.add("voted");
            inactiveBtn.classList.remove("voted");
        } else {
            activeBtn.classList.remove("voted");
        }

        fetch('/posts/vote', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}&vote_type=${voteType}&action=${action}`
        })
            .then(response => response.json())
            .then(data => {
                countElem.innerText = data.upvotes - data.downvotes;
            })
            .catch(error => console.error("Voting error:", error));
    }
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.vote-btn').forEach(button => {
        button.addEventListener('click', () => {
            let commentId = button.getAttribute('data-comment-id');
            let voteType = button.classList.contains('upvote') ? 'upvotes' : 'downvotes';
            let voteCountElem = document.getElementById(`comment-vote-count-${commentId}`);

            let action = button.classList.contains("voted") ? "remove" : "add";

            fetch('/posts/comment/vote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `comment_id=${commentId}&vote_type=${voteType}&action=${action}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        voteCountElem.innerText = data.upvotes - data.downvotes;

                        // Toggle colors and classes
                        if (action === "add") {
                            button.classList.add("voted");
                            button.style.color = voteType === "upvotes" ? "#ff4500" : "#7193ff";
                        } else {
                            button.classList.remove("voted");
                            button.style.color = "#818384";
                        }
                    }
                })
                .catch(error => console.error("Voting error:", error));
        });
    });
});
