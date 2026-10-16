<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Store a new tag or return existing one if already present.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['author', 'admin'], true) || $user->is_locked) {
            abort(403, 'Bạn không có quyền tạo hoặc quản lý thẻ bài viết.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ], [
            'name.required' => 'Vui lòng nhập tên thẻ bài viết.',
            'name.max' => 'Tên thẻ không được vượt quá 50 ký tự.',
        ]);

        $cleanName = Tag::normalizeName($validated['name']);

        if ($cleanName === '') {
            return response()->json([
                'message' => 'Tên thẻ không hợp lệ.',
                'errors' => ['name' => ['Tên thẻ không hợp lệ sau khi chuẩn hóa.']],
            ], 422);
        }

        $tag = Tag::findOrCreateByName($cleanName);

        return response()->json([
            'success' => true,
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ],
        ], 201);
    }

    /**
     * Search available tags for editor suggestions.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $cleanQ = Tag::normalizeName($q);

        $tags = Tag::query()
            ->when($cleanQ !== '', function ($query) use ($cleanQ) {
                $query->where('name', 'like', "%{$cleanQ}%")
                    ->orWhere('slug', 'like', "%{$cleanQ}%");
            })
            ->orderBy('name')
            ->take(30)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'success' => true,
            'tags' => $tags,
        ]);
    }
}

