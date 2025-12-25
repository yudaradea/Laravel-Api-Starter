# 🔄 Refresh Token Mechanism

JWT-style token refresh untuk seamless authentication experience.

## 🚀 Implementation Status

🚧 **Planned Features:**

-   Refresh token generation on login
-   Short-lived access tokens
-   Long-lived refresh tokens
-   Token rotation
-   Automatic refresh before expiry

## 📖 Planned Usage

### Login with Refresh Token

```http
POST /api/login

{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "access_token": "short-lived-token",
        "refresh_token": "long-lived-token",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

### Refresh Access Token

```http
POST /api/token/refresh

Headers:
Authorization: Bearer {refresh_token}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "access_token": "new-short-lived-token",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

## ⚙️ Configuration

```php
// config/sanctum.php
'expiration' => 60,  // Access token: 60 minutes
'refresh_expiration' => 43200,  // Refresh token: 30 days
```

## 🔧 Token Lifecycle

```plaintext
1. User Login
   ↓
2. Generate Access Token (60 min) + Refresh Token (30 days)
   ↓
3. User makes API requests with Access Token
   ↓
4. Access Token expires after 60 minutes
   ↓
5. Client uses Refresh Token to get new Access Token
   ↓
6. New Access Token issued (60 min)
   ↓
7. Repeat step 3-6
```

## 🔒 Security Features

1. **Token Rotation** - New refresh token issued on each refresh
2. **Expiration** - Both tokens have expiration
3. **Revocation** - Can revoke refresh tokens
4. **Single Use** - Old refresh tokens invalidated

## 📊 Database Schema

```php
// personal_access_tokens table
Schema::create('personal_access_tokens', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->morphs('tokenable');
    $table->string('name');  // 'access' or 'refresh'
    $table->string('token', 64)->unique();
    $table->text('abilities')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamp('last_used_at')->nullable();
    $table->timestamps();
});
```

## 💡 Client Implementation

### React/JavaScript Example

```javascript
let accessToken = localStorage.getItem("access_token");
let refreshToken = localStorage.getItem("refresh_token");

async function apiRequest(url, options = {}) {
    // Try with access token
    let response = await fetch(url, {
        ...options,
        headers: {
            ...options.headers,
            Authorization: `Bearer ${accessToken}`,
        },
    });

    // If 401, try refresh
    if (response.status === 401) {
        const refreshed = await refreshAccessToken();
        if (refreshed) {
            // Retry with new token
            response = await fetch(url, {
                ...options,
                headers: {
                    ...options.headers,
                    Authorization: `Bearer ${accessToken}`,
                },
            });
        }
    }

    return response;
}

async function refreshAccessToken() {
    const response = await fetch("/api/token/refresh", {
        method: "POST",
        headers: {
            Authorization: `Bearer ${refreshToken}`,
        },
    });

    if (response.ok) {
        const data = await response.json();
        accessToken = data.data.access_token;
        localStorage.setItem("access_token", accessToken);
        return true;
    }

    // Refresh failed, logout
    logout();
    return false;
}
```

## 🎯 Benefits

1. **Better Security** - Short-lived access tokens
2. **Better UX** - No frequent re-login
3. **Scalability** - Stateless authentication
4. **Control** - Can revoke refresh tokens

## ⚠️ Considerations

1. Store refresh token securely (HTTPOnly cookie recommended)
2. Never expose refresh token to client-side JS if possible
3. Implement token rotation
4. Monitor for suspicious refresh patterns

---

**Status:** Planning phase. Implementation coming in next update.
