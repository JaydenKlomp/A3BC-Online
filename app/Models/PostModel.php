<?php

namespace App\Models;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts'; // Tabelnaam in de database
    protected $primaryKey = 'id'; // Primaire sleutel van de tabel
    protected $allowedFields = ['title', 'content', 'image', 'link', 'created_at', 'upvotes', 'downvotes', 'user_id'];


    /**
     * Verhoogt het aantal upvotes of downvotes voor een post.
     *
     * @param int $postId De ID van de post
     * @param string $voteType 'upvotes' of 'downvotes'
     */
    public function incrementVote($postId, $voteType)
    {
        $this->set($voteType, "$voteType + 1", false)
            ->where('id', $postId)
            ->update();
    }

    public function decrementVote($postId, $voteType)
    {
        $this->set($voteType, "$voteType - 1", false)
            ->where('id', $postId)
            ->update();
    }

    public function getPostsByUser($userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }


}
