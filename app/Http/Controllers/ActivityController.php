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
        $filter = $request->query('type', 'all');

        // 1. Comments made by user
        $comments = $user->comments()
            ->with(['post.user', 'post.category'])
            ->latest()
            ->take(20)
            ->get();

        // 2. Posts liked by user
        $likedPosts = $user->likedPosts()
            ->published()
            ->with(['user', 'category'])
            ->withViewerState($user)
            ->latest('likes.created_at')
            ->take(20)
            ->get();

        // 3. Posts favorited by user
        $favoritedPosts = $user->favoritePosts()
            ->published()
            ->with(['user', 'category'])
            ->withViewerState($user)
            ->latest('favorites.created_at')
            ->take(20)
            ->get();

        // 4. Authors followed by user
        $following = $user->following()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->latest('follows.created_at')
            ->take(20)
            ->get();

        // Build unified chronological activity stream
        $activityList = collect();

        foreach ($comments as $comment) {
            if ($comment->post) {
                $activityList->push([
                    'id' => 'comment_'.$comment->id,
                    'type' => 'comments',
                    'action_title' => 'Bạn đã bình luận',
                    'created_at' => $comment->created_at,
                    'post' => $comment->post,
                    'comment' => $comment,
                ]);
            }
        }

        foreach ($likedPosts as $post) {
            $activityList->push([
                'id' => 'like_'.$post->id,
                'type' => 'likes',
                'action_title' => 'Bạn đã thích bài viết',
                'created_at' => $post->pivot?->created_at ?? $post->created_at,
                'post' => $post,
                'comment' => null,
            ]);
        }

        // Chronological sort: newest first
        $allActivities = $activityList->sortByDesc('created_at')->values();

        $activeFilter = in_array($filter, ['all', 'comments', 'likes'], true) ? $filter : 'all';

        $activities = match ($activeFilter) {
            'comments' => $allActivities->where('type', 'comments')->values(),
            'likes' => $allActivities->where('type', 'likes')->values(),
            default => $allActivities,
        };

        return view('activity.index', [
            'activities' => $activities,
            'allActivities' => $allActivities,
            'activeFilter' => $activeFilter,
            'comments' => $comments,
            'likedPosts' => $likedPosts,
            'favoritedPosts' => $favoritedPosts,
            'following' => $following,
            'stats' => [
                'comments_count' => $user->comments()->count(),
                'likes_count' => $user->likedPosts()->count(),
                'favorites_count' => $user->favoritePosts()->count(),
                'following_count' => $user->following()->count(),
            ],
        ]);
    }
}
