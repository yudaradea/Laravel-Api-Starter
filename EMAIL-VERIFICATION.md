# ✉️ Email Verification

Email verification system untuk memastikan email user valid.

## 🚀 Implementation Status

🚧 **Planned Features:**

-   Send verification email on registration
-   Verification link with token
-   Resend verification email
-   Middleware untuk protected routes
-   Email templates

## 📖 Planned Usage

### Registration with Email Verification

```php
// After registration
$user = User::create([...]);
$user->sendEmailVerificationNotification();

return ResponseHelper::success([
    'message' => 'Registration successful. Please check your email.',
]);
```

### Verify Email Endpoint

```http
POST /api/email/verify/{id}/{hash}
```

### Resend Verification

```http
POST /api/email/resend
```

### Protected Routes

```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Only verified users can access
    Route::get('/dashboard', ...);
});
```

## 🔧 Configuration

```env
# .env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 📧 Email Template

```blade
<!-- resources/views/emails/verify-email.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h1>Welcome to {{ config('app.name') }}!</h1>
    <p>Please click the button below to verify your email address.</p>
    <a href="{{ $verificationUrl }}">Verify Email</a>
</body>
</html>
```

## 🎯 Use Cases

1. **Prevent Fake Registrations** - Ensure real email addresses
2. **Account Security** - Verify ownership
3. **Reduce Spam** - Filter out bots
4. **Communication** - Ensure you can reach users

---

**Status:** Planning phase. Implementation coming in next update.
