<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Central Dashboard.
     */
    public function index(Request $request): View
    {
        // 1. Users metrics (1 query)
        $userStats = User::toBase()
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN role = ? THEN 1 END) as authors,
                COUNT(CASE WHEN role = ? THEN 1 END) as viewers
            ', ['author', 'viewer'])
            ->first();

        // 2. Posts metrics & views (1 query)
        $postStats = Post::toBase()
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN status = ? THEN 1 END) as draft,
                COUNT(CASE WHEN status = ? THEN 1 END) as pending,
                COUNT(CASE WHEN status = ? THEN 1 END) as published,
                COUNT(CASE WHEN status = ? THEN 1 END) as rejected,
                COALESCE(SUM(views), 0) as total_views
            ', ['draft', 'pending', 'published', 'rejected'])
            ->first();

        // 3. Comments metrics (1 query)
        $commentStats = Comment::toBase()
            ->selectRaw('
                COUNT(*) as total,
                COUNT(CASE WHEN status = ? THEN 1 END) as pending,
                COUNT(CASE WHEN status = ? THEN 1 END) as spam,
                COUNT(CASE WHEN status IN (?, ?) THEN 1 END) as moderation
            ', ['pending', 'spam', 'pending', 'spam'])
            ->first();

        // Recent items requiring editorial attention
        $recentPendingPosts = Post::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $recentPendingComments = Comment::with(['user', 'post'])
            ->whereIn('status', ['pending', 'spam'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'metrics' => [
                'users' => (int) ($userStats->total ?? 0),
                'authors' => (int) ($userStats->authors ?? 0),
                'viewers' => (int) ($userStats->viewers ?? 0),
                'posts' => (int) ($postStats->total ?? 0),
                'draft' => (int) ($postStats->draft ?? 0),
                'pending' => (int) ($postStats->pending ?? 0),
                'published' => (int) ($postStats->published ?? 0),
                'rejected' => (int) ($postStats->rejected ?? 0),
                'comments' => (int) ($commentStats->total ?? 0),
                'pending_comments' => (int) ($commentStats->pending ?? 0),
                'spam_comments' => (int) ($commentStats->spam ?? 0),
                'moderation_comments' => (int) ($commentStats->moderation ?? 0),
                'views' => (int) ($postStats->total_views ?? 0),
            ],
            'recentPendingPosts' => $recentPendingPosts,
            'recentPendingComments' => $recentPendingComments,
        ]);
    }
}
