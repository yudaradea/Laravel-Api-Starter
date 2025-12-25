# 🔑 Password Reset Functionality

Complete forgot password flow dengan email notification.

## 🚀 Implementation Status

🚧 **Planned Features:**

-   Forgot password endpoint
-   Send reset link via email
-   Token validation
-   Reset password endpoint
-   Rate limiting for security

## 📖 Planned Usage

### Request Password Reset

```http
POST /api/password/forgot

{
    "email": "user@example.com"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Password reset link sent to your email"
}
```

### Reset Password

```http
POST /api/password/reset

{
    "email": "user@example.com",
    "token": "reset-token-from-email",
    "password": "new-password",
    "password_confirmation": "new-password"
}
```

## 🔧 Endpoints

| Method | Endpoint                      | Description             |
| ------ | ----------------------------- | ----------------------- |
| POST   | `/api/password/forgot`        | Request reset link      |
| POST   | `/api/password/reset`         | Reset password          |
| GET    | `/api/password/reset/{token}` | Verify token (optional) |

## 📧 Email Template

```blade
<!-- resources/views/emails/password-reset.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <p>Click the button below to reset your password:</p>
    <a href="{{ $resetUrl }}">Reset Password</a>
    <p>This link will expire in 60 minutes.</p>
    <p>If you didn't request this, please ignore this email.</p>
</body>
</html>
```

## 🔒 Security Features

1. **Token Expiration** - Reset tokens expire after 60 minutes
2. **Rate Limiting** - Max 5 requests per hour per IP
3. **Single Use** - Tokens can only be used once
4. **Email Verification** - Only send to registered emails

## 📊 Database

```php
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
```

## 🎯 User Flow

1. User clicks "Forgot Password"
2. User enters email address
3. System sends reset link to email
4. User clicks link in email
5. User enters new password
6. Password updated, user can login

## 🔧 Configuration

```env
# .env
PASSWORD_RESET_EXPIRE=60  # Minutes
```

---

**Status:** Planning phase. Implementation coming in next update.
