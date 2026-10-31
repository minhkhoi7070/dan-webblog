<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    public function index(Request $request): Response|RedirectResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:255'],
            'tag' => ['nullable', 'string', 'max:255'],
            'page' => ['sometimes', 'integer'],
        ]);

        $search = $filters['q'] ?? null;
        $categorySlug = $filters['category'] ?? null;
        $tagSlug = $filters['tag'] ?? null;

        $activeCategory = null;
        if ($categorySlug) {
            $activeCategory = Category::where('slug', $categorySlug)->first();
        }

        $posts = Post::query()
            ->published()
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments' => fn ($q) => $q->where('status', 'approved'), 'likers', 'favoritedBy'])
            ->withViewerState($request->user())
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

        $viewData = [
            'posts' => $posts,
            'search' => $search,
            'activeCategory' => $activeCategory,
            'currentCategorySlug' => $categorySlug,
            'currentTagSlug' => $tagSlug,
        ];

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('posts.partials.feed-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        $viewData['categories'] = Category::withCount(['posts' => fn ($q) => $q->published()])->get();

        return response()
            ->view('posts.index', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
    }

    /**
     * Display the specified post and increment its view count.
     */
    public function show(Post $post, Request $request): View|RedirectResponse
    {
        // Enforce guest access: unpublished posts return 404 for guests and non-authorized users
        if ($post->status !== 'published' && Gate::denies('view', $post)) {
            abort(404);
        }

        // Backward compatibility: If post was accessed via legacy numeric ID in URL, 301 redirect to canonical slug URL
        $pathSegment = $request->segment(2);
        if ($pathSegment && is_numeric($pathSegment) && $post->slug && $pathSegment !== $post->slug) {
            return redirect()->to(route('posts.show', array_merge(['post' => $post->slug], $request->query())), 301);
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

        if ($viewer = $request->user()) {
            $post->loadExists([
                'likers as liked_by_viewer' => fn ($query) => $query->whereKey($viewer->id),
                'favoritedBy as saved_by_viewer' => fn ($query) => $query->whereKey($viewer->id),
            ]);
            $post->setAttribute('viewer_state_user_id', $viewer->id);
        }

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
    public function byAuthor(User $user, Request $request): Response|View|RedirectResponse
    {
        $search = $request->query('q');

        $posts = Post::query()
            ->published()
            ->where('user_id', $user->id)
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments' => fn ($q) => $q->where('status', 'approved'), 'likers', 'favoritedBy'])
            ->withViewerState($request->user())
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

        $viewData = [
            'author' => $user,
            'posts' => $posts,
            'search' => $search,
        ];

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('authors.partials.feed-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        return response()
            ->view('authors.show', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
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
        $latestPost = Post::query()
            ->where('user_id', auth()->id())
            ->latest('updated_at')
            ->latest('id')
            ->first();
        $latestDraft = $latestPost?->status === 'draft' ? $latestPost : null;

        return view('author.posts.create', [
            'categories' => $categories,
            'tags' => $tags,
            'latestDraft' => $latestDraft,
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
        $body = PostContentService::cleanHtml($body);
        $excerpt = $request->validated('excerpt') ?: Str::limit(strip_tags($body), 150);
        $rawSlug = $request->validated('slug');
        $slug = $rawSlug ?: Post::generateUniqueSlug($request->validated('title'));

        $action = $request->input('action');
        $user = $request->user();
        $isPublishAction = in_array($action, ['submit', 'publish'], true);

        if ($isPublishAction && $user->isAdmin()) {
            $status = 'published';
            $publishedAt = now();
        } elseif ($isPublishAction) {
            $status = 'pending';
            $publishedAt = null;
        } else {
            $status = 'draft';
            $publishedAt = null;
        }

        $post = DB::transaction(function () use ($user, $request, $status, $publishedAt, $body, $excerpt, $slug, $thumbnail) {
            $post = Post::create([
                'user_id' => $user->id,
                'category_id' => $request->validated('category_id'),
                'title' => $request->validated('title'),
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body' => $body,
                'thumbnail' => $thumbnail,
                'status' => $status,
                'published_at' => $publishedAt,
                'views' => 0,
            ]);

            $tagIds = array_unique($request->input('tags', []));
            $post->tags()->sync($tagIds);

            return $post;
        });

        if ($status === 'published') {
            return redirect()->route('posts.edit', $post)
                ->with('success', 'Bài viết đã được xuất bản thành công.');
        }

        if ($status === 'pending') {
            return redirect()->route('posts.edit', $post)
                ->with('success', 'Bài viết đã được gửi đến Admin để kiểm duyệt thành công (Đang chờ Admin duyệt).');
        }

        return redirect()->route('posts.edit', $post)
            ->with('success', 'Bản nháp đã được tạo thành công.');
    }

    /**
     * Check slug availability and validity via AJAX.
     */
    public function checkSlug(Request $request): JsonResponse
    {
        Gate::authorize('create', Post::class);

        $rawSlug = (string) $request->query('slug', '');
        $postId = $request->query('post_id');
        $postId = is_numeric($postId) ? (int) $postId : null;
        if ($postId !== null) {
            Gate::authorize('update', Post::findOrFail($postId));
        }

        $slug = Str::slug($rawSlug);

        if ($slug === '') {
            return response()->json([
                'valid' => false,
                'available' => false,
                'slug' => '',
                'message' => 'Đường dẫn không được để trống.',
            ]);
        }

        if (mb_strlen($slug) > 255) {
            return response()->json([
                'valid' => false,
                'available' => false,
                'slug' => $slug,
                'message' => 'Đường dẫn vượt quá giới hạn 255 ký tự.',
            ]);
        }

        if (in_array($slug, Post::RESERVED_SLUGS, true)) {
            return response()->json([
                'valid' => false,
                'available' => false,
                'slug' => $slug,
                'message' => 'Đường dẫn này được dành riêng cho chức năng hệ thống. Vui lòng chọn đường dẫn khác.',
            ]);
        }

        $isTaken = Post::where('slug', $slug)
            ->when($postId, fn ($q) => $q->where('id', '!=', $postId))
            ->exists();

        if ($isTaken) {
            return response()->json([
                'valid' => true,
                'available' => false,
                'slug' => $slug,
                'message' => 'Đường dẫn này đã được sử dụng bởi một bài viết khác.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'available' => true,
            'slug' => $slug,
            'message' => 'Đường dẫn hợp lệ và có thể sử dụng.',
        ]);
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
        abort_unless(is_string($path), 500);

        $temporaryVideos = $request->session()->get('temporary_post_videos', []);
        $temporaryVideos[$safeName] = $request->user()->id;
        $request->session()->put('temporary_post_videos', $temporaryVideos);

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
        if (! is_string($filename) || ! preg_match('/^[a-zA-Z0-9_-]+\.(mp4|webm)$/i', $filename)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên file video không hợp lệ.',
            ], 422);
        }

        $temporaryVideos = $request->session()->get('temporary_post_videos', []);
        abort_unless(($temporaryVideos[$filename] ?? null) === $request->user()->id, 403);
        abort_if(Post::query()->where('body', 'like', '%'.$filename.'%')->exists(), 403);

        $path = 'posts/videos/'.$filename;
        abort_unless(Storage::disk('public')->exists($path), 404);
        abort_unless(Storage::disk('public')->delete($path), 500);

        unset($temporaryVideos[$filename]);
        $request->session()->put('temporary_post_videos', $temporaryVideos);

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
        $body = PostContentService::cleanHtml($body);
        $excerpt = $request->validated('excerpt') ?: Str::limit(strip_tags($body), 150);

        // Security: Do not allow user input to tamper with status directly!
        $post->title = $request->validated('title');
        $post->category_id = $request->validated('category_id');
        $post->excerpt = $excerpt;
        $post->body = $body;

        if ($request->has('slug')) {
            $customSlug = $request->validated('slug');
            $post->slug = $customSlug ?: Post::generateUniqueSlug($post->title, $post->id);
        } elseif (empty($post->slug)) {
            $post->slug = Post::generateUniqueSlug($post->title, $post->id);
        }

        $action = $request->input('action');
        $user = $request->user();
        $isPublishAction = in_array($action, ['submit', 'publish'], true);

        if ($user->isAdmin() && $isPublishAction && in_array($post->status, ['draft', 'rejected', 'pending'], true)) {
            $post->status = 'published';
            $post->published_at = $post->published_at ?? now();
            $post->rejection_reason = null;
            $successMessage = 'Bài viết đã được xuất bản thành công.';
        } elseif ($isPublishAction && in_array($post->status, ['draft', 'rejected'], true)) {
            $post->status = 'pending';
            $successMessage = 'Bài viết đã được lưu và gửi đến Admin để kiểm duyệt thành công (Đang chờ Admin duyệt).';
        } elseif ($isPublishAction && $post->status === 'pending') {
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
        $user = $request->user();
        $post = DB::transaction(function () use ($post, $user): Post {
            $lockedPost = Post::query()->lockForUpdate()->findOrFail($post->id);
            Gate::authorize('submit', $lockedPost);
            abort_unless(in_array($lockedPost->status, ['draft', 'rejected'], true), 422);

            if ($user->isAdmin() && $lockedPost->user_id === $user->id) {
                $lockedPost->update([
                    'status' => 'published',
                    'published_at' => $lockedPost->published_at ?? now(),
                    'rejection_reason' => null,
                ]);

                return $lockedPost;
            }

            $lockedPost->update(['status' => 'pending']);

            return $lockedPost;
        });

        if ($post->status === 'published') {

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => 'published',
                    'message' => "Bài viết '{$post->title}' đã được xuất bản thành công.",
                ]);
            }

            return redirect()->back()
                ->with('success', "Bài viết '{$post->title}' đã được xuất bản thành công.");
        }

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
    public function stats(Request $request): Response|View
    {
        $user = $request->user();
        abort_if(! $user, 401);

        $query = $user->isAdmin() ? Post::query() : Post::where('user_id', $user->id);

        $postMetrics = (clone $query)->toBase()
            ->selectRaw("COUNT(*) as total, SUM(views) as views,
                COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'published' THEN 1 END) as published,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected")
            ->first();

        $totalLikes = Like::whereIn('post_id', (clone $query)->select('id'))->count();
        $totalComments = Comment::whereIn('post_id', (clone $query)->select('id'))->count();

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

        $viewData = [
            'stats' => [
                'total' => (int) $postMetrics->total,
                'draft' => (int) $postMetrics->draft,
                'pending' => (int) $postMetrics->pending,
                'published' => (int) $postMetrics->published,
                'rejected' => (int) $postMetrics->rejected,
                'views' => (int) $postMetrics->views,
                'likes' => $totalLikes,
                'comments' => $totalComments,
            ],
            'posts' => $posts,
            'currentStatus' => $statusFilter,
            'search' => $search,
        ];

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('author.posts.partials.stats-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        return response()
            ->view('author.posts.stats', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
    }
}
