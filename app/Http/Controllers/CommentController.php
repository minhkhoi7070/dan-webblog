<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a newly created comment or reply.
     */
    public function store(StoreCommentRequest $request, ?Post $post = null): JsonResponse|RedirectResponse
    {
        if (! $post && $request->filled('post_id')) {
            $post = Post::findOrFail($request->validated('post_id'));
        }

        abort_if(! $post || $post->status !== 'published', 404, 'Post not found or unavailable.');

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $request->validated('parent_id'),
            'body' => $request->validated('body'),
            'status' => 'approved',
        ]);

        $comment->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully.',
                'comment' => [
                    'id' => $comment->id,
                    'post_id' => $comment->post_id,
                    'parent_id' => $comment->parent_id,
                    'body' => $comment->body,
                    'status' => $comment->status,
                    'user_name' => $comment->user->name,
                    'user_avatar' => $comment->user->avatar,
                    'created_at' => $comment->created_at->diffForHumans(),
                ],
            ], 201);
        }

        return redirect()->back()
            ->with('success', 'Bình luận thành công!')
            ->withFragment('comments');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa bình luận.');
    }
}
