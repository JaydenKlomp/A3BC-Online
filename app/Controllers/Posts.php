<?php

namespace App\Controllers;
use App\Models\PostModel;
use App\Models\CommentModel;


class Posts extends BaseController
{
    public function index()
    {
        helper('time');
        $model = new PostModel();
        $commentModel = new CommentModel();

        $sort = $this->request->getGet('sort') ?? 'hot';

        switch ($sort) {
            case 'new':
                $posts = $model->orderBy('created_at', 'DESC')->findAll();
                break;
            case 'top':
                $posts = $model->select('*, (upvotes - downvotes) AS score')
                    ->orderBy('score DESC', '', false) //
                    ->findAll();
                break;
            case 'rising':
                $posts = $model->select('*, (upvotes - downvotes) AS score')
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('score', 'DESC', false)
                    ->findAll();
                break;
            default:
                $posts = $model->select('*, (upvotes - downvotes) AS score')
                    ->orderBy('score', 'DESC', false)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
                break;
        }

        foreach ($posts as &$post) {
            $post['comment_count'] = $commentModel->where('post_id', $post['id'])->countAllResults();
        }

        $data['posts'] = $posts;
        $data['currentSort'] = ucfirst($sort); // ✅ Pass the current sorting method for UI
        return view('posts/index', $data);
    }



    public function create()
    {
        return view('posts/create');
    }

    public function store()
    {
        $model = new PostModel();
        $postData = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content') ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Handle Image Upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads', $newName);
            $postData['image'] = $newName;
        }

        // Handle Link Submission
        if ($this->request->getPost('link')) {
            $postData['link'] = $this->request->getPost('link');
        }

        $model->save($postData);
        return redirect()->to('/posts');
    }


    public function vote()
    {
        $postId = $this->request->getPost('post_id');
        $voteType = $this->request->getPost('vote_type'); // 'upvotes' or 'downvotes'
        $action = $this->request->getPost('action'); // 'add' or 'remove'

        $model = new PostModel();
        $post = $model->find($postId);

        if (!$post) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid post ID']);
        }

        if ($action === 'add') {
            $model->incrementVote($postId, $voteType);
        } elseif ($action === 'remove') {
            $model->decrementVote($postId, $voteType);
        }

        $updatedPost = $model->find($postId);
        return $this->response->setJSON([
            'success' => true,
            'upvotes' => $updatedPost['upvotes'],
            'downvotes' => $updatedPost['downvotes']
        ]);
    }

    public function voteComment()
    {
        $commentId = $this->request->getPost('comment_id');
        $voteType = $this->request->getPost('vote_type'); // 'upvotes' or 'downvotes'
        $action = $this->request->getPost('action'); // 'add' or 'remove'

        $model = new CommentModel();
        $comment = $model->find($commentId);

        if (!$comment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid comment ID']);
        }

        if ($action === 'add') {
            $model->incrementVote($commentId, $voteType);
        } elseif ($action === 'remove') {
            $model->decrementVote($commentId, $voteType);
        }

        $updatedComment = $model->find($commentId);
        return $this->response->setJSON([
            'success' => true,
            'upvotes' => $updatedComment['upvotes'],
            'downvotes' => $updatedComment['downvotes']
        ]);
    }


    public function addComment()
    {
        $commentModel = new CommentModel();

        $commentModel->save([
            'post_id' => $this->request->getPost('post_id'),
            'parent_id' => $this->request->getPost('parent_id') ?? null,
            'content' => $this->request->getPost('content'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/posts/' . $this->request->getPost('post_id'));
    }

    public function view($id)
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        $data['post'] = $postModel->find($id);

        // Fetch nested comments using the function from CommentModel
        $data['comments'] = $commentModel->getCommentsWithReplies($id); // ✅ FIXED

        // Fetch trending posts (based on most upvoted)
        $data['trendingPosts'] = $postModel->orderBy('upvotes', 'DESC')->limit(5)->findAll();

        return view('posts/view', $data);
    }

    public function landing()
    {
        helper('time');
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        // Get featured posts (random selection)
        $data['featured_posts'] = $postModel->orderBy('created_at', 'DESC')->limit(3)->findAll();

        // Get trending posts (most upvoted)
        $data['trending_posts'] = $postModel->orderBy('upvotes', 'DESC')->limit(5)->findAll();

        // Get latest comments
        $data['latest_comments'] = $commentModel->orderBy('created_at', 'DESC')->limit(5)->findAll();

        // Get total post and comment counts
        $data['total_posts'] = $postModel->countAll(); // ✅ FIXED
        $data['total_comments'] = $commentModel->countAll(); // ✅ FIXED

        return view('landing', $data); // ✅ Ensures all data is passed
    }




}
