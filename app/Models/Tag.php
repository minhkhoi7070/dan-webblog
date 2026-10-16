<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Normalize tag name by trimming and removing leading hashtags.
     */
    public static function normalizeName(string $name): string
    {
        return trim(ltrim(trim($name), '#'));
    }

    /**
     * Generate a unique slug for a given tag name.
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($name);
        if ($baseSlug === '') {
            $baseSlug = 'tag-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6));
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Find an existing tag by name/slug or create a new one safely.
     */
    public static function findOrCreateByName(string $name): static
    {
        $cleanName = static::normalizeName($name);
        $slug = \Illuminate\Support\Str::slug($cleanName);

        // Case-insensitive name match or slug match to prevent duplicates
        $existing = static::whereRaw('LOWER(name) = ?', [mb_strtolower($cleanName, 'UTF-8')])
            ->orWhere('slug', $slug)
            ->first();

        if ($existing) {
            return $existing;
        }

        return static::create([
            'name' => $cleanName,
            'slug' => static::generateUniqueSlug($cleanName),
        ]);
    }

    /**
     * Posts that have this tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }
}
