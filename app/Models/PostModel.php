<?php

namespace App\Models;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts'; // Tabelnaam in de database
    protected $primaryKey = 'id'; // Primaire sleutel van de tabel
    protected $allowedFields = ['title', 'content', 'image', 'link', 'created_at', 'upvotes', 'downvotes', 'user_id', 'image', 'community_id'];

    // Enable automatic timestamps

    protected $createdField  = 'created_at'; // ✅ Tell CI to use "created_at"

    /**
     * Haal alle posts op met bijbehorende gebruikersinformatie
     */
    public function getPostsWithUser()
    {
        return $this->select('posts.*, users.username, users.role, communities.name as community_name')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->join('communities', 'communities.id = posts.community_id', 'left')
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Haal een specifieke post op met bijbehorende gebruikersinformatie
     */
    public function getPostWithUserById($id)
    {
        return $this->select('posts.*, users.username, users.role, communities.name as community_name')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->join('communities', 'communities.id = posts.community_id', 'left')
            ->where('posts.id', $id)
            ->first();
    }

    /**
     * Haal trending posts op met gebruikersinformatie
     */
    public function getTrendingPosts()
    {
        return $this->select('posts.*, users.username, users.role')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->orderBy('posts.upvotes', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Haal featured posts op met gebruikersinformatie
     */
    public function getFeaturedPosts()
    {
        return $this->select('posts.*, users.username, users.role')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->orderBy('posts.created_at', 'DESC')
            ->limit(3)
            ->findAll();
    }

    public function updateUserKarma($userId)
    {
        $karmaData = $this->select('SUM(upvotes) as total_upvotes, SUM(downvotes) as total_downvotes')
            ->where('user_id', $userId)
            ->first();

        $karma = ($karmaData['total_upvotes'] ?? 0) - ($karmaData['total_downvotes'] ?? 0);

        return $this->db->table('users')
            ->where('id', $userId)
            ->set('karma', $karma)
            ->update();
    }

    public function incrementVote($postId, $voteType)
    {
        return $this->db->table($this->table)
            ->set($voteType, $voteType . ' + 1', false)
            ->where('id', $postId)
            ->update();
    }

    public function decrementVote($postId, $voteType)
    {
        return $this->db->table($this->table)
            ->set($voteType, $voteType . ' - 1', false)
            ->where('id', $postId)
            ->update();
    }

    // ✅ Delete a post and all associated comments
    public function deletePostWithComments($postId)
    {
        $commentModel = new \App\Models\CommentModel();
        $commentModel->where('post_id', $postId)->delete();

        return $this->delete($postId);
    }

    public function getPostsByCommunity($communityId)
    {
        return $this->select('posts.*, users.username, users.role, communities.name as community_name')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->join('communities', 'communities.id = posts.community_id', 'left')
            ->where('posts.community_id', $communityId)
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();
    }



}
