<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of published posts with search, category filter, and pagination.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $search = $request->query('q');
        $categorySlug = $request->query('category');
        $tagSlug = $request->query('tag');

        $activeCategory = null;
        if ($categorySlug) {
            $activeCategory = Category::where('slug', $categorySlug)->first();
        }

        $posts = Post::query()
            ->published()
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments' => fn ($q) => $q->where('status', 'approved'), 'likers', 'favoritedBy'])
            ->when($search, fn ($query) => $query->search($search))
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
            })
            ->when($tagSlug, function ($query) use ($tagSlug) {
                $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
            })
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->has('page')) {
            $requestedPage = (int) $request->query('page');
            $maxPage = max(1, $posts->lastPage());
            if ($requestedPage < 1) {
                return redirect()->to($request->fullUrlWithQuery(['page' => 1]));
            }
            if ($requestedPage > $maxPage) {
                return redirect()->to($request->fullUrlWithQuery(['page' => $maxPage]));
            }
        }

        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->get();

        return view('posts.index', [
            'posts' => $posts,
            'categories' => $categories,
            'search' => $search,
            'activeCategory' => $activeCategory,
            'currentCategorySlug' => $categorySlug,
            'currentTagSlug' => $tagSlug,
        ]);
    }

    /**
     * Display the specified post and increment its view count.
     */
    public function show(Post $post): View
    {
        // Enforce guest access: unpublished posts return 404 for guests and non-authorized users
        if ($post->status !== 'published' && Gate::denies('view', $post)) {
            abort(404);
        }

        // Increment view count
        $post->increment('views');

        // Eager load relations to prevent N+1 queries (only display approved comments and replies publicly)
        $post->load([
            'user',
            'category',
            'tags',
            'comments' => fn ($query) => $query->whereNull('parent_id')
                ->where('status', 'approved')
                ->with([
                    'user',
                    'replies' => fn ($q) => $q->where('status', 'approved')->with('user'),
                ])
                ->latest(),
        ]);

        $post->loadCount([
            'comments' => fn ($q) => $q->where('status', 'approved'),
            'likers',
            'favoritedBy',
        ]);

        // Related published posts from the same category
        $relatedPosts = Post::query()
            ->published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with(['user', 'category'])
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('posts.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * Display published posts by a specific author.
     */
    public function byAuthor(User $user, Request $request): View|RedirectResponse
    {
        $search = $request->query('q');

        $posts = Post::query()
            ->published()
            ->where('user_id', $user->id)
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments' => fn ($q) => $q->where('status', 'approved'), 'likers', 'favoritedBy'])
            ->when($search, fn ($query) => $query->search($search))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        if ($request->has('page')) {
            $requestedPage = (int) $request->query('page');
            $maxPage = max(1, $posts->lastPage());
            if ($requestedPage < 1) {
                return redirect()->to($request->fullUrlWithQuery(['page' => 1]));
            }
            if ($requestedPage > $maxPage) {
                return redirect()->to($request->fullUrlWithQuery(['page' => $maxPage]));
            }
        }

        $user->loadCount(['posts' => fn ($q) => $q->published(), 'followers']);

        return view('authors.show', [
            'author' => $user,
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    // =========================================================================
    // AUTHOR CMS ACTIONS (Sprint 3)
    // =========================================================================

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        Gate::authorize('create', Post::class);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('author.posts.create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $thumbnail = null;
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $thumbnail = 'storage/'.$path;
        }

        $body = $request->input('body') ?? $request->input('content');
        $excerpt = $request->validated('excerpt') ?: Str::limit(strip_tags($body), 150);
        $slug = Str::slug($request->validated('title')).'-'.Str::lower(Str::random(6));

        // Workflow rule: Authors ALWAYS create posts with 'draft' status
        $post = Post::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->validated('category_id'),
            'title' => $request->validated('title'),
            'slug' => $slug,
            'excerpt' => $excerpt,
            'body' => $body,
            'thumbnail' => $thumbnail,
            'status' => 'draft',
            'views' => 0,
        ]);

        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('posts.edit', $post)
            ->with('success', 'Bài viết đã được tạo thành công dưới dạng Bản nháp (Draft).');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $post->load('tags');

        return view('author.posts.edit', [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    /**
     * Update the specified post in storage.
     */
    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        if ($request->hasFile('thumbnail')) {
            // Delete old file if present
            if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
                $oldPath = str_replace('storage/', '', $post->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = 'storage/'.$path;
        }

        $body = $request->input('body') ?? $request->input('content');
        $excerpt = $request->validated('excerpt') ?: Str::limit(strip_tags($body), 150);

        // Security: Do not allow user input to tamper with status directly!
        $post->title = $request->validated('title');
        $post->category_id = $request->validated('category_id');
        $post->excerpt = $excerpt;
        $post->body = $body;
        $post->save();

        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('posts.edit', $post)
            ->with('success', 'Bài viết đã được cập nhật thành công.');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
            $oldPath = str_replace('storage/', '', $post->thumbnail);
            Storage::disk('public')->delete($oldPath);
        }

        $post->delete();

        return redirect()->route('posts.stats')
            ->with('success', 'Bài viết đã được xóa thành công.');
    }

    /**
     * Submit a draft or rejected post for editorial review.
     */
    public function submit(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        Gate::authorize('submit', $post);

        $post->update(['status' => 'pending']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Bài viết đã được gửi cho Ban biên tập xét duyệt (Pending).',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Bài viết đã được gửi cho Ban biên tập xét duyệt (Pending).');
    }

    /**
     * Display author dashboard statistics and posts management list.
     */
    public function stats(Request $request): View
    {
        $user = $request->user();
        abort_if(! $user, 401);

        $query = $user->isAdmin() ? Post::query() : Post::where('user_id', $user->id);

        $totalPosts = (clone $query)->count();
        $draftCount = (clone $query)->where('status', 'draft')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $publishedCount = (clone $query)->where('status', 'published')->count();
        $rejectedCount = (clone $query)->where('status', 'rejected')->count();

        $totalViews = (clone $query)->sum('views');

        // Total likes & comments on author's posts
        $totalLikes = (clone $query)->withCount('likers')->get()->sum('likers_count');
        $totalComments = (clone $query)->withCount('comments')->get()->sum('comments_count');

        $statusFilter = $request->query('status');
        $search = $request->query('q');

        $posts = (clone $query)
            ->with(['category', 'tags'])
            ->withCount(['comments', 'likers', 'favoritedBy'])
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->when($search, fn ($q) => $q->search($search))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('author.posts.stats', [
            'stats' => [
                'total' => $totalPosts,
                'draft' => $draftCount,
                'pending' => $pendingCount,
                'published' => $publishedCount,
                'rejected' => $rejectedCount,
                'views' => $totalViews,
                'likes' => $totalLikes,
                'comments' => $totalComments,
            ],
            'posts' => $posts,
            'currentStatus' => $statusFilter,
            'search' => $search,
        ]);
    }
}
