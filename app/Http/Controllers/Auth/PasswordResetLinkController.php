<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset email input view (Screen 1).
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset OTP request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $normalizedEmail = strtolower(trim($request->input('email')));
        $user = User::where('email', $normalizedEmail)->first();

        // Always store email in session so the user can proceed to OTP screen
        // regardless of whether user exists or not (anti-enumeration)
        session(['password_reset_email' => $normalizedEmail]);

        if ($user) {
            /** @var OtpService $otpService */
            $otpService = app(OtpService::class);

            try {
                $otp = $otpService->generate(
                    email: $normalizedEmail,
                    purpose: OtpService::PURPOSE_PASSWORD_RESET,
                    user: $user,
                    ip: $request->ip(),
                    userAgent: $request->userAgent()
                );

                $user->notify(new PasswordResetOtpNotification($otp));
            } catch (Throwable $e) {
                Log::error('Lỗi gửi OTP đặt lại mật khẩu: '.$e->getMessage(), [
                    'email' => $normalizedEmail,
                ]);

                return redirect()->route('password.otp')
                    ->with('warning', 'Hệ thống đã nhận yêu cầu nhưng máy chủ gửi thư đang gặp sự cố. Bạn có thể bấm gửi lại mã sau ít phút.');
            }
        }

        return redirect()->route('password.otp')
            ->with('status', 'Nếu địa chỉ email tồn tại trong hệ thống, mã OTP 6 chữ số đã được gửi đến hộp thư của bạn.');
    }

    /**
     * Display the OTP verification view (Screen 2).
     */
    public function showOtpForm(): View|RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);
        $normalizedEmail = strtolower(trim($email));
        $cooldown = $otpService->getCooldownRemaining($normalizedEmail, OtpService::PURPOSE_PASSWORD_RESET);
        $expiry = $otpService->getExpiryRemaining($normalizedEmail, OtpService::PURPOSE_PASSWORD_RESET);

        $latestOtp = EmailOtp::query()
            ->where('email', $normalizedEmail)
            ->where('purpose', OtpService::PURPOSE_PASSWORD_RESET)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        $expiresAtMs = ($latestOtp && $latestOtp->expires_at && ! $latestOtp->isExpired())
            ? $latestOtp->expires_at->getTimestamp() * 1000
            : null;
        $serverTimeMs = now()->getTimestamp() * 1000;

        return view('auth.reset-password-otp', [
            'email' => $email,
            'cooldown' => $cooldown,
            'expiry' => $expiry,
            'expiresAtMs' => $expiresAtMs,
            'serverTimeMs' => $serverTimeMs,
        ]);
    }

    /**
     * Verify the entered OTP code and grant a short-lived reset token (Screen 2 -> 3).
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP gồm 6 chữ số.',
            'otp.size' => 'Mã OTP phải bao gồm đúng 6 chữ số.',
            'otp.regex' => 'Mã OTP chỉ được chứa các ký số 0-9.',
        ]);

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);

        $result = $otpService->verify(
            email: $email,
            purpose: OtpService::PURPOSE_PASSWORD_RESET,
            code: $request->input('otp')
        );

        if (! $result['success']) {
            return back()->withInput()->withErrors([
                'otp' => $result['message'],
            ]);
        }

        // Generate a cryptographically secure reset token bound to this session and email
        $resetToken = Str::random(64);

        session([
            'password_reset_auth' => [
                'email' => $email,
                'token_hash' => hash('sha256', $resetToken.config('app.key')),
                'expires_at' => now()->addMinutes(15)->timestamp,
            ],
        ]);

        return redirect()->route('password.reset', ['token' => $resetToken])
            ->with('status', 'Xác thực OTP thành công. Vui lòng thiết lập mật khẩu mới cho tài khoản của bạn.');
    }

    /**
     * Resend a new OTP for password reset.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);

        if ($otpService->isUnderCooldown($email, OtpService::PURPOSE_PASSWORD_RESET)) {
            $remaining = $otpService->getCooldownRemaining($email, OtpService::PURPOSE_PASSWORD_RESET);

            return back()->with('warning', "Vui lòng đợi {$remaining} giây trước khi yêu cầu gửi lại mã OTP mới.");
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            try {
                $otp = $otpService->generate(
                    email: $email,
                    purpose: OtpService::PURPOSE_PASSWORD_RESET,
                    user: $user,
                    ip: $request->ip(),
                    userAgent: $request->userAgent()
                );

                $user->notify(new PasswordResetOtpNotification($otp));
            } catch (Throwable $e) {
                Log::error('Lỗi gửi lại OTP đặt lại mật khẩu: '.$e->getMessage(), [
                    'email' => $email,
                ]);

                return back()->with('error', 'Không thể gửi email OTP lúc này. Vui lòng kiểm tra lại dịch vụ email hoặc thử lại sau ít phút.');
            }
        }

        return back()->with('status', 'Mã OTP mới đã được gửi đến email của bạn.');
    }
}
