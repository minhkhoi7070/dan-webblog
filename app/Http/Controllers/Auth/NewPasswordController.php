<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Throwable;

class NewPasswordController extends Controller
{
    /**
     * Display the new password creation view (Screen 3).
     */
    public function create(Request $request, ?string $token = null): View|RedirectResponse
    {
        $token = $token ?? $request->route('token');
        $resetAuth = session('password_reset_auth');

        if (! $token ||
            ! $resetAuth ||
            now()->timestamp > $resetAuth['expires_at'] ||
            ! hash_equals($resetAuth['token_hash'], hash('sha256', $token.config('app.key')))) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Phiên đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng xác thực mã OTP trước.',
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $resetAuth['email'],
        ]);
    }

    /**
     * Handle the new password submission.
     */
    public function store(Request $request): RedirectResponse
    {
        $resetAuth = session('password_reset_auth');
        $token = $request->input('token');

        if (! $token ||
            ! $resetAuth ||
            now()->timestamp > $resetAuth['expires_at'] ||
            ! hash_equals($resetAuth['token_hash'], hash('sha256', $token.config('app.key')))) {
            return redirect()->route('password.request')->withErrors([
                'email' => 'Phiên đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng thực hiện lại từ đầu.',
            ]);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $user = User::where('email', $resetAuth['email'])->first();

        if (! $user) {
            session()->forget(['password_reset_auth', 'password_reset_email']);

            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản không tồn tại trên hệ thống.',
            ]);
        }

        // Update password with hash (email_verified_at is intentionally unchanged)
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Invalidate the session reset authorization token
        session()->forget(['password_reset_auth', 'password_reset_email']);

        // Send confirmation email that password was changed (no password in email)
        try {
            $user->notify(new PasswordChangedNotification);
        } catch (Throwable $e) {
            Log::error('Lỗi gửi email thông báo đổi mật khẩu: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        event(new PasswordReset($user));

        return redirect()->route('login')->with('status', 'Mật khẩu của bạn đã được đặt lại thành công. Vui lòng đăng nhập bằng mật khẩu mới.');
    }
}
