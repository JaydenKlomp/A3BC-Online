<?php

namespace App\Controllers;
use App\Models\UserModel;
use App\Models\PostModel;
use App\Models\CommentModel;

class Profile extends BaseController
{
    public function index($username)
    {
        $userModel = new UserModel();
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->to('/')->with('error', 'User not found.');
        }

        $posts = [];
        $comments = [];

        // ✅ Fetch user posts
        if ($postModel->db->fieldExists('user_id', 'posts')) {
            $posts = $postModel->where('user_id', $user['id'])->orderBy('created_at', 'DESC')->findAll();
        }

        // ✅ Fetch user comments & join with post title
        if ($commentModel->db->fieldExists('user_id', 'comments')) {
            $comments = $commentModel->select('comments.*, posts.title as post_title')
                ->join('posts', 'posts.id = comments.post_id', 'left')
                ->where('comments.user_id', $user['id'])
                ->orderBy('comments.created_at', 'DESC')
                ->findAll();
        }

        $data = [
            'user' => $user,
            'posts' => $posts,
            'comments' => $comments
        ];

        return view('profile', $data);
    }


}
