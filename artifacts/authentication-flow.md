# BlogMNM — Authentication Architecture & Flow

## 1. Overview
Authentication in BlogMNM is built on **Laravel Breeze (Blade Stack)**, delivering a secure, session-based stateful authentication system. It includes brute-force rate limiting, session fixation protection, and active account lock verification.

```mermaid
sequenceDiagram
    autonumber
    actor User
    participant Browser
    participant Route as routes/auth.php
    participant FormRequest as LoginRequest
    participant Auth as Auth::attempt()
    participant Model as User Model
    participant Session as Laravel Session

    User->>Browser: Enters Email & Password
    Browser->>Route: POST /login (with CSRF token)
    Route->>FormRequest: Execute authenticate()
    
    FormRequest->>FormRequest: ensureIsNotRateLimited() (max 5 attempts/min)
    alt Rate Limit Exceeded
        FormRequest-->>Browser: 422 Too Many Requests (Lockout timeout)
    end

    FormRequest->>Auth: Attempt credentials
    alt Invalid Credentials
        Auth-->>FormRequest: false
        FormRequest->>FormRequest: RateLimiter::hit()
        FormRequest-->>Browser: 302 with session errors ['email']
    else Valid Credentials
        Auth-->>FormRequest: true (User retrieved)
        FormRequest->>Model: Inspect user is_locked attribute
        
        alt Account is Locked (AUTH-07)
            FormRequest->>Auth: Auth::logout()
            FormRequest->>Session: Invalidate session
            FormRequest-->>Browser: 422 Error: "Your account has been locked."
        else Account Active
            FormRequest->>FormRequest: RateLimiter::clear()
            FormRequest->>Session: $request->session()->regenerate()
            FormRequest-->>Browser: 302 Redirect to /dashboard or intended URL
        end
    end
```

---

## 2. Authentication Subsystems

### 2.1 User Registration
- **Route**: `POST /register` $\to$ `RegisteredUserController@store`
- **Validation**:
  ```php
  $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
  ]);
  ```
- **Privilege Whitelist**: Role is strictly assigned as `'viewer'` via database schema defaults and model configuration. Direct parameter tampering attempting to pass `role = 'admin'` is rejected.
- **Session Startup**: Logs user in immediately via `Auth::login($user)` and fires `Registered` event.

### 2.2 Login & Security Defenses
- **Route**: `POST /login` $\to$ `AuthenticatedSessionController@store`
- **Rate Limiting**: Managed in `App\Http\Requests\Auth\LoginRequest`:
  - Cache key: `Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip())`.
  - Max attempts: 5 per 60 seconds.
- **Session Fixation Defense**: Executes `$request->session()->regenerate()` upon valid authentication.
- **Locked User Interception (`AUTH-07`)**:
  ```php
  if (Auth::user()?->is_locked) {
      Auth::logout();
      throw ValidationException::withMessages([
          'email' => __('Your account has been locked by an administrator.'),
      ]);
  }
  ```

### 2.3 Logout & Session Cleanup
- **Route**: `POST /logout` $\to$ `AuthenticatedSessionController@destroy`
- **Cleanup Routine**:
  ```php
  Auth::guard('web')->logout();
  $request->session()->invalidate();
  $request->session()->regenerateToken();
  ```
- Prevents back-button session caching and invalidates existing CSRF tokens.

### 2.4 Password Management Pipeline
- **Forgot Password**: Generates cryptographic token saved in `password_reset_tokens` table.
- **Reset Password**: Validates token expiry, hashes new password via `Hash::make()`, resets remember token, and notifies user.
- **In-App Password Update**: `PUT /password` (`PasswordController@update`) verifies `current_password` before updating the hash.
