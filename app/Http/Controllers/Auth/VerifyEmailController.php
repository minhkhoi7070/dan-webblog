<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified using a 6-digit OTP code.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route($request->is('admin/*') ? 'admin.dashboard' : 'dashboard', absolute: false).'?verified=1');
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
            email: $request->user()->email,
            purpose: OtpService::PURPOSE_EMAIL_VERIFICATION,
            code: $request->input('otp')
        );

        if (! $result['success']) {
            return back()->withInput()->withErrors([
                'otp' => $result['message'],
            ]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route($request->is('admin/*') ? 'admin.dashboard' : 'dashboard', absolute: false).'?verified=1')
            ->with('success', 'Xác minh email thành công! Tài khoản của bạn đã được kích hoạt đầy đủ.');
    }

    /**
     * Mark the authenticated user's email address as verified via signed link (legacy compatibility).
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route($request->is('admin/*') ? 'admin.dashboard' : 'dashboard', absolute: false).'?verified=1')
                ->with('success', 'Địa chỉ email của bạn đã được xác minh trước đó.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route($request->is('admin/*') ? 'admin.dashboard' : 'dashboard', absolute: false).'?verified=1')
            ->with('success', 'Xác minh email thành công! Tài khoản của bạn đã được kích hoạt đầy đủ.');
    }
}
