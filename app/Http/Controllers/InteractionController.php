<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function likeState(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->is_locked, 403, 'Account is locked.');
        abort_if($post->status !== 'published', 404, 'Post not found.');

        return response()->json([
            'liked' => $post->likers()->where('user_id', $user->id)->exists(),
            'likes_count' => $post->likers()->count(),
        ]);
    }

    public function favoriteState(Request $request, Post $post): JsonResponse
    {
        $user = $request->user();
        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->is_locked, 403, 'Account is locked.');
        abort_if($post->status !== 'published', 404, 'Post not found.');

        return response()->json([
            'saved' => $post->favoritedBy()->where('user_id', $user->id)->exists(),
        ]);
    }

    public function followState(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();
        abort_if(! $currentUser, 401, 'Unauthenticated.');
        abort_if($currentUser->is_locked, 403, 'Account is locked.');

        return response()->json([
            'following' => $currentUser->following()->where('following_id', $user->id)->exists(),
            'followers_count' => $user->followers()->count(),
        ]);
    }

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
        if ($request->isMethod('GET')) {
            return $this->likeState($request, $post);
        }

        $user = $request->user();
        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->is_locked, 403, 'Account is locked.');
        abort_if($post->status !== 'published', 404, 'Post not found.');

        $request->validate(['liked' => ['sometimes', 'required', 'boolean']]);
        $isLiked = $post->likers()->where('user_id', $user->id)->exists();
        $liked = $request->has('liked') ? $request->boolean('liked') : ! $isLiked;

        if (! $liked) {
            $post->likers()->detach($user->id);
        } else {
            $post->likes()->firstOrCreate(['user_id' => $user->id]);
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
        if ($request->isMethod('GET')) {
            return $this->favoriteState($request, $post);
        }

        $user = $request->user();
        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->is_locked, 403, 'Account is locked.');
        abort_if($post->status !== 'published', 404, 'Post not found.');

        $request->validate(['saved' => ['sometimes', 'required', 'boolean']]);
        $isSaved = $post->favoritedBy()->where('user_id', $user->id)->exists();
        $saved = $request->has('saved') ? $request->boolean('saved') : ! $isSaved;

        if (! $saved) {
            $post->favoritedBy()->detach($user->id);
        } else {
            $post->favorites()->firstOrCreate(['user_id' => $user->id]);
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
        if ($request->isMethod('GET')) {
            return $this->followState($request, $user);
        }

        $currentUser = $request->user();
        abort_if(! $currentUser, 401, 'Unauthenticated.');
        abort_if($currentUser->is_locked, 403, 'Account is locked.');

        // Rule: Cannot follow self
        if ($currentUser->id === $user->id) {
            return response()->json([
                'message' => 'Cannot follow self.',
            ], 422);
        }

        $request->validate(['following' => ['sometimes', 'required', 'boolean']]);
        $isFollowing = $currentUser->following()->where('following_id', $user->id)->exists();
        $following = $request->has('following') ? $request->boolean('following') : ! $isFollowing;

        if (! $following) {
            $currentUser->following()->detach($user->id);
        } else {
            $currentUser->followingRelations()->firstOrCreate(['following_id' => $user->id]);
        }

        return response()->json([
            'following' => $following,
        ]);
    }
}
