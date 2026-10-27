<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

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
        $user->loadCount([
            'comments', 'likedPosts', 'favoritePosts', 'following',
            'posts as published_posts_count' => fn ($query) => $query->published(),
        ]);

        $userPosts = $user->posts()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->with(['user', 'category', 'tags'])
            ->withCount(['comments' => fn ($query) => $query->where('status', 'approved'), 'likers', 'favoritedBy'])
            ->withViewerState($user)
            ->get();

        $savedPosts = $user->favoritePosts()
            ->published()
            ->latest('favorites.created_at')
            ->take(3)
            ->with(['user', 'category'])
            ->get();

        return view('profile.show', [
            'user' => $user,
            'userPosts' => $userPosts,
            'savedPosts' => $savedPosts,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $oldAvatar = $user->avatar;
        $uploadedPath = null;

        if ($request->hasFile('avatar')) {
            $uploadedPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $uploadedPath;
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        try {
            $user->save();

            // After DB save succeeds, remove previous custom avatar from managed storage
            if ($uploadedPath && $oldAvatar && str_starts_with($oldAvatar, 'avatars/') && $oldAvatar !== $uploadedPath && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }
        } catch (Throwable $e) {
            // Roll back newly uploaded file if DB update fails
            if ($uploadedPath && Storage::disk('public')->exists($uploadedPath)) {
                Storage::disk('public')->delete($uploadedPath);
            }

            throw $e;
        }

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
        abort_if($user?->is_locked, 403, 'Account is locked.');

        // Only viewers are allowed to transition to author. Admin and existing author cannot be modified.
        if ($user && $user->role === 'viewer') {
            $user->forceFill(['role' => 'author'])->save();

            return redirect()->route('posts.create')
                ->with('success', 'Chúc mừng bạn đã trở thành tác giả! Hãy bắt đầu viết bài đầu tiên.');
        }

        return redirect()->route('home');
    }
}
