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

        if ($postModel->db->fieldExists('user_id', 'posts')) {
            $posts = $postModel->where('user_id', $user['id'])->orderBy('created_at', 'DESC')->findAll();
        }

        if ($commentModel->db->fieldExists('user_id', 'comments')) {
            $comments = $commentModel->where('user_id', $user['id'])->orderBy('created_at', 'DESC')->findAll();
        }

        $data = [
            'user' => $user,
            'posts' => $posts,
            'comments' => $comments
        ];

        return view('profile', $data);
    }

}
