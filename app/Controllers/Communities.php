<?php

namespace App\Controllers;

use App\Models\CommunityModel;
use App\Models\PostModel;
use CodeIgniter\Controller;

class Communities extends BaseController
{
    public function index()
    {
        $communityModel = new CommunityModel();
        $data['communities'] = $communityModel->getCommunities();

        return view('community/communities', $data);
    }

    public function view($id)
    {
        $communityModel = new CommunityModel();
        $postModel = new PostModel();

        $community = $communityModel->getCommunityById($id);
        if (!$community) {
            return redirect()->to('/communities')->with('error', 'Community not found.');
        }

        $data['community'] = $community;
        $data['posts'] = $communityModel->getPostsByCommunity($id);

        return view('community/community', $data);
    }

    public function create()
    {
        $session = session();
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/communities')->with('error', 'Only admins can create communities.');
        }

        return view('community/create_community');
    }

    public function store()
    {
        $session = session();
        $userId = $session->get('user_id'); // Ensure the user ID is set

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/')->with('error', 'Only admins can create communities.');
        }

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in to create a community.');
        }

        $communityModel = new \App\Models\CommunityModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'rules' => json_encode(explode("\n", trim($this->request->getPost('rules')))), // Store rules as JSON
            'created_by' => $userId, // ✅ Ensure the creator's ID is set
        ];

        // Handle Banner Upload
        $banner = $this->request->getFile('banner');
        if ($banner && $banner->isValid() && !$banner->hasMoved()) {
            $newName = $banner->getRandomName();
            $banner->move(FCPATH . 'images/communitybanners', $newName);
            $data['banner'] = $newName;
        }

        // Handle Profile Picture Upload
        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture && $profilePicture->isValid() && !$profilePicture->hasMoved()) {
            $newName = $profilePicture->getRandomName();
            $profilePicture->move(FCPATH . 'images/communitypictures', $newName);
            $data['profile_picture'] = $newName;
        }

        $communityModel->insert($data);

        return redirect()->to('/communities')->with('success', 'Community created successfully!');
    }


}
