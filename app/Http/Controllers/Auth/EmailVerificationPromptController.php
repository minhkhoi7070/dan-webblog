<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
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
            return redirect()->intended(route($request->is('admin/*') ? 'admin.dashboard' : 'dashboard', absolute: false));
        }

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);
        $user = $request->user();
        $normalizedEmail = strtolower(trim($user->email));

        $cooldown = $otpService->getCooldownRemaining($normalizedEmail, OtpService::PURPOSE_EMAIL_VERIFICATION);
        $expiry = $otpService->getExpiryRemaining($normalizedEmail, OtpService::PURPOSE_EMAIL_VERIFICATION);

        $latestOtp = EmailOtp::query()
            ->where(function ($query) use ($user, $normalizedEmail) {
                $query->where('user_id', $user->id)
                    ->orWhere('email', $normalizedEmail);
            })
            ->where('purpose', OtpService::PURPOSE_EMAIL_VERIFICATION)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        $hasOtp = $latestOtp !== null;
        $expiresAtMs = ($latestOtp && $latestOtp->expires_at && ! $latestOtp->isExpired())
            ? $latestOtp->expires_at->getTimestamp() * 1000
            : null;
        $serverTimeMs = now()->getTimestamp() * 1000;

        return view('auth.verify-email', [
            'cooldown' => $cooldown,
            'expiry' => $expiry,
            'hasOtp' => $hasOtp,
            'expiresAtMs' => $expiresAtMs,
            'serverTimeMs' => $serverTimeMs,
        ]);
    }
}
