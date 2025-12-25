# 📦 API Versioning Guide

API versioning memungkinkan Anda maintain multiple versions dari API secara bersamaan, sehingga backward compatibility terjaga.

## 📍 Struktur

```bash
routes/
├── api.php       # Legacy/unversioned routes (optional)
└── v1.php        # Version 1 routes

app/Http/Controllers/
├── Api/
│   └── V1/
│       ├── AuthController.php
│       └── UserController.php
└── Controller.php
```

## 🎯 URL Structure

### Version 1 (Current)

```plaintext
https://api.yourdomain.com/api/v1/login
https://api.yourdomain.com/api/v1/user
https://api.yourdomain.com/api/v1/products
```

### Legacy/Unversioned (Optional)

```plaintext
https://api.yourdomain.com/api/login
https://api.yourdomain.com/api/user
```

## 🚀 Current Implementation

### Routes

-   **`routes/v1.php`** - All V1 endpoints
-   **Prefix**: `/api/v1`

### Controllers

-   **`app/Http/Controllers/Api/V1/`**

### Available Endpoints

| Method | Endpoint            | Description  |
| ------ | ------------------- | ------------ |
| POST   | `/api/v1/login`     | Login        |
| POST   | `/api/v1/register`  | Register     |
| POST   | `/api/v1/logout`    | Logout       |
| GET    | `/api/v1/me`        | Current user |
| GET    | `/api/v1/user`      | List users   |
| POST   | `/api/v1/user`      | Create user  |
| GET    | `/api/v1/user/{id}` | Get user     |
| PUT    | `/api/v1/user/{id}` | Update user  |
| DELETE | `/api/v1/user/{id}` | Delete user  |

## 📝 How to Add New Version (V2)

### Step 1: Create V2 Directory

```bash
mkdir -p app/Http/Controllers/Api/V2
```

### Step 2: Copy Controllers

```bash
cp -r app/Http/Controllers/Api/V1/* app/Http/Controllers/Api/V2/
```

### Step 3: Update Namespace

Update namespace di semua V2 controllers:

```php
<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
// ...
```

### Step 4: Create V2 Routes

**`routes/v2.php`:**

```php
<?php

use App\Http\Controllers\Api\V2\AuthController;
use App\Http\Controllers\Api\V2\UserController;
use Illuminate\Support\Facades\Route;

// V2 routes here...
Route::post('/login', [AuthController::class, 'login']);
// ...
```

### Step 5: Register V2 in bootstrap/app.php

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // API Version 1
            Route::prefix('api/v1')
                ->middleware('api')
                ->group(base_path('routes/v1.php'));

            // API Version 2
            Route::prefix('api/v2')
                ->middleware('api')
                ->group(base_path('routes/v2.php'));
        },
    )
    // ...
```

### Step 6: Modify V2 as Needed

Now you can modify V2 controllers/logic without breaking V1!

## 🔄 Version Migration Strategy

### Scenario: Breaking Changes

**V1 Response:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": "123",
            "name": "John Doe"
        }
    }
}
```

**V2 Response (Breaking Change):**

```json
{
    "status": "success",
    "result": {
        "user": {
            "userId": "123",
            "fullName": "John Doe"
        }
    }
}
```

### Implementation

**V1 Controller:**

```php
namespace App\Http\Controllers\Api\V1;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);

        return ResponseHelper::success([
            'user' => new UserResource($user)
        ]);
    }
}
```

**V2 Controller:**

```php
namespace App\Http\Controllers\Api\V2;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Different response structure
        return response()->json([
            'status' => 'success',
            'result' => [
                'user' => new UserResourceV2($user)
            ]
        ]);
    }
}
```

## 📋 Best Practices

### 1. Version Based on Breaking Changes

✅ **DO create new version for:**

-   Response structure changes
-   Required field changes
-   Authentication method changes
-   Endpoint URL changes

❌ **DON'T create new version for:**

-   Adding optional fields
-   Bug fixes
-   Performance improvements
-   Internal refactoring

