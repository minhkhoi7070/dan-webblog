<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FollowingController extends Controller
{
    /**
     * Display posts and creators followed by the authenticated user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $tab = $request->query('tab', 'feed');
        if (! in_array($tab, ['feed', 'authors'], true)) {
            $tab = 'feed';
        }

        $followedIds = $user->following()->pluck('users.id');
        $followingCount = $followedIds->count();

        // 1. Posts Feed from Followed Authors
        $posts = null;
        if ($tab === 'feed') {
            $posts = Post::query()
                ->published()
                ->whereIn('user_id', $followedIds)
                ->with(['user', 'category', 'tags'])
                ->withCount(['comments' => fn ($q) => $q->where('status', 'approved'), 'likers', 'favoritedBy'])
                ->withViewerState($user)
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
        }

        // 2. Followed Authors Directory
        $authors = null;
        if ($tab === 'authors') {
            $authors = $user->following()
                ->withCount(['followers', 'posts' => fn ($q) => $q->published()])
                ->latest('follows.created_at')
                ->paginate(12)
                ->withQueryString();

            if ($request->has('page')) {
                $requestedPage = (int) $request->query('page');
                $maxPage = max(1, $authors->lastPage());
                if ($requestedPage < 1) {
                    return redirect()->to($request->fullUrlWithQuery(['page' => 1]));
                }
                if ($requestedPage > $maxPage) {
                    return redirect()->to($request->fullUrlWithQuery(['page' => $maxPage]));
                }
            }
        }

        // 3. Recommended Authors (For empty state or discovery)
        $suggestedAuthors = collect();
        if ($followingCount === 0 || $tab === 'authors') {
            $suggestedAuthors = User::query()
                ->whereIn('role', ['author', 'admin'])
                ->where('id', '!=', $user->id)
                ->when($followedIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $followedIds))
                ->withCount(['followers', 'posts' => fn ($q) => $q->published()])
                ->orderByDesc('followers_count')
                ->take(6)
                ->get();
        }

        $viewData = [
            'tab' => $tab,
            'posts' => $posts,
            'authors' => $authors,
            'followingCount' => $followingCount,
            'suggestedAuthors' => $suggestedAuthors,
        ];

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('following.partials.feed-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        return response()
            ->view('following.index', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
    }
}
