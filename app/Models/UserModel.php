<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'email', 'phone', 'password', 'username', 'role',
        'karma', 'followers', 'banner', 'bio', 'social_links',
        'is_verified', 'verification_token', 'profile_picture'
    ];
    protected $useTimestamps = true;

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    public function getUserByToken($token)
    {
        return $this->where('verification_token', $token)->first();
    }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }
}
