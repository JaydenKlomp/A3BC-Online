<?php

namespace App\Models;
use CodeIgniter\Model;

class CommunityModel extends Model
{
    protected $table = 'communities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'banner', 'profile_picture', 'rules', 'created_by'];


    public function getCommunities()
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    public function getCommunityById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function getPostsByCommunity($communityId)
    {
        return $this->db->table('posts')
            ->select('posts.*, users.username, users.role')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->where('posts.community_id', $communityId)
            ->orderBy('posts.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}