### 2. Keep Old Versions Stable

```php
// V1 - Keep stable, no breaking changes
namespace App\Http\Controllers\Api\V1;

class ProductController extends Controller
{
    public function index()
    {
        // Original implementation
        // DO NOT modify this
    }
}
```

### 3. Deprecation Notice

Add deprecation warnings to old versions:

```php
class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'deprecated' => true,
            'message' => 'This endpoint is deprecated. Please use /api/v2/user',
            'deprecation_date' => '2025-12-31',
            'data' => $data,
        ]);
    }
}
```

### 4. Set Default Version

```php
// Redirect /api/user to /api/v1/user
Route::redirect('/api/user', '/api/v1/user');
```

### 5. Version in Header (Alternative)

Instead of URL versioning, you can use headers:

```php
Route::middleware('api')->group(function () {
    Route::post('/login', function (Request $request) {
        $version = $request->header('API-Version', 'v1');

        if ($version === 'v2') {
            return app(V2\AuthController::class)->login($request);
        }

        return app(V1\AuthController::class)->login($request);
    });
});
```

**Client request:**

```bash
curl -X POST https://api.yourdomain.com/api/login \
  -H "API-Version: v2" \
  -H "Content-Type: application/json"
```

## 🧪 Testing Different Versions

### Test V1

```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### Test V2

```bash
curl -X POST http://localhost:8000/api/v2/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

## 📊 Version Lifecycle

```plaintext
V1 (Current) ──────────────────────────────────────┐
                                                    │
V2 (Beta) ───────────┐                             │
                     │                              │
V2 (Stable) ─────────┴──────────────────────────┐  │
                                                 │  │
V1 (Deprecated) ─────────────────────────────────┴──┤
                                                    │
V1 (Sunset) ────────────────────────────────────────┘

Timeline:
- V2 Beta: 3 months
- V1 Deprecated: 6 months warning
- V1 Sunset: Remove completely
```

## 🔔 Client Migration Guide

### For API Consumers

**Step 1: Check current version**

```javascript
const response = await fetch("https://api.yourdomain.com/api/v1/user");
```

**Step 2: Test new version**

```javascript
const response = await fetch("https://api.yourdomain.com/api/v2/user");
```

**Step 3: Update client code**

```javascript
// Update all endpoints from v1 to v2
const BASE_URL = "https://api.yourdomain.com/api/v2";
```

## 📝 Documentation per Version

### Swagger/OpenAPI

**`docs/v1/openapi.yaml`**

```yaml
openapi: 3.0.0
info:
    title: API V1
    version: 1.0.0
paths:
    /api/v1/login:
        post:
            summary: Login
```

**`docs/v2/openapi.yaml`**

```yaml
openapi: 3.0.0
info:
    title: API V2
    version: 2.0.0
paths:
    /api/v2/login:
        post:
            summary: Login (V2)
```

## 🎯 When to Version?

### Major Version (v1 → v2)

-   Complete API redesign
-   Authentication method change
-   Major response structure change

### Minor Version (v1.0 → v1.1)

-   New endpoints
-   New optional fields
-   Bug fixes

### Patch Version (v1.0.0 → v1.0.1)

-   Bug fixes only
-   No new features
-   No breaking changes

## 🚫 Common Mistakes

### ❌ DON'T: Modify old versions

```php
// app/Http/Controllers/Api/V1/UserController.php
public function index()
{
    // DON'T add new logic here if it breaks clients
    $users = User::with('newRelation')->get(); // Breaking!
}
```

### ✅ DO: Create new version

```php
// app/Http/Controllers/Api/V2/UserController.php
public function index()
{
    // Add new logic in V2
    $users = User::with('newRelation')->get(); // OK!
}
```

## 📚 References

-   [REST API Versioning](https://restfulapi.net/versioning/)
-   [API Versioning Best Practices](https://www.freecodecamp.org/news/how-to-version-a-rest-api/)

---

**Your API is now version-ready! 📦**
