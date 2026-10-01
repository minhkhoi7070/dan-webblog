<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Perform pre-authorization checks.
     * Locked users are blocked from all actions.
     * Administrators have global comment management access.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_locked) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        return ! $user->is_locked;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Determine whether the user can delete the comment.
     * Allowed: Comment author, Post author (moderating own post), or Administrator.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id
            || $user->id === $comment->post?->user_id;
    }

    /**
     * Determine whether the user can moderate comments on a post.
     * Allowed: Post author (on own post) or Administrator.
     */
    public function moderate(User $user, Comment $comment): bool
    {
        return $user->id === $comment->post?->user_id;
    }
}
