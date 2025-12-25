# 🔒 CORS Configuration Guide

## 📍 Lokasi File

File konfigurasi CORS ada di: **`config/cors.php`**

## 🎯 Contoh Konfigurasi

### 1️⃣ Development (Allow Localhost)

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',      // React default
        'http://localhost:5173',      // Vite default
        'http://localhost:8080',      // Vue default
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 2️⃣ Production (Specific Domains Only)

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://yourdomain.com',
        'https://www.yourdomain.com',
        'https://app.yourdomain.com',
        'https://admin.yourdomain.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 3️⃣ Multiple Subdomains dengan Pattern

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://yourdomain.com',
    ],

    // Pattern untuk semua subdomain
    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.yourdomain\.com$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 4️⃣ Development + Production

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([
        // Development
        env('APP_ENV') === 'local' ? 'http://localhost:3000' : null,
        env('APP_ENV') === 'local' ? 'http://localhost:5173' : null,

        // Production
        env('APP_ENV') === 'production' ? 'https://yourdomain.com' : null,
        env('APP_ENV') === 'production' ? 'https://www.yourdomain.com' : null,
    ]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 5️⃣ Using Environment Variable

**`.env`:**

```env
# Development
FRONTEND_URL=http://localhost:3000

# Production
# FRONTEND_URL=https://yourdomain.com
```

**`config/cors.php`:**

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 6️⃣ Multiple Frontend URLs from .env

**`.env`:**

```env
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:5173,https://yourdomain.com"
```

**`config/cors.php`:**

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

## 📋 Penjelasan Setiap Option

### `paths`

Array path yang akan di-apply CORS policy.

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
```

-   `api/*` - Semua endpoint API
-   `sanctum/csrf-cookie` - Untuk CSRF protection Sanctum

### `allowed_methods`

HTTP methods yang diperbolehkan.

```php
// Allow all methods
'allowed_methods' => ['*'],

// Or specific methods only
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
```

### `allowed_origins`

Domain yang diperbolehkan akses API.

```php
// Allow all (NOT RECOMMENDED for production!)
'allowed_origins' => ['*'],

// Specific domains only (RECOMMENDED)
'allowed_origins' => [
    'https://yourdomain.com',
    'https://app.yourdomain.com',
],
```

⚠️ **JANGAN gunakan `['*']` di production!**

### `allowed_origins_patterns`

Pattern regex untuk domain yang diperbolehkan.

```php
'allowed_origins_patterns' => [
    '/^https:\/\/.*\.yourdomain\.com$/',  // Semua subdomain
    '/^https:\/\/.*\.staging\.yourdomain\.com$/',  // Semua staging subdomain
],
```

### `allowed_headers`

Headers yang diperbolehkan dalam request.

```php
// Allow all headers
'allowed_headers' => ['*'],

// Or specific headers only
'allowed_headers' => [
    'Content-Type',
    'Authorization',
    'X-Requested-With',
    'Accept',
],
```

### `exposed_headers`

Headers yang bisa diakses oleh frontend.

```php
'exposed_headers' => [
    'X-Total-Count',
    'X-Page-Count',
],
```

### `max_age`

Berapa lama browser cache preflight request (dalam detik).

```php
'max_age' => 0,           // No cache
'max_age' => 86400,       // 24 hours
'max_age' => 604800,      // 7 days
```

### `supports_credentials`

Apakah request boleh include credentials (cookies, authorization headers).

```php
'supports_credentials' => true,   // Required untuk Sanctum
'supports_credentials' => false,
```

⚠️ **Must be `true` jika pakai Sanctum!**

## 🔧 Cara Apply Konfigurasi

### 1. Update config file

Edit `config/cors.php` sesuai kebutuhan.

### 2. Clear config cache

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Test dari frontend

```javascript
// React/Vue/Angular example
fetch("http://localhost:8000/api/login", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        email: "admin@example.com",
        password: "password",
    }),
})
    .then((response) => response.json())
    .then((data) => console.log(data));
```

## ❌ Troubleshooting CORS Errors

### Error: "Access to fetch has been blocked by CORS policy"

**Solusi 1: Cek allowed_origins**

```php
'allowed_origins' => [
    'http://localhost:3000',  // Pastikan sesuai dengan frontend URL
],
```

**Solusi 2: Clear cache**

```bash
php artisan config:clear
```

**Solusi 3: Pastikan credentials true**

```php
'supports_credentials' => true,
```

### Error: "Wildcard '\*' cannot be used in combination with 'credentials'"

Jika `supports_credentials` = `true`, TIDAK boleh pakai wildcard `*`.

❌ **SALAH:**

```php
'allowed_origins' => ['*'],
'supports_credentials' => true,
```

✅ **BENAR:**

```php
'allowed_origins' => [
    'http://localhost:3000',
    'https://yourdomain.com',
],
'supports_credentials' => true,
```

### Error: "The CORS protocol does not allow specifying a wildcard"

Ganti wildcard dengan domain spesifik.

## 🌍 Real World Examples

### Example 1: React App + Laravel API

**Frontend (React) di `http://localhost:3000`**
**Backend (Laravel) di `http://localhost:8000`**

**`config/cors.php`:**

```php
'allowed_origins' => [
    'http://localhost:3000',
],
'supports_credentials' => true,
```

**Frontend code:**

```javascript
const login = async () => {
    const response = await fetch("http://localhost:8000/api/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        credentials: "include", // Important!
        body: JSON.stringify({ email, password }),
    });
    return response.json();
};
```

### Example 2: Production Multi-Domain

**Main site:** `https://yourdomain.com`
**Admin panel:** `https://admin.yourdomain.com`
**Mobile app web:** `https://app.yourdomain.com`

**`config/cors.php`:**

```php
'allowed_origins' => [
    'https://yourdomain.com',
    'https://admin.yourdomain.com',
    'https://app.yourdomain.com',
],
```

### Example 3: Development + Staging + Production

**`.env.local`:**

```env
CORS_ALLOWED_ORIGINS="http://localhost:3000"
```

**`.env.staging`:**

```env
CORS_ALLOWED_ORIGINS="https://staging.yourdomain.com"
```

**`.env.production`:**

```env
CORS_ALLOWED_ORIGINS="https://yourdomain.com,https://www.yourdomain.com"
```

**`config/cors.php`:**

```php
'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '')),
```

## 🔐 Security Best Practices

### 1. NEVER use wildcard in production

❌ DON'T:

```php
'allowed_origins' => ['*'],
```

✅ DO:

```php
'allowed_origins' => [
    'https://yourdomain.com',
],
```

### 2. Use specific methods

❌ DON'T:

```php
'allowed_methods' => ['*'],
```

✅ DO (if you want to be strict):

```php
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
```

### 3. Use HTTPS in production

❌ DON'T:

```php
'allowed_origins' => [
    'http://yourdomain.com',  // HTTP tidak aman!
],
```

✅ DO:

```php
'allowed_origins' => [
    'https://yourdomain.com',  // HTTPS aman
],
```

### 4. Set appropriate max_age

```php
'max_age' => 86400,  // 24 hours, reduce preflight requests
```

### 5. Only expose necessary headers

```php
'exposed_headers' => [
    'X-Total-Count',  // Only what frontend needs
],
```

## 📝 Testing CORS

### Test dengan cURL

```bash
curl -H "Origin: http://localhost:3000" \
     -H "Access-Control-Request-Method: POST" \
     -H "Access-Control-Request-Headers: Content-Type" \
     -X OPTIONS \
     --verbose \
     http://localhost:8000/api/login
```

Expected response headers:

```http
Access-Control-Allow-Origin: http://localhost:3000
Access-Control-Allow-Methods: POST
Access-Control-Allow-Headers: Content-Type
Access-Control-Allow-Credentials: true
```

### Test dengan Browser Console

```javascript
fetch("http://localhost:8000/api/login", {
    method: "OPTIONS",
    headers: {
        Origin: "http://localhost:3000",
    },
}).then((response) => {
    console.log("CORS headers:", response.headers);
});
```

## 📚 References

-   [MDN CORS Documentation](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS)
-   [Laravel CORS Documentation](https://laravel.com/docs/cors)
-   [fruitcake/laravel-cors Package](https://github.com/fruitcake/laravel-cors)

---

**Pro Tip:** Untuk production, selalu gunakan domain spesifik dan HTTPS! 🔒
