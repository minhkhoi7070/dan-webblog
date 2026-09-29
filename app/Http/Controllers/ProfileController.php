<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile overview.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $user->loadCount(['comments', 'likedPosts', 'favoritePosts', 'following']);

        return view('profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Upgrade an authenticated viewer to author role.
     */
    public function becomeAuthor(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only viewers are allowed to transition to author. Admin and existing author cannot be modified.
        if ($user && $user->role === 'viewer') {
            $user->forceFill(['role' => 'author'])->save();

            return redirect()->route('posts.create')
                ->with('success', 'Chúc mừng bạn đã trở thành tác giả! Hãy bắt đầu viết bài đầu tiên.');
        }

        return redirect()->route('home');
    }
}
