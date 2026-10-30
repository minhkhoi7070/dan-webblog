<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'viewer',
        ]);

        $mailSent = true;
        try {
            event(new Registered($user));
        } catch (Throwable $e) {
            $mailSent = false;
            Log::error('Không thể gửi email xác minh khi đăng ký user: '.$e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        /** @var OtpService $otpService */
        $otpService = app(OtpService::class);

        if ($otpService->getExpiryRemaining($user->email, OtpService::PURPOSE_EMAIL_VERIFICATION) === 0) {
            try {
                $user->sendEmailVerificationNotification();
                $mailSent = true;
            } catch (Throwable $e) {
                $mailSent = false;
                Log::error('Không thể cấp mã OTP xác minh khi đăng ký user: '.$e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        }

        Auth::login($user);

        if (! $mailSent) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Tài khoản đã tạo thành công, nhưng chưa thể gửi email xác minh lúc này do sự cố kết nối máy chủ gửi thư. Vui lòng nhấn "Gửi lại email xác minh" hoặc thử lại sau ít phút.');
        }

        return redirect()->route('verification.notice');
    }
}
