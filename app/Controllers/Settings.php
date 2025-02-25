<?php

namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Settings extends Controller
{
    public function index()
    {
        $session = session();
        $userModel = new UserModel();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $data['user'] = $userModel->find($userId);
        return view('Profile/settings', $data);
    }

    public function updateAccount()
    {
        $session = session();
        $userModel = new UserModel();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $updateData = [
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($userId, $updateData);
        return redirect()->to('/settings')->with('success', 'Account updated!');
    }

    public function updateProfile()
    {
        $session = session();
        $userModel = new UserModel();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $updateData = [
            'username' => $this->request->getPost('display_name'),
            'bio' => $this->request->getPost('bio'),
            'social_links' => $this->request->getPost('social_links'),
        ];

        // 🚨 Handle Banner Upload
        $banner = $this->request->getFile('banner');
        if ($banner && $banner->isValid() && !$banner->hasMoved()) {
            if ($banner->getSize() > 5 * 1024 * 1024) { // Max 5MB
                return redirect()->to('/settings')->with('error', 'Banner is too large. Max: 5MB');
            }

            $newName = $banner->getRandomName();
            if (!$banner->move(FCPATH . 'images/banners', $newName)) {
                return redirect()->to('/settings')->with('error', 'Failed to upload banner.');
            }
            $updateData['banner'] = $newName;
        }

        // 🚨 Handle Profile Picture Upload
        $profilePicture = $this->request->getFile('profile_picture');
        if ($profilePicture && $profilePicture->isValid() && !$profilePicture->hasMoved()) {
            if ($profilePicture->getSize() > 5 * 1024 * 1024) { // Max 5MB
                return redirect()->to('/settings')->with('error', 'Profile picture is too large. Max: 5MB');
            }

            $newName = $profilePicture->getRandomName();
            if (!$profilePicture->move(FCPATH . 'images/profilepicture', $newName)) {
                return redirect()->to('/settings')->with('error', 'Failed to upload profile picture.');
            }

            $updateData['profile_picture'] = $newName;
            $session->set('profile_picture', $newName); // 🔥 Update session instantly
        }

        // 🚀 Update User & Handle Errors
        if ($userModel->update($userId, $updateData)) {
            return redirect()->to('/settings')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->to('/settings')->with('error', 'Failed to update profile.');
        }
    }





    public function deleteAccount()
    {
        $session = session();
        $userModel = new UserModel();
        $userId = $session->get('user_id');

        if (!$userId) {
            return redirect()->to('/login');
        }

        $userModel->delete($userId);
        $session->destroy();
        return redirect()->to('/');
    }

}

