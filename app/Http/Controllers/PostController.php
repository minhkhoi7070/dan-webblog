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
use Illuminate\Support\Facades\DB;
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
        $body = \App\Services\PostContentService::cleanHtml($body);
        $excerpt = $request->validated('excerpt') ?: Str::limit(strip_tags($body), 150);
        $slug = Str::slug($request->validated('title')).'-'.Str::lower(Str::random(6));

        $action = $request->input('action');
        $status = ($action === 'submit') ? 'pending' : 'draft';

        $post = DB::transaction(function () use ($request, $status, $body, $excerpt, $slug, $thumbnail) {
            $post = Post::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->validated('category_id'),
                'title' => $request->validated('title'),
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body' => $body,
                'thumbnail' => $thumbnail,
                'status' => $status,
                'views' => 0,
            ]);

            $tagIds = array_unique($request->input('tags', []));
            $post->tags()->sync($tagIds);

            return $post;
        });

        if ($status === 'pending') {
            return redirect()->route('posts.edit', $post)
                ->with('success', 'Bài viết đã được gửi đến Admin để kiểm duyệt thành công (Đang chờ Admin duyệt).');
        }

        return redirect()->route('posts.edit', $post)
            ->with('success', 'Bài viết đã được tạo thành công dưới dạng Bản nháp (Draft).');
    }

    /**
     * Upload an embedded image for post content.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        Gate::authorize('create', Post::class);

        $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,webp,gif',
                'max:2048',
            ],
        ], [
            'image.required' => 'Vui lòng chọn file hình ảnh.',
            'image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, webp, hoặc gif.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.',
        ]);

        $file = $request->file('image');

        $extension = $file->guessExtension() ?: 'jpg';
        $safeName = Str::random(40).'.'.$extension;

        $path = $file->storeAs('posts/images', $safeName, 'public');

        return response()->json([
            'success' => true,
            'url' => '/storage/'.$path,
            'filename' => $safeName,
        ], 201);
    }

    /**
     * Upload an embedded personal video for post content.
     */
    public function uploadVideo(Request $request): JsonResponse
    {
        Gate::authorize('create', Post::class);

        // Check if server post_max_size or upload limit dropped the request payload
        if ($request->header('Content-Length') && empty($request->allFiles())) {
            $postMaxSize = ini_get('post_max_size') ?: 'N/A';
            $uploadMax = ini_get('upload_max_filesize') ?: 'N/A';

            return response()->json([
                'success' => false,
                'message' => "Dung lượng video vượt quá giới hạn cấu hình của máy chủ web (upload_max_filesize: {$uploadMax}, post_max_size: {$postMaxSize}). Vui lòng giảm dung lượng video hoặc liên hệ quản trị viên.",
            ], 422);
        }

        if (! $request->hasFile('video')) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy file video trong yêu cầu tải lên.',
            ], 422);
        }

        $file = $request->file('video');

        if (! $file->isValid()) {
            $errorCode = $file->getError();
            $errorMessage = match ($errorCode) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Dung lượng file video vượt quá giới hạn cho phép của máy chủ web.',
                UPLOAD_ERR_PARTIAL => 'File video chỉ mới được tải lên một phần. Vui lòng thử lại.',
                UPLOAD_ERR_NO_FILE => 'Không có file video nào được tải lên.',
                UPLOAD_ERR_NO_TMP_DIR => 'Lỗi máy chủ: Thiếu thư mục tạm thời để lưu trữ video.',
                UPLOAD_ERR_CANT_WRITE => 'Lỗi máy chủ: Không thể ghi file video lên ổ đĩa.',
                default => 'Tải lên video thất bại với mã lỗi hệ thống: '.$errorCode,
            };

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 422);
        }

        $maxSizeKb = (int) config('blog.max_video_size_kb', 102400);

        $request->validate([
            'video' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/webm',
                'mimes:mp4,webm',
                'max:'.$maxSizeKb,
            ],
        ], [
            'video.required' => 'Vui lòng chọn file video để tải lên.',
            'video.file' => 'File tải lên không hợp lệ.',
            'video.mimetypes' => 'Định dạng MIME không hợp lệ. Hệ thống chỉ hỗ trợ video MP4 (video/mp4) và WebM (video/webm).',
            'video.mimes' => 'Định dạng file không hợp lệ. Chỉ chấp nhận phần mở rộng .mp4 và .webm.',
            'video.max' => 'Dung lượng video vượt quá giới hạn cho phép (tối đa '.round($maxSizeKb / 1024).'MB).',
        ]);

        $clientExtension = strtolower((string) $file->getClientOriginalExtension());
        $mimeType = (string) $file->getMimeType();

        $extension = in_array($clientExtension, ['mp4', 'webm'], true)
            ? $clientExtension
            : ($mimeType === 'video/webm' ? 'webm' : 'mp4');

        $safeName = Str::random(40).'.'.$extension;
        $path = $file->storeAs('posts/videos', $safeName, 'public');

        return response()->json([
            'success' => true,
            'url' => '/storage/'.$path,
            'filename' => $safeName,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'type' => $mimeType,
        ], 201);
    }

    /**
     * Delete a previously uploaded video file (before saving the post).
     */
    public function deleteVideo(Request $request): JsonResponse
    {
        Gate::authorize('create', Post::class);

        $filename = $request->input('filename');
        if (! $filename || ! is_string($filename) || preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên file video không hợp lệ.',
            ], 422);
        }

        $safeBasename = basename($filename);
        $path = 'posts/videos/'.$safeBasename;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa file video thành công.',
        ]);
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

        $removeThumbnail = $request->boolean('remove_thumbnail');

        if ($request->hasFile('thumbnail')) {
            // Delete old file if present in storage and not shared by other posts
            if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
                $oldPath = str_replace('storage/', '', $post->thumbnail);
                $isShared = Post::where('thumbnail', $post->thumbnail)->where('id', '!=', $post->id)->exists();
                if (! $isShared) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = 'storage/'.$path;
        } elseif ($removeThumbnail) {
            // User explicitly removed the thumbnail
            if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
                $oldPath = str_replace('storage/', '', $post->thumbnail);
                $isShared = Post::where('thumbnail', $post->thumbnail)->where('id', '!=', $post->id)->exists();
                if (! $isShared) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $post->thumbnail = null;
        }

        $body = $request->input('body') ?? $request->input('content');
        $body = \App\Services\PostContentService::cleanHtml($body);
        $excerpt = $request->validated('excerpt') ?: Str::limit(strip_tags($body), 150);

        // Security: Do not allow user input to tamper with status directly!
        $post->title = $request->validated('title');
        $post->category_id = $request->validated('category_id');
        $post->excerpt = $excerpt;
        $post->body = $body;

        $action = $request->input('action');
        if ($action === 'submit' && in_array($post->status, ['draft', 'rejected'], true)) {
            $post->status = 'pending';
            $successMessage = 'Bài viết đã được lưu và gửi đến Admin để kiểm duyệt thành công (Đang chờ Admin duyệt).';
        } elseif ($action === 'submit' && $post->status === 'pending') {
            $successMessage = 'Bài viết đã được cập nhật và đang chờ Admin duyệt.';
        } else {
            $successMessage = 'Bài viết đã được cập nhật thành công.';
        }

        DB::transaction(function () use ($post, $request) {
            $post->save();

            $tagIds = array_unique($request->input('tags', []));
            $post->tags()->sync($tagIds);
        });

        return redirect()->route('posts.edit', $post)
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
            $oldPath = str_replace('storage/', '', $post->thumbnail);
            $isShared = Post::where('thumbnail', $post->thumbnail)->where('id', '!=', $post->id)->exists();
            if (! $isShared) {
                Storage::disk('public')->delete($oldPath);
            }
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
                'message' => 'Bài viết đã được gửi đến Admin để kiểm duyệt (Đang chờ Admin duyệt).',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Bài viết đã được gửi đến Admin để kiểm duyệt (Đang chờ Admin duyệt).');
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
        $search = is_string($search) ? trim($search) : null;
        if ($search === '') {
            $search = null;
        }

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
