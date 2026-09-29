<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * Display a listing of posts favorited by the authenticated user.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $posts = $user->favoritePosts()
            ->published()
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments', 'likers', 'favoritedBy'])
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

        return view('favorites.index', [
            'posts' => $posts,
        ]);
    }
}
