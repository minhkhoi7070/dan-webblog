<?php

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OtpService
{
    public const PURPOSE_EMAIL_VERIFICATION = 'email_verification';

    public const PURPOSE_PASSWORD_RESET = 'password_reset';

    public const CODE_LENGTH = 6;

    public const EXPIRATION_MINUTES = 5;

    public const MAX_ATTEMPTS = 5;

    public const COOLDOWN_SECONDS = 60;

    public const STATUS_SUCCESS = 'success';

    public const STATUS_INVALID = 'invalid';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_MAX_ATTEMPTS = 'max_attempts';

    public const STATUS_NOT_FOUND = 'not_found';

    public const STATUS_COOLDOWN = 'cooldown';

    /**
     * Compute a deterministic SHA-256 hash peppered with the app key.
     */
    public function hashCode(string $code): string
    {
        return hash('sha256', trim($code).config('app.key'));
    }

    /**
     * Check if a cooldown is currently active for this email and purpose.
     */
    public function isUnderCooldown(string $email, string $purpose): bool
    {
        return $this->getCooldownRemaining($email, $purpose) > 0;
    }

    /**
     * Get remaining cooldown seconds before a new OTP can be requested.
     */
    public function getCooldownRemaining(string $email, string $purpose): int
    {
        $normalizedEmail = strtolower(trim($email));

        $latest = EmailOtp::query()
            ->where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if (! $latest || ! $latest->created_at) {
            return 0;
        }

        $elapsed = $latest->created_at->diffInSeconds(now());

        return (int) max(0, ceil(self::COOLDOWN_SECONDS - $elapsed));
    }

    /**
     * Get remaining seconds before the active unconsumed OTP expires.
     */
    public function getExpiryRemaining(string $email, string $purpose): int
    {
        $normalizedEmail = strtolower(trim($email));

        $latest = EmailOtp::query()
            ->where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if (! $latest || $latest->isExpired()) {
            return 0;
        }

        return (int) max(0, ceil(now()->diffInSeconds($latest->expires_at, false)));
    }

    /**
     * Generate a new 6-digit OTP code, invalidating any previous unconsumed codes.
     *
     * @throws RuntimeException
     */
    public function generate(
        string $email,
        string $purpose,
        ?User $user = null,
        ?string $ip = null,
        ?string $userAgent = null,
        bool $force = false
    ): string {
        $normalizedEmail = strtolower(trim($email));

        if (! in_array($purpose, [self::PURPOSE_EMAIL_VERIFICATION, self::PURPOSE_PASSWORD_RESET], true)) {
            throw new RuntimeException("Mục đích OTP '{$purpose}' không hợp lệ.");
        }

        if (! $force && $this->isUnderCooldown($normalizedEmail, $purpose)) {
            $remaining = $this->getCooldownRemaining($normalizedEmail, $purpose);
            throw new RuntimeException("Vui lòng đợi {$remaining} giây trước khi yêu cầu gửi lại mã mới.");
        }

        return DB::transaction(function () use ($normalizedEmail, $purpose, $user, $ip, $userAgent) {
            // Invalidate all previous unconsumed codes for this email and purpose
            EmailOtp::query()
                ->where('email', $normalizedEmail)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);

            // Cryptographically secure 6-digit random number
            $code = sprintf('%06d', random_int(0, 999999));
            $codeHash = $this->hashCode($code);

            EmailOtp::create([
                'user_id' => $user?->id ?? User::where('email', $normalizedEmail)->value('id'),
                'email' => $normalizedEmail,
                'purpose' => $purpose,
                'code_hash' => $codeHash,
                'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
                'attempts' => 0,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            return $code;
        });
    }

    /**
     * Verify a 6-digit OTP code against the latest active record.
     *
     * @return array{success: bool, status: string, message: string, remaining_attempts: int, otp?: EmailOtp}
     */
    public function verify(string $email, string $purpose, string $code): array
    {
        $normalizedEmail = strtolower(trim($email));
        $trimmedCode = trim($code);

        if (strlen($trimmedCode) !== self::CODE_LENGTH || ! ctype_digit($trimmedCode)) {
            return [
                'success' => false,
                'status' => self::STATUS_INVALID,
                'message' => 'Mã OTP phải bao gồm đúng 6 chữ số.',
                'remaining_attempts' => 0,
            ];
        }

        return DB::transaction(function () use ($normalizedEmail, $purpose, $trimmedCode) {
            /** @var EmailOtp|null $record */
            $record = EmailOtp::query()
                ->where('email', $normalizedEmail)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $record) {
                return [
                    'success' => false,
                    'status' => self::STATUS_NOT_FOUND,
                    'message' => 'Không tìm thấy mã OTP hợp lệ. Vui lòng yêu cầu mã mới.',
                    'remaining_attempts' => 0,
                ];
            }

            if ($record->isExpired()) {
                return [
                    'success' => false,
                    'status' => self::STATUS_EXPIRED,
                    'message' => 'Mã OTP đã hết hạn sau 5 phút. Vui lòng bấm gửi lại mã mới.',
                    'remaining_attempts' => 0,
                ];
            }

            if ($record->hasExceededMaxAttempts(self::MAX_ATTEMPTS)) {
                return [
                    'success' => false,
                    'status' => self::STATUS_MAX_ATTEMPTS,
                    'message' => 'Bạn đã nhập sai mã quá 5 lần. Mã OTP này đã bị hủy, vui lòng yêu cầu gửi lại mã mới.',
                    'remaining_attempts' => 0,
                ];
            }

            $expectedHash = $this->hashCode($trimmedCode);

            if (! hash_equals($record->code_hash, $expectedHash)) {
                $record->increment('attempts');
                $remaining = max(0, self::MAX_ATTEMPTS - $record->attempts);

                if ($remaining === 0) {
                    return [
                        'success' => false,
                        'status' => self::STATUS_MAX_ATTEMPTS,
                        'message' => 'Bạn đã nhập sai mã 5 lần. Mã OTP này đã bị vô hiệu hóa, vui lòng yêu cầu mã mới.',
                        'remaining_attempts' => 0,
                    ];
                }

                return [
                    'success' => false,
                    'status' => self::STATUS_INVALID,
                    'message' => "Mã OTP không chính xác. Bạn còn {$remaining} lần thử.",
                    'remaining_attempts' => $remaining,
                ];
            }

            // Successfully matched
            $record->update(['consumed_at' => now()]);

            return [
                'success' => true,
                'status' => self::STATUS_SUCCESS,
                'message' => 'Xác minh mã OTP thành công.',
                'remaining_attempts' => self::MAX_ATTEMPTS,
                'otp' => $record,
            ];
        });
    }
}
