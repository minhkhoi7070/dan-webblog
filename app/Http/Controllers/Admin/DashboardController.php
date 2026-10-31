<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Central Dashboard.
     */
    public function index(Request $request): View
    {
        $data = $this->getMetricsData();

        return view('admin.dashboard', $data);
    }

    /**
     * Provide live operational metrics for real-time dashboard sync.
     */
    public function realtimeMetrics(Request $request): JsonResponse
    {
        $data = $this->getMetricsData();

        return response()->json([
            'metrics' => $data['metrics'],
            'pending_posts_count' => $data['recentPendingPosts']->count(),
            'pending_comments_count' => $data['recentPendingComments']->count(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Compute current operational metrics and queue collections.
     *
     * @return array{metrics: array<string, int>, recentPendingPosts: Collection, recentPendingComments: Collection}
     */
    protected function getMetricsData(): array
    {
        $userMetrics = User::toBase()
            ->selectRaw("COUNT(*) as total,
                COUNT(CASE WHEN role = 'author' THEN 1 END) as authors,
                COUNT(CASE WHEN role = 'viewer' THEN 1 END) as viewers")
            ->first();

        $postMetrics = Post::toBase()
            ->selectRaw("COUNT(*) as total, COALESCE(SUM(views), 0) as views,
                COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'published' THEN 1 END) as published,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected")
            ->first();

        $commentMetrics = Comment::toBase()
            ->selectRaw("COUNT(*) as total,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'spam' THEN 1 END) as spam,
                COUNT(CASE WHEN status IN ('pending', 'spam') THEN 1 END) as moderation")
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

        return [
            'metrics' => [
                'users' => (int) ($userMetrics->total ?? 0),
                'authors' => (int) ($userMetrics->authors ?? 0),
                'viewers' => (int) ($userMetrics->viewers ?? 0),
                'posts' => (int) ($postMetrics->total ?? 0),
                'draft' => (int) ($postMetrics->draft ?? 0),
                'pending' => (int) ($postMetrics->pending ?? 0),
                'published' => (int) ($postMetrics->published ?? 0),
                'rejected' => (int) ($postMetrics->rejected ?? 0),
                'comments' => (int) ($commentMetrics->total ?? 0),
                'pending_comments' => (int) ($commentMetrics->pending ?? 0),
                'spam_comments' => (int) ($commentMetrics->spam ?? 0),
                'moderation_comments' => (int) ($commentMetrics->moderation ?? 0),
                'views' => (int) ($postMetrics->views ?? 0),
            ],
            'recentPendingPosts' => $recentPendingPosts,
            'recentPendingComments' => $recentPendingComments,
        ];
    }
}
