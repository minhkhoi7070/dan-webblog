<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    public function update(ProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $oldAvatar = $user->avatar;
        $uploadedPath = null;

        if ($request->hasFile('avatar')) {
            $uploadedPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $uploadedPath;
        }

        $fillData = [
            'name' => $validated['name'],
        ];

        if (array_key_exists('bio', $validated)) {
            $fillData['bio'] = $validated['bio'];
        }

        if (isset($validated['email'])) {
            $fillData['email'] = $validated['email'];
        }

        $user->fill($fillData);

        $emailChanged = $user->isDirty('email');
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        try {
            $user->save();

            if ($emailChanged) {
                try {
                    $user->sendEmailVerificationNotification();
                } catch (Throwable $mailException) {
                    Log::error('Lỗi gửi email xác minh khi đổi email: '.$mailException->getMessage(), [
                        'user_id' => $user->id,
                        'new_email' => $user->email,
                    ]);

                    if ($request->wantsJson()) {
                        return response()->json([
                            'status' => 'profile-updated',
                            'message' => 'Hồ sơ đã cập nhật nhưng chưa thể gửi email xác minh đến địa chỉ mới lúc này.',
                            'email_changed' => true,
                            'redirect' => route('profile.edit'),
                            'user' => [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'avatar_url' => $user->avatar_url,
                            ],
                        ]);
                    }

                    return Redirect::route('profile.edit')->with('warning', 'Hồ sơ đã cập nhật nhưng chưa thể gửi email xác minh đến địa chỉ mới lúc này. Vui lòng thử lại sau.');
                }
            }

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

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'profile-updated',
                'message' => 'Cập nhật thông tin hồ sơ thành công!',
                'email_changed' => $emailChanged,
                'redirect' => $emailChanged ? route('verification.notice') : null,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'bio' => $user->bio,
                    'avatar_url' => $user->avatar_url,
                ],
            ]);
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
