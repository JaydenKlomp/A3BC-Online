<?php
namespace App\Controllers;
use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\CommunityModel;
use CodeIgniter\Controller;


class Posts extends BaseController
{

    public function landing()
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();
        $communityModel = new CommunityModel();
        $session = session();

        // ✅ Fetch all posts with user & community info
        $data['posts'] = $postModel->select('posts.*, users.username, users.role, communities.id as community_id, communities.name as community_name')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->join('communities', 'communities.id = posts.community_id', 'left')
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();

        $sort = $this->request->getGet('sort') ?? 'hot';

        if ($sort === 'new') {
            $orderBy = 'posts.created_at DESC';
        } elseif ($sort === 'top') {
            $orderBy = '(posts.upvotes - posts.downvotes) DESC'; // Sort by net votes
        } elseif ($sort === 'rising') {
            $orderBy = '(posts.upvotes - posts.downvotes) DESC, posts.created_at DESC'; // Net votes + newest first
        } else { // Default: "Hot" sorting
            $orderBy = '(posts.upvotes - posts.downvotes) DESC, posts.created_at DESC';
        }

        $data['currentSort'] = ucfirst($sort);
        $data['posts'] = $postModel->select('posts.*, users.username, users.role, communities.id as community_id, communities.name as community_name, (posts.upvotes - posts.downvotes) as net_votes')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->join('communities', 'communities.id = posts.community_id', 'left')
            ->orderBy($orderBy)
            ->findAll();



        // ✅ Fetch featured posts
        $data['featured_posts'] = $postModel->getFeaturedPosts();

        // ✅ Fetch trending posts
        $data['trending_posts'] = $postModel->getTrendingPosts();

        // ✅ Fetch latest comments with community info
        $data['latest_comments'] = $commentModel->select('comments.*, posts.id as post_id, communities.id as community_id, communities.name as community_name')
            ->join('posts', 'posts.id = comments.post_id')
            ->join('communities', 'communities.id = posts.community_id', 'left')
            ->orderBy('comments.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // ✅ Fetch total counts
        $data['total_posts'] = $postModel->countAll();
        $data['total_comments'] = $commentModel->countAll();
        $data['total_communities'] = $communityModel->countAll();

        // ✅ Fetch all communities for the left sidebar
        $data['communities'] = $communityModel->orderBy('name', 'ASC')->findAll();

        // ✅ Manage Recent Communities (Session-based)
        $recentCommunities = $session->get('recent_communities') ?? [];
        if (!empty($recentCommunities)) {
            $data['recent_communities'] = $communityModel
                ->whereIn('id', $recentCommunities)
                ->orderBy('FIELD(id, ' . implode(',', $recentCommunities) . ')')
                ->findAll();
        } else {
            $data['recent_communities'] = [];
        }

        return view('landing', $data);
    }
    // Toont de pagina om een post te maken
    public function create()
    {
        $communityModel = new \App\Models\CommunityModel();
        $data['communities'] = $communityModel->findAll();

        return view('posts/create', $data);
    }



    // Verwerkt het opslaan van een nieuwe post
    public function store()
    {
        $session = session();
        $postModel = new PostModel();
        $communityModel = new \App\Models\CommunityModel();
        $userId = $session->get('user_id');
        $communityId = $this->request->getPost('community_id');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in to post.');
        }

        if (!$communityModel->find($communityId)) {
            return redirect()->to('/posts/create')->with('error', 'Selected community does not exist.');
        }

