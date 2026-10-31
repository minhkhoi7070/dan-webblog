<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * Display a listing of posts for administrative moderation.
     */
    public function index(Request $request): Response|View
    {
        $search = $request->query('q') ?? $request->query('search');
        $search = is_string($search) ? trim($search) : null;
        if ($search === '') {
            $search = null;
        }

        $author = $request->query('author');
        $author = is_string($author) ? trim($author) : null;
        if ($author === '') {
            $author = null;
        }

        $fromDate = $this->normalizeDateQuery($request->query('from_date'));
        $toDate = $this->normalizeDateQuery($request->query('to_date'));
        $dateRangeError = $fromDate !== null && $toDate !== null && $fromDate > $toDate
            ? 'Ngày bắt đầu không được sau ngày kết thúc.'
            : null;

        $status = $request->query('status');
        $status = is_string($status) ? trim($status) : null;
        if ($status === '') {
            $status = null;
        }
        if ($status === 'draft') {
            $status = null;
        }

        $categoryId = $request->query('category_id');
        $categoryId = is_numeric($categoryId) ? (int) $categoryId : null;

        $posts = Post::query()
            ->where('status', '!=', 'draft')
            ->with(['user', 'category', 'reviewer'])
            ->withCount(['comments', 'likers'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, fn ($q) => $q->search($search))
            ->when($author, fn ($q) => $q->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$author}%")))
            ->when($fromDate !== null && $dateRangeError === null, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate !== null && $dateRangeError === null, fn ($q) => $q->whereDate('created_at', '<=', $toDate))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        // Status counts for tabs
        $statusCounts = [
            'all' => Post::where('status', '!=', 'draft')->count(),
            'pending' => Post::where('status', 'pending')->count(),
            'published' => Post::where('status', 'published')->count(),
            'rejected' => Post::where('status', 'rejected')->count(),
        ];

        $viewData = [
            'posts' => $posts,
            'categories' => $categories,
            'currentStatus' => $status,
            'currentCategory' => $categoryId,
            'search' => $search,
            'currentAuthor' => $author,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'dateRangeError' => $dateRangeError,
            'statusCounts' => $statusCounts,
        ];

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('admin.posts.partials.table-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        return response()
            ->view('admin.posts.index', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
    }

    private function normalizeDateQuery(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $date = trim($value);
        $parsedDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        $dateErrors = \DateTimeImmutable::getLastErrors();

        if (
            $parsedDate === false
            || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
            || $parsedDate->format('Y-m-d') !== $date
        ) {
            return null;
        }

        return $date;
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
        $post = DB::transaction(function () use ($request, $post): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);
            abort_unless($lockedPost->status === 'pending', 422, 'Chỉ bài viết đang chờ duyệt mới có thể được phê duyệt.');

            $lockedPost->update([
                'status' => 'published',
                'rejection_reason' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'published_at' => $lockedPost->published_at ?? now(),
            ]);

            return $lockedPost;
        });

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
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ], [
            'rejection_reason.required' => 'Vui lòng nhập lý do từ chối bài viết.',
        ]);

        $reason = $request->string('rejection_reason')->toString();

        $post = DB::transaction(function () use ($request, $post, $reason): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);
            abort_unless($lockedPost->status === 'pending', 422, 'Chỉ bài viết đang chờ duyệt mới có thể bị từ chối.');

            $lockedPost->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return $lockedPost;
        });

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
        $postTitle = $post->title;
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', "Đã xóa bài viết '{$postTitle}' thành công.");
    }
}
