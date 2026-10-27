<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    /**
     * Display a listing of posts favorited by the authenticated user.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $posts = $user->favoritePosts()
            ->published()
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments' => fn ($query) => $query->where('status', 'approved'), 'likers', 'favoritedBy'])
            ->withViewerState($user)
            ->latest('favorites.created_at')
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
        ];

        if ($request->header('X-BlogMNM-Feed') === '1') {
            return response()
                ->view('favorites.partials.feed-results', $viewData)
                ->header('X-BlogMNM-Feed', '1')
                ->header('Vary', 'X-BlogMNM-Feed')
                ->header('Cache-Control', 'private, no-store');
        }

        return response()
            ->view('favorites.index', $viewData)
            ->header('Vary', 'X-BlogMNM-Feed')
            ->header('Cache-Control', 'private, no-store');
    }
}
