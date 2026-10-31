<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get the localized login validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => trans('validation.required', locale: 'vi'),
            'email' => trans('validation.email', locale: 'vi'),
            'string' => trans('validation.string', locale: 'vi'),
        ];
    }

    /**
     * Get the localized login field names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return trans('validation.attributes', locale: 'vi');
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(string $guard = 'web', bool $requireAdmin = false): void
    {
        $this->ensureIsNotRateLimited();

        $message = trans('auth.failed', locale: 'vi');

        $authenticated = Auth::guard($guard)->attemptWhen(
            $this->only('email', 'password'),
            function (User $user) use ($guard, $requireAdmin, &$message): bool {
                if ($guard === 'web' && ! in_array($user->role, ['viewer', 'author'], true)) {
                    return false;
                }

                if ($user->is_locked) {
                    $message = trans('auth.locked', locale: 'vi');

                    return false;
                }

                if ($requireAdmin && ! $user->isAdmin()) {
                    $message = trans('auth.admin_only', locale: 'vi');

                    return false;
                }

                return true;
            },
            $this->boolean('remember'),
        );

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ], 'vi'),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
