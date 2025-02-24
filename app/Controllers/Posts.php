<?php
namespace App\Controllers;
use App\Models\PostModel;
use App\Models\CommentModel;

class Posts extends BaseController
{
    // Haalt alle posts op en sorteert ze op basis van de geselecteerde filter
    public function index()
    {
        helper('time');
        $model = new PostModel();
        $commentModel = new CommentModel();

        $sort = $this
            ->request
            ->getGet('sort') ?? 'hot'; // Bepaalt de sorteermethode
        // Sorteer posts op basis van de geselecteerde methode
        switch ($sort)
        {
            case 'new' : $posts = $model->orderBy('created_at', 'DESC')
                ->findAll();
                break;
            case 'top' : $posts = $model->select('*, (upvotes - downvotes) AS score')
                ->orderBy('score DESC', '', false)
                ->findAll();
                break;
            case 'rising':
                $posts = $model->select('*, (upvotes - downvotes) AS score')
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('score', 'DESC', false)
                    ->findAll();
                break;
            default: // Standaard sortering (hot)
                $posts = $model->select('*, (upvotes - downvotes) AS score')
                    ->orderBy('score', 'DESC', false)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
                break;
        }

// Voeg aantal reacties toe aan elke post
        foreach ($posts as & $post)
        {
            $post['comment_count'] = $commentModel->where('post_id', $post['id'])->countAllResults();
        }

        $data['posts'] = $posts;
        $data['currentSort'] = ucfirst($sort); // Houd de huidige sorteermethode bij in de UI
        return view('posts/index', $data);
    }

// Toont de pagina om een post te maken
    public function create()
    {
        return view('posts/create');
    }

// Verwerkt het opslaan van een nieuwe post
    public function store()
    {
        $model = new PostModel();
        $postData = ['title' => $this
            ->request
            ->getPost('title') , 'content' => $this
            ->request
            ->getPost('content') ?? '', 'created_at' => date('Y-m-d H:i:s') , 'user_id' => $session->get('user_id')];


        // Verwerk afbeelding (indien toegevoegd)
        $image = $this
            ->request
            ->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved())
        {
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads', $newName);
            $postData['image'] = $newName;
        }

        // Verwerk link (indien toegevoegd)
        if ($this
            ->request
            ->getPost('link'))
        {
            $postData['link'] = $this
                ->request
                ->getPost('link');
        }

        $model->save($postData);
        return redirect()->to('/posts');
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

        $model = new PostModel();
        $post = $model->find($postId);

        if (!$post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ongeldige post ID']);
        }

        // Get current vote state
        $userVote = $_SESSION['user_votes'][$postId] ?? null;

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

        $updatedPost = $model->find($postId);
        return $this->response->setJSON([
            'success' => true,
            'upvotes' => $updatedPost['upvotes'],
            'downvotes' => $updatedPost['downvotes']
        ]);
    }




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


// Voegt een nieuwe reactie toe aan een post of een andere reactie
    public function addComment()
    {
        $commentModel = new CommentModel();

        $commentModel->save(['post_id' => $this
            ->request
            ->getPost('post_id') , 'parent_id' => $this
            ->request
            ->getPost('parent_id') ?? null, 'content' => $this
            ->request
            ->getPost('content') , 'created_at' => date('Y-m-d H:i:s') , ]);

        return redirect()
            ->to('/posts/' . $this
                    ->request
                    ->getPost('post_id'));
    }

// Toont een enkele post met reacties
    public function view($id)
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        $data['post'] = $postModel->find($id);

        // Haal de reacties inclusief nested replies op
        $data['comments'] = $commentModel->getCommentsWithReplies($id);

        // Haal trending posts op (meest upvoted)
        $data['trendingPosts'] = $postModel->orderBy('upvotes', 'DESC')
            ->limit(5)
            ->findAll();

        return view('posts/view', $data);
    }

// Haalt gegevens op voor de landingspagina
    public function landing()
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        // Haal willekeurige uitgelichte posts op
        $data['featured_posts'] = $postModel->orderBy('created_at', 'DESC')
            ->limit(3)
            ->findAll();

        // Haal trending posts op (meest upvoted)
        $data['trending_posts'] = $postModel->orderBy('upvotes', 'DESC')
            ->limit(5)
            ->findAll();

        // Haal de nieuwste reacties op
        $data['latest_comments'] = $commentModel->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Tel het totaal aantal posts en reacties
        $data['total_posts'] = $postModel->countAll();
        $data['total_comments'] = $commentModel->countAll();

        return view('landing', $data);
    }
}

