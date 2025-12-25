# ⚡ Rate Limiting Guide

Rate limiting melindungi API Anda dari abuse, brute force attacks, dan excessive requests.

## 📍 Konfigurasi

Rate limiting dikonfigurasi di: **`app/Providers/RateLimitServiceProvider.php`**

## 🎯 Default Rate Limits

Starter pack sudah include rate limits berikut:

| Rate Limiter     | Limit      | Kegunaan                          |
| ---------------- | ---------- | --------------------------------- |
| `api`            | 60/minute  | Global API requests               |
| `login`          | 5/minute   | Login attempts (anti brute force) |
| `register`       | 3/hour     | Registration (anti spam)          |
| `password-reset` | 5/hour     | Password reset requests           |
| `uploads`        | 10/minute  | File uploads                      |
| `sensitive`      | 10/minute  | Delete, update password, dll      |
| `public`         | 30/minute  | Public endpoints                  |
| `premium`        | 200/minute | Premium users (optional)          |

## 🔧 Cara Menggunakan

### 1️⃣ Apply ke Routes

**`routes/api.php`:**

```php
// Login dengan rate limit
Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// API dengan rate limit global
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', [UserController::class, 'index']);
});

// Sensitive operations dengan rate limit ketat
Route::middleware(['auth:sanctum', 'throttle:sensitive'])->group(function () {
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
    Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword']);
});
```

### 2️⃣ Apply di Controller

```php
class UserController extends Controller
{
    public function __construct()
    {
        // Apply rate limit ke specific methods
        $this->middleware('throttle:uploads')->only('uploadAvatar');
        $this->middleware('throttle:sensitive')->only(['destroy', 'updatePassword']);
    }
}
```

### 3️⃣ Custom Rate Limit per Method

```php
Route::get('/search', [SearchController::class, 'search'])
    ->middleware('throttle:30,1'); // 30 requests per minute
```

## ⚙️ Custom Rate Limiters

### Tambah Rate Limiter Baru

Edit `app/Providers/RateLimitServiceProvider.php`:

```php
public function boot(): void
{
    // Custom rate limit untuk export
    RateLimiter::for('export', function (Request $request) {
        return Limit::perHour(10)
            ->by($request->user()?->id ?: $request->ip())
            ->response(function (Request $request, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many export requests.',
                    'retry_after' => $headers['Retry-After'] ?? 3600,
                ], 429, $headers);
            });
    });
}
```

### Rate Limit Berdasarkan User Role

```php
RateLimiter::for('api', function (Request $request) {
    $user = $request->user();

    // Premium users get higher limit
    if ($user && $user->hasRole('premium')) {
        return Limit::perMinute(200)->by($user->id);
    }

    // Regular users
    if ($user) {
        return Limit::perMinute(60)->by($user->id);
    }

    // Guest users (by IP)
    return Limit::perMinute(30)->by($request->ip());
});
```

### Rate Limit dengan Multiple Limits

```php
RateLimiter::for('api', function (Request $request) {
    return [
        // 1000 requests per day
        Limit::perDay(1000)->by($request->user()?->id),

        // 60 requests per minute
        Limit::perMinute(60)->by($request->user()?->id),
    ];
});
```

### Rate Limit dengan Decay Time Custom

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id)
        ->decayAfter(120); // Reset after 2 minutes instead of 1
});
```

## 📊 Response Format

Ketika rate limit exceeded, API return:

```json
{
    "success": false,
    "message": "Too many requests. Please try again later.",
    "retry_after": 60
}
```

**Headers:**

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 0
Retry-After: 60
```

## 🎨 Custom Response

Edit response di `RateLimitServiceProvider.php`:

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id ?: $request->ip())
        ->response(function (Request $request, array $headers) {
            return response()->json([
                'success' => false,
                'message' => 'Whoa there! Slow down cowboy 🤠',
                'retry_after' => $headers['Retry-After'] ?? 60,
                'limit' => $headers['X-RateLimit-Limit'] ?? 60,
                'remaining' => 0,
            ], 429, $headers);
        });
});
```

## 🔍 Real World Examples

### Example 1: E-commerce API

```php
// Product browsing (public, lebih generous)
RateLimiter::for('browse', function (Request $request) {
    return Limit::perMinute(100)->by($request->ip());
});

// Checkout (authenticated, lebih ketat)
RateLimiter::for('checkout', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()->id);
});

// Order creation (sangat ketat)
RateLimiter::for('create-order', function (Request $request) {
    return Limit::perMinute(3)->by($request->user()->id);
});
```

### Example 2: Social Media API

```php
// Read posts (generous)
RateLimiter::for('read-posts', function (Request $request) {
    return Limit::perMinute(200)->by($request->user()?->id);
});

// Create post (moderate)
RateLimiter::for('create-post', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()->id);
});

