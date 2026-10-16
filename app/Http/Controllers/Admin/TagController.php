<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of tags with post counts.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $tags = Tag::query()
            ->withCount('posts')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tags.index', [
            'tags' => $tags,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create(): View
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tags,slug'],
        ], [
            'name.required' => 'Vui lòng nhập tên thẻ bài viết.',
            'name.unique' => 'Tên thẻ này đã tồn tại trong hệ thống.',
            'slug.unique' => 'Đường dẫn tĩnh (slug) này đã được sử dụng.',
        ]);

        $cleanName = Tag::normalizeName($validated['name']);
        $preferredSlug = ! empty($validated['slug']) ? $validated['slug'] : $cleanName;
        $slug = Tag::generateUniqueSlug($preferredSlug);

        Tag::create([
            'name' => $cleanName,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Thẻ bài viết mới đã được tạo thành công.');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', [
            'tag' => $tag,
        ]);
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('tags', 'name')->ignore($tag->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('tags', 'slug')->ignore($tag->id)],
        ], [
            'name.required' => 'Vui lòng nhập tên thẻ bài viết.',
            'name.unique' => 'Tên thẻ này đã tồn tại trong hệ thống.',
            'slug.unique' => 'Đường dẫn tĩnh (slug) này đã được sử dụng.',
        ]);

        $cleanName = Tag::normalizeName($validated['name']);
        $preferredSlug = ! empty($validated['slug']) ? $validated['slug'] : $cleanName;
        $slug = Tag::generateUniqueSlug($preferredSlug, $tag->id);

        $tag->update([
            'name' => $cleanName,
            'slug' => $slug,
        ]);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Thẻ bài viết đã được cập nhật thành công.');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->posts()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Thẻ bài viết đã được xóa thành công.');
    }
}

