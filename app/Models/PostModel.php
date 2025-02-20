<?php

namespace App\Models;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'content', 'image', 'link', 'created_at', 'upvotes', 'downvotes'];


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


}
