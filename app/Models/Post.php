<?php

namespace App\Models;

use App\Services\PostContentService;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    public const RESERVED_SLUGS = ['create', 'stats', 'check-slug'];

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'thumbnail',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'views',
        'published_at',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
        'views' => 0,
    ];

    /**
     * Get human-readable status label in Vietnamese.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Bản nháp',
            'pending' => 'Chờ duyệt',
            'published' => 'Đã xuất bản',
            'rejected' => 'Bị từ chối',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Check if post is currently draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if post is pending review.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if post is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if post is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'views' => 'integer',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (Post $post) {
            if ($post->thumbnail && str_starts_with($post->thumbnail, 'storage/')) {
                $isShared = static::where('thumbnail', $post->thumbnail)->where('id', '!=', $post->id)->exists();
                if (! $isShared) {
                    $oldPath = str_replace('storage/', '', $post->thumbnail);
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });
    }

    /**
     * Accessor for content (alias of body).
     */
    public function getContentAttribute(): ?string
    {
        return $this->body;
    }

    /**
     * Mutator for content (alias of body).
     */
    public function setContentAttribute(?string $value): void
    {
        $this->attributes['body'] = $value;
    }

    /**
     * Get the sanitized, rendered HTML content for display.
     */
    public function getRenderedBodyAttribute(): string
    {
        return PostContentService::renderHtml($this->body);
    }

    /**
     * Get the HTML content formatted for the WYSIWYG editor.
     */
    public function getEditorContentAttribute(): string
    {
        return PostContentService::toEditorHtml($this->body);
    }

    /**
     * Get image URLs for Threads-style media gallery display.
     *
     * @return array<int, string>
     */
    public function getGalleryImagesAttribute(): array
    {
        $images = [];
        if ($this->thumbnail) {
            $images[] = asset($this->thumbnail);
        }

        if ($this->body && preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $this->body, $matches)) {
            foreach ($matches[1] as $src) {
                if (! in_array($src, $images, true)) {
                    $images[] = $src;
                }
                if (count($images) >= 6) {
                    break;
                }
            }
        }

        return $images;
    }

    /**
     * The author of this post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin reviewer of this post.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * The category this post belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Tags associated with this post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * Comments on this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Users who liked this post.
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * Like records for this post.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Users who favorited/saved this post.
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Favorite records for this post.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Check if a given user has liked this post.
     */
    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ((int) $this->getAttribute('viewer_state_user_id') === $user->id
            && array_key_exists('liked_by_viewer', $this->attributes)) {
            return (bool) $this->getAttribute('liked_by_viewer');
        }

        return $this->likers()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a given user has saved/favorited this post.
     */
    public function isSavedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ((int) $this->getAttribute('viewer_state_user_id') === $user->id
            && array_key_exists('saved_by_viewer', $this->attributes)) {
            return (bool) $this->getAttribute('saved_by_viewer');
        }

        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Select interaction state for the current viewer without per-card queries.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeWithViewerState(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query;
        }

        return $query->withExists([
            'likers as liked_by_viewer' => fn (Builder $query) => $query->whereKey($user->id),
            'favoritedBy as saved_by_viewer' => fn (Builder $query) => $query->whereKey($user->id),
        ])->selectRaw('? as viewer_state_user_id', [$user->id]);
    }

    /**
     * Scope to only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to search posts by title, excerpt, or body.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%");
        });
    }

    /**
     * Retrieve the model for a bound value with legacy ID fallback.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $post = parent::resolveRouteBinding($value, $field);
        if ($post) {
            return $post;
        }

        // Backward compatibility: If lookup by slug fails but $value is numeric, check by primary key ID
        if (($field === 'slug' || $field === null) && is_numeric($value)) {
            return static::where('id', (int) $value)->first();
        }

        return null;
    }

    /**
     * Generate a unique, SEO-friendly slug for a post.
     */
    public static function generateUniqueSlug(string $titleOrSlug, ?int $ignoreId = null): string
    {
        $baseSlug = rtrim(mb_substr(Str::slug($titleOrSlug), 0, 255), '-');
        if ($baseSlug === '') {
            $baseSlug = 'bai-viet';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (in_array($slug, self::RESERVED_SLUGS, true) || static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $counter++;
            $suffix = "-{$counter}";
            $slug = rtrim(mb_substr($baseSlug, 0, 255 - strlen($suffix)), '-').$suffix;
        }

        return $slug;
    }
}
