<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a summary of recent activity by the authenticated viewer.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Comments made by user
        $comments = $user->comments()
            ->with(['post'])
            ->latest()
            ->take(10)
            ->get();

        // 2. Posts liked by user
        $likedPosts = $user->likedPosts()
            ->published()
            ->with(['user', 'category'])
            ->latest('likes.created_at')
            ->take(10)
            ->get();

        // 3. Posts favorited by user
        $favoritedPosts = $user->favoritePosts()
            ->published()
            ->with(['user', 'category'])
            ->latest('favorites.created_at')
            ->take(10)
            ->get();

        // 4. Authors followed by user
        $following = $user->following()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->latest('follows.created_at')
            ->take(10)
            ->get();

        $user->loadCount(['comments', 'likedPosts', 'favoritePosts', 'following']);

        return view('activity.index', [
            'comments' => $comments,
            'likedPosts' => $likedPosts,
            'favoritedPosts' => $favoritedPosts,
            'following' => $following,
            'stats' => [
                'comments_count' => (int) $user->comments_count,
                'likes_count' => (int) $user->liked_posts_count,
                'favorites_count' => (int) $user->favorite_posts_count,
                'following_count' => (int) $user->following_count,
            ],
        ]);
    }
}
