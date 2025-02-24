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

        $sort = $this->request->getGet('sort') ?? 'hot'; // Bepaalt de sorteermethode

        // Haal posts op met gebruikersinformatie
        $posts = $model->getPostsWithUser();

        // Voeg aantal reacties toe aan elke post
        foreach ($posts as &$post)
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
        $session = session(); // Sessie laden
        $model = new PostModel();

        $postData = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content') ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $session->get('user_id') // Haal de ingelogde gebruiker ID op
        ];

        // Verwerk afbeelding (indien toegevoegd)
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads', $newName);
            $postData['image'] = $newName;
        }

        // Verwerk link (indien toegevoegd)
        if ($this->request->getPost('link')) {
            $postData['link'] = $this->request->getPost('link');
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
    public function landing()
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        // Haal featured posts op met gebruikersinformatie
        $data['featured_posts'] = $postModel->getFeaturedPosts();

        // Haal trending posts op met gebruikersinformatie
        $data['trending_posts'] = $postModel->getTrendingPosts();

        // Haal de nieuwste reacties op
        $data['latest_comments'] = $commentModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        // Tel het totaal aantal posts en reacties
        $data['total_posts'] = $postModel->countAll();
        $data['total_comments'] = $commentModel->countAll();

        return view('landing', $data);
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
}
