<?php

namespace App\Models;
use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['post_id', 'content', 'created_at', 'upvotes', 'downvotes', 'parent_id', 'user_id'];

    public function incrementVote($commentId, $voteType)
    {
        $this->set($voteType, "$voteType + 1", false)
            ->where('id', $commentId)
            ->update();
    }

    public function decrementVote($commentId, $voteType)
    {
        $this->set($voteType, "$voteType - 1", false)
            ->where('id', $commentId)
            ->update();
    }


    public function getCommentsWithReplies($postId)
    {
        $comments = $this->where('post_id', $postId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->buildCommentTree($comments);
    }

    private function buildCommentTree($comments, $parentId = null)
    {
        $tree = [];
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parentId) {
                $comment['replies'] = $this->buildCommentTree($comments, $comment['id']);
                $tree[] = $comment;
            }
        }
        return $tree;
    }

    public function getCommentsByUser($userId)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getCommentsWithUser($postId)
    {
        return $this->select('comments.*, users.username, users.role')
            ->join('users', 'users.id = comments.user_id')
            ->where('post_id', $postId)
            ->orderBy('comments.created_at', 'ASC')
            ->findAll();
    }




}