        $postData = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'user_id' => $userId,
            'community_id' => $communityId,
            'created_at' => date('Y-m-d H:i:s') // ✅ Store current timestamp
        ];

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            if ($image->getSize() > 5 * 1024 * 1024) { // ✅ Max 5MB file size
                return redirect()->to('/posts/create')->with('error', 'Image is too large. Max: 5MB');
            }

            $newName = $image->getRandomName();
            if (!$image->move(FCPATH . 'images/posts', $newName)) {
                return redirect()->to('/posts/create')->with('error', 'Failed to upload image.');
            }
            $postData['image'] = $newName;
        }

        if ($postModel->insert($postData)) {
            return redirect()->to('/')->with('success', 'Post created successfully!');
        } else {
            return redirect()->to('/posts/create')->with('error', 'Failed to create post.');
        }
    }



    // Verwerkt upvotes en downvotes op posts
    public function vote()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $postId = $this->request->getPost('post_id');
        $voteType = $this->request->getPost('vote_type'); // 'upvotes' or 'downvotes'
        $action = $this->request->getPost('action'); // 'add', 'remove', 'switch'

        if (!$postId || !$voteType || !$action) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request parameters']);
        }

        $model = new PostModel();
        $post = $model->find($postId);

        if (!$post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid post ID']);
        }

        $userId = $post['user_id']; // Get the post owner

        try {
            if ($action === 'add') {
                $model->incrementVote($postId, $voteType);
                $_SESSION['user_votes'][$postId] = $voteType;
            } elseif ($action === 'remove') {
                $model->decrementVote($postId, $voteType);
                unset($_SESSION['user_votes'][$postId]);
            } elseif ($action === 'switch') {
                $oppositeVoteType = ($voteType === 'upvotes') ? 'downvotes' : 'upvotes';
                $model->decrementVote($postId, $oppositeVoteType);
                $model->incrementVote($postId, $voteType);
                $_SESSION['user_votes'][$postId] = $voteType;
            }

            // Update user karma after vote
            $model->updateUserKarma($userId);

            $updatedPost = $model->find($postId);

            return $this->response->setJSON([
                'success' => true,
                'upvotes' => $updatedPost['upvotes'],
                'downvotes' => $updatedPost['downvotes']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Vote error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Vote failed']);
        }
    }


    // Voegt een nieuwe reactie toe aan een post of een andere reactie
    public function addComment()
    {
        $session = session();
        $commentModel = new CommentModel();

        $commentModel->save([
            'post_id' => $this->request->getPost('post_id'),
            'parent_id' => $this->request->getPost('parent_id') ?? null,
            'content' => $this->request->getPost('content'),
            'user_id' => $session->get('user_id'), // Sla de user_id op in de database
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/posts/' . $this->request->getPost('post_id'));
    }

    // Toont een enkele post met reacties
    public function view($id)
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        $data['post'] = $postModel->getPostWithUserById($id);
        $data['comments'] = $commentModel->getCommentsWithUser($id); // Zorgt voor gebruikersinformatie bij reacties

        // Haal trending posts op (meest upvoted)
        $data['trendingPosts'] = $postModel->orderBy('upvotes', 'DESC')
            ->limit(5)
            ->findAll();

        return view('posts/view', $data);
    }


    // Haalt gegevens op voor de landingspagina



    // Verwerkt upvotes en downvotes op reacties
    public function voteComment()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $commentId = $this->request->getPost('comment_id');
        $voteType = $this->request->getPost('vote_type'); // 'upvotes' or 'downvotes'
        $action = $this->request->getPost('action'); // 'add', 'remove', 'switch'

        $model = new CommentModel();
        $comment = $model->find($commentId);

        if (!$comment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ongeldige comment ID']);
        }

        // Get current vote state
        $userVote = $_SESSION['user_votes'][$commentId] ?? null;

        if ($action === 'add') {
            $model->incrementVote($commentId, $voteType);
            $_SESSION['user_votes'][$commentId] = $voteType; // Store user vote
        } elseif ($action === 'remove') {
            $model->decrementVote($commentId, $voteType);
            unset($_SESSION['user_votes'][$commentId]); // Remove vote state
        } elseif ($action === 'switch') {
            $oppositeVoteType = ($voteType === 'upvotes') ? 'downvotes' : 'upvotes';
            $model->decrementVote($commentId, $oppositeVoteType); // Remove previous vote
            $model->incrementVote($commentId, $voteType); // Add new vote
            $_SESSION['user_votes'][$commentId] = $voteType;
        }

        $updatedComment = $model->find($commentId);
        return $this->response->setJSON([
            'success' => true,
            'upvotes' => $updatedComment['upvotes'],
            'downvotes' => $updatedComment['downvotes']
        ]);
    }

    public function delete($postId)
    {
        $session = session();
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        $userId = $session->get('user_id');

        // Get post details
        $post = $postModel->find($postId);
        if (!$post) {
            return redirect()->to('/')->with('error', 'Post not found.');
        }

        // Check if the logged-in user is the owner or an admin
        if ($post['user_id'] !== $userId && $session->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'You do not have permission to delete this post.');
        }

        // ✅ Delete all comments associated with the post
        $commentModel->where('post_id', $postId)->delete();

        // ✅ Delete the post itself
        if ($postModel->delete($postId)) {
            return redirect()->to('/')->with('success', 'Post deleted successfully.');
        } else {
            return redirect()->to('/posts/' . $postId)->with('error', 'Failed to delete post.');
        }
    }

    public function community($communityId)
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();
        $communityModel = new \App\Models\CommunityModel();

        // ✅ Check if community exists
        $community = $communityModel->find($communityId);
        if (!$community) {
            return redirect()->to('/')->with('error', 'Community not found.');
        }

        // ✅ Get posts related to this community
        $posts = $postModel->getPostsByCommunity($communityId);

        // ✅ Add comment count for each post
        foreach ($posts as &$post) {
            $post['comment_count'] = $commentModel->where('post_id', $post['id'])->countAllResults();
        }

        $data = [
            'posts' => $posts,
            'community' => $community,
        ];

        return view('community/community', $data);
    }



    public function viewCommunity($communityId)
    {
        $session = session();
        $communityModel = new CommunityModel();
        $postModel = new PostModel();
        helper('time');

        // ✅ Fetch community info
        $community = $communityModel->find($communityId);
        if (!$community) {
            return redirect()->to('/')->with('error', 'Community not found.');
        }

        // ✅ Fetch posts for this community
        $posts = $postModel->getPostsByCommunity($communityId);

        // ✅ Store recent community visit in session
        $recentCommunities = $session->get('recent_communities') ?? [];
        $recentCommunities = array_unique(array_merge([$communityId], $recentCommunities));
        if (count($recentCommunities) > 5) {
            array_pop($recentCommunities); // Keep only the last 5
        }
        $session->set('recent_communities', $recentCommunities);

        return view('community/community', ['community' => $community, 'posts' => $posts]);
    }


}
