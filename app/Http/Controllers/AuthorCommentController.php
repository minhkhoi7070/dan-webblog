<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AuthorCommentController extends Controller
{
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['approved', 'pending', 'spam'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $search = trim($validated['q'] ?? '');
        $currentStatus = $validated['status'] ?? null;
        $comments = Comment::query()
            ->whereHas('post', fn (Builder $query): Builder => $query->where('user_id', $request->user()->id))
            ->with(['user', 'post'])
            ->when($currentStatus, fn (Builder $query): Builder => $query->where('status', $currentStatus))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('body', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('post', fn (Builder $query): Builder => $query->where('title', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $viewData = compact('comments', 'search', 'currentStatus');

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('author.comments.partials.comment-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        return response()
            ->view('author.comments.index', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
    }
}
