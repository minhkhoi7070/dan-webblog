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
        // Users metrics
        $totalUsers = User::count();
        $totalAuthors = User::where('role', 'author')->count();
        $totalViewers = User::where('role', 'viewer')->count();

        // Posts metrics
        $totalPosts = Post::count();
        $draftPosts = Post::where('status', 'draft')->count();
        $pendingPosts = Post::where('status', 'pending')->count();
        $publishedPosts = Post::where('status', 'published')->count();
        $rejectedPosts = Post::where('status', 'rejected')->count();

        // Comments metrics
        $totalComments = Comment::count();
        $pendingComments = Comment::where('status', 'pending')->count();
        $spamComments = Comment::where('status', 'spam')->count();
        $moderationComments = Comment::whereIn('status', ['pending', 'spam'])->count();

        // Overall views
        $totalViews = (int) Post::sum('views');

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
                'users' => $totalUsers,
                'authors' => $totalAuthors,
                'viewers' => $totalViewers,
                'posts' => $totalPosts,
                'draft' => $draftPosts,
                'pending' => $pendingPosts,
                'published' => $publishedPosts,
                'rejected' => $rejectedPosts,
                'comments' => $totalComments,
                'pending_comments' => $pendingComments,
                'spam_comments' => $spamComments,
                'moderation_comments' => $moderationComments,
                'views' => $totalViews,
            ],
            'recentPendingPosts' => $recentPendingPosts,
            'recentPendingComments' => $recentPendingComments,
        ]);
    }
}
