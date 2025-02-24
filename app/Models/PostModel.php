<?php

namespace App\Models;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts'; // Tabelnaam in de database
    protected $primaryKey = 'id'; // Primaire sleutel van de tabel
    protected $allowedFields = ['title', 'content', 'image', 'link', 'created_at', 'upvotes', 'downvotes', 'user_id'];

    /**
     * Haal alle posts op met bijbehorende gebruikersinformatie
     */
    public function getPostsWithUser()
    {
        return $this->select('posts.*, users.username, users.role')
            ->join('users', 'users.id = posts.user_id', 'left')
            ->orderBy('posts.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Haal een specifieke post op met bijbehorende gebruikersinformatie
     */
    public function getPostWithUserById($id)
    {
        return $this->select('posts.*, users.username, users.role')
            ->join('users', 'users.id = posts.user_id', 'left')
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
}
