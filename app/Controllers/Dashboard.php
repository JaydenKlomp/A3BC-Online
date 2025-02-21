<?php

namespace App\Controllers;
use App\Models\PostModel;
use App\Models\CommentModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        // Haal totale statistieken op
        $data['totalPosts'] = $postModel->countAll();
        $data['totalComments'] = $commentModel->countAll();
        $data['totalUpvotes'] = $postModel->selectSum('upvotes')->first()['upvotes'];
        $data['totalDownvotes'] = $postModel->selectSum('downvotes')->first()['downvotes'];

        return view('dashboard', $data);
    }

    public function getChartData()
    {
        $postModel = new PostModel();
        $commentModel = new CommentModel();

        // Get filter type from request (default to "daily")
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
