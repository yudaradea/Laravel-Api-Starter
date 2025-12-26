# ⚡ Panduan Rate Limiting

Rate limiting melindungi API Anda dari penyalahgunaan (abuse), serangan brute force, dan request berlebihan.

## 📍 Konfigurasi

Rate limiting dikonfigurasi di: **`app/Providers/RateLimitServiceProvider.php`**

## 🎯 Default Rate Limits

Starter pack sudah termasuk rate limits berikut:

| Rate Limiter     | Limit     | Kegunaan                           |
| :--------------- | :-------- | :--------------------------------- |
| `api`            | 60/menit  | Request API global                 |
| `login`          | 5/menit   | Percobaan login (anti brute force) |
| `register`       | 3/jam     | Pendaftaran (anti spam)            |
| `password-reset` | 5/jam     | Request reset password             |
| `uploads`        | 10/menit  | Upload file                        |
| `sensitive`      | 10/menit  | Hapus, update password, dll        |
| `public`         | 30/menit  | Endpoint publik                    |
| `premium`        | 200/menit | Pengguna premium (opsional)        |

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
                    'message' => 'Terlalu banyak request export.',
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

    // Pengguna premium dapat limit lebih tinggi
    if ($user && $user->hasRole('premium')) {
        return Limit::perMinute(200)->by($user->id);
    }

    // Pengguna reguler
    if ($user) {
        return Limit::perMinute(60)->by($user->id);
    }

    // Pengguna tamu (berdasarkan IP)
    return Limit::perMinute(30)->by($request->ip());
});
```

### Rate Limit dengan Multiple Limits

```php
RateLimiter::for('api', function (Request $request) {
    return [
        // 1000 requests per hari
        Limit::perDay(1000)->by($request->user()?->id),

        // 60 requests per menit
        Limit::perMinute(60)->by($request->user()?->id),
    ];
});
```

### Rate Limit dengan Custom Decay Time

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id)
        ->decayAfter(120); // Reset setelah 2 menit, bukan 1
});
```

## 📊 Format Response

Ketika rate limit terlampaui (exceeded), API mengembalikan:

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
                'message' => 'Sabar dulu boss! 🤠',
                'retry_after' => $headers['Retry-After'] ?? 60,
                'limit' => $headers['X-RateLimit-Limit'] ?? 60,
                'remaining' => 0,
            ], 429, $headers);
        });
});
```

## 🔍 Contoh Dunia Nyata

### Contoh 1: E-commerce API

```php
// Product browsing (publik, lebih longgar)
RateLimiter::for('browse', function (Request $request) {
    return Limit::perMinute(100)->by($request->ip());
});

// Checkout (terautentikasi, lebih ketat)
RateLimiter::for('checkout', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()->id);
});

// Pembuatan pesanan (sangat ketat)
RateLimiter::for('create-order', function (Request $request) {
    return Limit::perMinute(3)->by($request->user()->id);
});
```

### Contoh 2: Social Media API

```php
// Membaca postingan (longgar)
RateLimiter::for('read-posts', function (Request $request) {
    return Limit::perMinute(200)->by($request->user()?->id);
});

// Membuat postingan (moderat)
RateLimiter::for('create-post', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()->id);
});

// Mengirim pesan (ketat untuk anti spam)
RateLimiter::for('send-message', function (Request $request) {
    return Limit::perMinute(20)->by($request->user()->id);
});
```

### Contoh 3: API dengan Tiered Limits

```php
RateLimiter::for('api', function (Request $request) {
    $user = $request->user();

    if (!$user) {
        // Anonim: 20/menit
        return Limit::perMinute(20)->by($request->ip());
    }

    // Cek tingkatan langganan
    if ($user->subscription_tier === 'enterprise') {
        return Limit::perMinute(500)->by($user->id);
    }

    if ($user->subscription_tier === 'premium') {
        return Limit::perMinute(200)->by($user->id);
    }

    // Tingkat gratis: 60/menit
    return Limit::perMinute(60)->by($user->id);
});
```

## 🧪 Testing Rate Limits

### Test dengan cURL

```bash
# Kirim banyak request secara cepat
for i in {1..10}; do
  curl -X POST http://localhost:8000/api/login \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@example.com","password":"wrong"}' \
    -w "\nStatus: %{http_code}\n" \
    -s
done
```

Setelah 5 request (login rate limit), akan dapat response 429.

### Test dengan Script JS

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

### Verifikasi Headers

```bash
curl -I http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

Cari headers berikut:

```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```

## 🔓 Bypass Rate Limit (untuk Testing)

### Nonaktifkan sementara

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

## 📈 Monitor Penggunaan Rate Limit

### Log Hit Rate Limit

```php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)
        ->by($request->user()?->id ?: $request->ip())
        ->response(function (Request $request, array $headers) {
            // Log hit rate limit
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

### Cek Sisa Limit (Remaining Limit)

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
// ❌ BURUK: Satu rate limit untuk semua
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', ...);
    Route::get('/products', ...);
    Route::delete('/account', ...);
});

// ✅ BAIK: Rate limit sesuai sensitivitas
Route::middleware('throttle:login')->post('/login', ...);
Route::middleware('throttle:api')->get('/products', ...);
Route::middleware('throttle:sensitive')->delete('/account', ...);
```

### 2. Ketat untuk Operasi Tulis, Longgar untuk Operasi Baca

```php
// Operasi baca: 100/menit
RateLimiter::for('read', function (Request $request) {
    return Limit::perMinute(100);
});

// Operasi tulis: 20/menit
RateLimiter::for('write', function (Request $request) {
    return Limit::perMinute(20);
});
```

### 3. Lacak berdasarkan User ID, Bukan IP (jika terautentikasi)

```php
// ✅ BAIK: Lacak berdasarkan user ID
return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());

// ❌ BURUK: Lacak berdasarkan IP saja (bisa di-bypass dengan VPN)
return Limit::perMinute(60)->by($request->ip());
```

### 4. Pesan Kustom yang Informatif

```php
->response(function (Request $request, array $headers) {
    return response()->json([
        'success' => false,
        'message' => 'Anda telah mencapai batas request. Mohon tunggu ' .
                     ($headers['Retry-After'] ?? 60) . ' detik.',
        'retry_after' => $headers['Retry-After'] ?? 60,
        'limit' => $headers['X-RateLimit-Limit'] ?? 60,
    ], 429, $headers);
});
```

## 🔧 Opsi Konfigurasi

| Method                 | Deskripsi                                             |
| :--------------------- | :---------------------------------------------------- |
| `perMinute(60)`        | 60 request per menit                                  |
| `perHour(1000)`        | 1000 request per jam                                  |
| `perDay(10000)`        | 10000 request per hari                                |
| `by($key)`             | Lacak berdasarkan key spesifik (ID pengguna, IP, dll) |
| `response($callback)`  | Custom response saat limit terlampaui                 |
| `decayAfter($seconds)` | Custom decay time (waktu reset)                       |

## 📚 Referensi

-   [Laravel Rate Limiting Docs](https://laravel.com/docs/routing#rate-limiting)
-   [HTTP 429 Too Many Requests](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429)

---

**Rate limiting sudah aktif! API Anda sekarang terlindungi dari abuse! 🛡️**
