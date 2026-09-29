<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    /**
     * Toggle like status for a post.
     *
     * Response exactly:
     * {
     *   "liked": true,
     *   "likes_count": 25
     * }
     */
    public function like(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->is_locked, 403, 'Account is locked.');
        abort_if($post->status !== 'published', 404, 'Post not found.');

        $isLiked = $post->likers()->where('user_id', $user->id)->exists();

        if ($isLiked) {
            $post->likers()->detach($user->id);
            $liked = false;
        } else {
            // syncWithoutDetaching ensures duplicate like requests are idempotent
            $post->likers()->syncWithoutDetaching([$user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $post->likers()->count(),
        ]);
    }

    /**
     * Toggle favorite/saved status for a post.
     *
     * Response exactly:
     * {
     *   "saved": true
     * }
     */
    public function favorite(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->is_locked, 403, 'Account is locked.');
        abort_if($post->status !== 'published', 404, 'Post not found.');

        $isSaved = $post->favoritedBy()->where('user_id', $user->id)->exists();

        if ($isSaved) {
            $post->favoritedBy()->detach($user->id);
            $saved = false;
        } else {
            $post->favoritedBy()->syncWithoutDetaching([$user->id]);
            $saved = true;
        }

        return response()->json([
            'saved' => $saved,
        ]);
    }

    /**
     * Toggle follow status for an author.
     *
     * Response exactly:
     * {
     *   "following": true
     * }
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();
        abort_if(! $currentUser, 401, 'Unauthenticated.');
        abort_if($currentUser->is_locked, 403, 'Account is locked.');

        // Rule: Cannot follow self
        if ($currentUser->id === $user->id) {
            return response()->json([
                'message' => 'Cannot follow self.',
            ], 422);
        }

        $isFollowing = $currentUser->following()->where('following_id', $user->id)->exists();

        if ($isFollowing) {
            $currentUser->following()->detach($user->id);
            $following = false;
        } else {
            $currentUser->following()->syncWithoutDetaching([$user->id]);
            $following = true;
        }

        return response()->json([
            'following' => $following,
        ]);
    }
}
