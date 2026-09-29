<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of posts for administrative moderation.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        $posts = Post::query()
            ->with(['user', 'category', 'reviewer'])
            ->withCount(['comments', 'likers'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, fn ($q) => $q->search($search))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        // Status counts for tabs
        $statusCounts = [
            'all' => Post::count(),
            'pending' => Post::where('status', 'pending')->count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'rejected' => Post::where('status', 'rejected')->count(),
        ];

        return view('admin.posts.index', [
            'posts' => $posts,
            'categories' => $categories,
            'currentStatus' => $status,
            'currentCategory' => $categoryId,
            'search' => $search,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Display detailed post information for editorial review.
     */
    public function show(Post $post): View
    {
        $post->load(['user', 'category', 'tags', 'reviewer']);
        $post->loadCount(['comments', 'likers', 'favoritedBy']);

        return view('admin.posts.show', [
            'post' => $post,
        ]);
    }

    /**
     * Approve a post for immediate publication.
     */
    public function approve(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $post->update([
            'status' => 'published',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'published_at' => $post->published_at ?? now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'published',
                'message' => "Bài viết '{$post->title}' đã được phê duyệt và xuất bản thành công.",
            ]);
        }

        return redirect()->back()
            ->with('success', "Bài viết '{$post->title}' đã được phê duyệt và xuất bản thành công.");
    }

    /**
     * Reject a post with editorial feedback reason.
     */
    public function reject(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = $request->input('rejection_reason') ?? 'Nội dung chưa đáp ứng đủ tiêu chuẩn biên tập của hệ thống.';

        $post->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'message' => "Bài viết '{$post->title}' đã bị từ chối phê duyệt.",
            ]);
        }

        return redirect()->back()
            ->with('success', "Bài viết '{$post->title}' đã bị từ chối phê duyệt.");
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
            $oldPath = str_replace('storage/', '', $post->thumbnail);
            Storage::disk('public')->delete($oldPath);
        }

        $postTitle = $post->title;
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', "Đã xóa bài viết '{$postTitle}' thành công.");
    }
}
