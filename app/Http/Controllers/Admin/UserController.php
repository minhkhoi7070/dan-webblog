<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a paginated listing of users with search and role filters.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $role = $request->query('role');
        $status = $request->query('status');

        $users = User::query()
            ->withCount(['posts', 'comments'])
            ->when($search, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($role, fn ($q) => $q->where('role', $role))
            ->when($status !== null && $status !== '', function ($q) use ($status) {
                if ($status === 'locked') {
                    $q->where('is_locked', true);
                } elseif ($status === 'active') {
                    $q->where('is_locked', false);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
            'roleFilter' => $role,
            'statusFilter' => $status,
        ]);
    }

    /**
     * Display detailed profile and activity of the specified user.
     */
    public function show(User $user): View
    {
        $user->loadCount(['posts', 'comments', 'followers', 'following']);

        $recentPosts = $user->posts()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $recentComments = $user->comments()
            ->with('post')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.users.show', [
            'user' => $user,
            'recentPosts' => $recentPosts,
            'recentComments' => $recentComments,
        ]);
    }

    /**
     * Lock the specified user's account to prevent login/actions.
     */
    public function lock(Request $request, User $user): RedirectResponse
    {
        // Guard against destructive self-lockout
        if ($user->id === $request->user()->id) {
            return redirect()->back()
                ->with('error', 'Hành động bị chặn: Bạn không thể tự khóa tài khoản của chính mình.');
        }

        // Guard against locking peer administrators
        if ($user->isAdmin()) {
            return redirect()->back()
                ->with('error', 'Hành động bị chặn: Bạn không thể khóa tài khoản của Quản trị viên khác.');
        }

        $user->update(['is_locked' => true]);

        return redirect()->back()
            ->with('success', "Đã khóa thành công tài khoản của người dùng {$user->name}.");
    }

    /**
     * Unlock the specified user's account.
     */
    public function unlock(Request $request, User $user): RedirectResponse
    {
        $user->update(['is_locked' => false]);

        return redirect()->back()
            ->with('success', "Đã mở khóa tài khoản của người dùng {$user->name}.");
    }
}
