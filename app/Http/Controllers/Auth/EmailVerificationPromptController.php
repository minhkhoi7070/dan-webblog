<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);
        $cooldown = $otpService->getCooldownRemaining($request->user()->email, OtpService::PURPOSE_EMAIL_VERIFICATION);
        $expiry = $otpService->getExpiryRemaining($request->user()->email, OtpService::PURPOSE_EMAIL_VERIFICATION);
        $hasOtp = $request->user()->emailOtps()
            ->where('purpose', OtpService::PURPOSE_EMAIL_VERIFICATION)
            ->whereNull('consumed_at')
            ->exists();

        return view('auth.verify-email', [
            'cooldown' => $cooldown,
            'expiry' => $expiry,
            'hasOtp' => $hasOtp,
        ]);
    }
}
