<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route($request->is('admin/*') ? 'admin.dashboard' : 'dashboard', absolute: false));
        }

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);

        if ($otpService->isUnderCooldown($request->user()->email, OtpService::PURPOSE_EMAIL_VERIFICATION)) {
            $remaining = $otpService->getCooldownRemaining($request->user()->email, OtpService::PURPOSE_EMAIL_VERIFICATION);

            return back()->with('warning', "Vui lòng đợi {$remaining} giây trước khi yêu cầu gửi lại mã OTP mới.");
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            Log::error('Lỗi gửi lại email xác minh OTP: '.$e->getMessage(), [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]);

            return back()->with('error', 'Không thể gửi email lúc này do sự cố kết nối máy chủ gửi thư. Vui lòng kiểm tra lại dịch vụ email hoặc thử lại sau ít phút.');
        }

        return back()->with('status', 'verification-link-sent');
    }
}
