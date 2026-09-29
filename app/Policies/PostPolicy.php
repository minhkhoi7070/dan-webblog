<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Perform pre-authorization checks.
     * Admin can manage all.
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
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Post $post): bool
    {
        if ($post->status === 'published') {
            return true;
        }

        return $user?->id === $post->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAuthor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can submit a post for review.
     */
    public function submit(User $user, Post $post): bool
    {
        return $user->id === $post->user_id
            && in_array($post->status, ['draft', 'rejected'], true);
    }

    /**
     * Determine whether the user can approve a post.
     */
    public function approve(User $user, Post $post): bool
    {
        return false; // Handled by before() for admin
    }

    /**
     * Determine whether the user can reject a post.
     */
    public function reject(User $user, Post $post): bool
    {
        return false; // Handled by before() for admin
    }
}