// Send message (ketat untuk anti spam)
RateLimiter::for('send-message', function (Request $request) {
    return Limit::perMinute(20)->by($request->user()->id);
});
```

### Example 3: API dengan Tiered Limits

```php
RateLimiter::for('api', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        // Anonymous: 20/minute
        return Limit::perMinute(20)->by($request->ip());
    }

    // Check subscription tier
    if ($user->subscription_tier === 'enterprise') {
        return Limit::perMinute(500)->by($user->id);
    }

    if ($user->subscription_tier === 'premium') {
        return Limit::perMinute(200)->by($user->id);
    }

    // Free tier: 60/minute
    return Limit::perMinute(60)->by($user->id);
});
```

## 🧪 Testing Rate Limits

### Test dengan cURL

```bash
# Send multiple requests quickly
for i in {1..10}; do
  curl -X POST http://localhost:8000/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@example.com","password":"wrong"}' \
    -w "\nStatus: %{http_code}\n" \
    -s
done
```

Setelah 5 requests (login rate limit), akan dapat response 429.

### Test dengan Script

**test-rate-limit.js:**

```javascript
async function testRateLimit() {
    const url = "http://localhost:8000/api/login";

    for (let i = 1; i <= 10; i++) {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                email: "admin@example.com",
                password: "wrong",
            }),
        });

        console.log(`Request ${i}: ${response.status}`);

        if (response.status === 429) {
            const data = await response.json();
            console.log("Rate limited!", data);
            break;
        }
    }
}

testRateLimit();
```

### Verify Headers

```bash
curl -I http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Look for headers:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

## 🔓 Bypass Rate Limit (untuk Testing)

### Disable sementara

Di `.env`:

```env
RATE_LIMIT_ENABLED=false
```

Di `RateLimitServiceProvider.php`:

```php
public function boot(): void
{
    if (!env('RATE_LIMIT_ENABLED', true)) {
        return;
    }

    // ... rate limiter definitions
}
```

### Whitelist IP tertentu

```php
RateLimiter::for('api', function (Request $request) {
    // Whitelist untuk development
    $whitelistedIps = ['127.0.0.1', '::1'];

    if (in_array($request->ip(), $whitelistedIps)) {
        return Limit::none();
    }

    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

## 📈 Monitor Rate Limit Usage

### Log Rate Limit Hits

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id ?: $request->ip())
        ->response(function (Request $request, array $headers) {
            // Log rate limit hit
            \Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'endpoint' => $request->path(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Too many requests.',
            ], 429);
        });
});
```

### Check Remaining Limit

```php
use Illuminate\Support\Facades\RateLimiter;

public function checkLimit(Request $request)
{
    $key = $request->user()?->id ?: $request->ip();

    $remaining = RateLimiter::remaining('api:' . $key, 60);
    $available = RateLimiter::availableIn('api:' . $key);

    return response()->json([
        'remaining' => $remaining,
        'available_in' => $available . ' seconds',
    ]);
}
```

## ⚠️ Best Practices

### 1. Gunakan Rate Limit yang Berbeda untuk Endpoint yang Berbeda

```php
// ❌ BAD: Satu rate limit untuk semua
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', ...);
    Route::get('/products', ...);
    Route::delete('/account', ...);
});

// ✅ GOOD: Rate limit sesuai sensitivity
Route::middleware('throttle:login')->post('/login', ...);
Route::middleware('throttle:api')->get('/products', ...);
Route::middleware('throttle:sensitive')->delete('/account', ...);
```

### 2. Ketat untuk Write Operations, Generous untuk Read

```php
// Read operations: 100/minute
RateLimiter::for('read', function (Request $request) {
    return Limit::perMinute(100);
});

// Write operations: 20/minute
RateLimiter::for('write', function (Request $request) {
    return Limit::perMinute(20);
});
```

### 3. Track by User ID, Bukan IP (jika authenticated)

```php
// ✅ GOOD: Track by user ID
return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());

// ❌ BAD: Track by IP saja (bisa di-bypass dengan VPN)
return Limit::perMinute(60)->by($request->ip());
```

### 4. Custom Message yang Informatif

```php
->response(function (Request $request, array $headers) {
    return response()->json([
        'success' => false,
        'message' => 'You have exceeded the rate limit. Please wait ' .
                     ($headers['Retry-After'] ?? 60) . ' seconds.',
        'retry_after' => $headers['Retry-After'] ?? 60,
        'limit' => $headers['X-RateLimit-Limit'] ?? 60,
    ], 429, $headers);
});
```

## 🔧 Configuration Options

| Method                 | Deskripsi                                |
| ---------------------- | ---------------------------------------- |
| `perMinute(60)`        | 60 requests per minute                   |
| `perHour(1000)`        | 1000 requests per hour                   |
| `perDay(10000)`        | 10000 requests per day                   |
| `by($key)`             | Track by specific key (user ID, IP, etc) |
| `response($callback)`  | Custom response saat limit exceeded      |
| `decayAfter($seconds)` | Custom decay time                        |

## 📚 References

-   [Laravel Rate Limiting Docs](https://laravel.com/docs/routing#rate-limiting)
-   [HTTP 429 Too Many Requests](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429)

---

**Rate limiting sudah aktif! API Anda sekarang terlindungi dari abuse! 🛡️**
