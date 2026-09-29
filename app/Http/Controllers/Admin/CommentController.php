<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for moderation.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $comments = Comment::query()
            ->with(['user', 'post'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($query, $term) {
                $query->where('body', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusCounts = [
            'all' => Comment::count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
        ];

        return view('admin.comments.index', [
            'comments' => $comments,
            'currentStatus' => $status,
            'search' => $search,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Approve the specified comment.
     */
    public function approve(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $comment->update(['status' => 'approved']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'approved',
                'message' => 'Bình luận đã được duyệt hiển thị thành công.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Bình luận đã được duyệt hiển thị thành công.');
    }

    /**
     * Mark the specified comment as spam.
     */
    public function spam(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $comment->update(['status' => 'spam']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'spam',
                'message' => 'Bình luận đã được đánh dấu là Spam.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Bình luận đã được đánh dấu là Spam.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bình luận thành công.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Đã xóa bình luận thành công.');
    }
}
