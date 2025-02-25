<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\CommunityModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $postModel = new PostModel();
        $commentModel = new CommentModel();
        $userModel = new UserModel();
        $communityModel = new CommunityModel();

        $data['totalPosts'] = $postModel->countAll();
        $data['totalComments'] = $commentModel->countAll();
        $data['totalUpvotes'] = $postModel->selectSum('upvotes')->first()['upvotes'];
        $data['totalDownvotes'] = $postModel->selectSum('downvotes')->first()['downvotes'];
        $data['totalUsers'] = $userModel->countAll();
        $data['totalCommunities'] = $communityModel->countAll();

        $data['users'] = $userModel->findAll();
        $data['posts'] = $postModel->getPostsWithUser();
        $data['comments'] = $commentModel->getAllCommentsWithUser();
        $data['communities'] = $communityModel->findAll();

        return view('dashboard', $data);
    }

    public function deleteUser()
    {
        $userModel = new UserModel();
        $json = $this->request->getJSON();

        if (!isset($json->id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid user ID'])->setStatusCode(400);
        }

        $userModel->delete($json->id);
        return $this->response->setJSON(['success' => true, 'message' => 'User deleted']);
    }

    public function deletePost()
    {
        $postModel = new PostModel();
        $json = $this->request->getJSON();

        if (!isset($json->id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid post ID'])->setStatusCode(400);
        }

        $postModel->delete($json->id);
        return $this->response->setJSON(['success' => true, 'message' => 'Post deleted']);
    }

    public function deleteComment()
    {
        $commentModel = new CommentModel();
        $json = $this->request->getJSON();

        if (!isset($json->id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid comment ID'])->setStatusCode(400);
        }

        $commentModel->delete($json->id);
        return $this->response->setJSON(['success' => true, 'message' => 'Comment deleted']);
    }

    public function deleteCommunity()
    {
        $communityModel = new CommunityModel();
        $json = $this->request->getJSON();

        if (!isset($json->id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid community ID'])->setStatusCode(400);
        }

        $communityModel->delete($json->id);
        return $this->response->setJSON(['success' => true, 'message' => 'Community deleted']);
    }

    public function getChartData()
    {
        $postModel = new PostModel();

        $filter = $this->request->getGet('filter') ?? 'daily';

        switch ($filter) {
            case 'weekly':
                $posts = $postModel->select("YEARWEEK(created_at) as period, COUNT(*) as total")
                    ->groupBy('period')
                    ->orderBy('period', 'ASC')
                    ->findAll();
                break;
            case 'monthly':
                $posts = $postModel->select("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
                    ->groupBy('period')
                    ->orderBy('period', 'ASC')
                    ->findAll();
                break;
            default: // Daily
                $posts = $postModel->select("DATE(created_at) as period, COUNT(*) as total")
                    ->groupBy('period')
                    ->orderBy('period', 'ASC')
                    ->findAll();
                break;
        }

        return $this->response->setJSON(['posts' => $posts]);
    }




}
