# 🚀 Complete Step-by-Step Guide

Panduan lengkap dari clone project sampai bikin module baru dan semua fitur yang sudah kita buat.

---

## 📥 PART 1: Installation & Setup

### Step 1: Clone/Download Project

**Option A: Via GitHub (Recommended)**

```bash
git clone https://github.com/username/laravel-api-starter.git
cd laravel-api-starter
```

<br/>
**Option B: Download ZIP**

```bash
# Extract laravel-api-starter.zip
unzip laravel-api-starter.zip
cd laravel-api-starter
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Setup Database

**Option A: SQLite (Easiest)**

```bash
# Create database file
touch database/database.sqlite

# Update .env
DB_CONNECTION=sqlite
# Comment out these lines:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

<br/>
**Option B: MySQL**

```bash
# Create database
mysql -u root -p
CREATE DATABASE api_starter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Update .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_starter
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Run seeders (create default users & roles)
php artisan db:seed
```

<br/>
**Default Users Created:**

-   **Super Admin:** admin@example.com / password
-   **Admin:** user@example.com / password
-   **User:** test@example.com / password

### Step 5: Start Server

```bash
php artisan serve
```

Server running at: `http://localhost:8000`

### Step 6: Test API

```bash
# Test login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

**Response:**

```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "access_token": "1|xxxxx...",
    "token_type": "Bearer",
    "user": { ... }
  }
}
```

### Step 7: View Documentation Website

Open browser:

```plaintext
http://localhost:8000/dokumentasi
```

**✅ Installation Complete!**

---

## 🎯 PART 2: Creating New Module (Product Example)

Mari kita buat module **Product** dari awal sampai akhir!

### Step 1: Create Migration

```bash
php artisan make:migration create_products_table
```

<br/>
**File:** `database/migrations/xxxx_create_products_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run migration:

```bash
php artisan migrate
```

### Step 2: Create Model

**File:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use UUID, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    /**
     * Scope untuk filter by category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
```

### Step 3: Create Interface

**File:** `app/Interfaces/ProductRepositoryInterface.php`

```php
<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function index($perPage, $search);
    public function getAllPaginated($perPage, $search);
    public function store(array $data);
    public function show($id);
    public function update($id, array $data);
    public function destroy($id);
}
```

### Step 4: Create Repository

**File:** `app/Repositories/ProductRepository.php`

```php
<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products (standard pagination)
     */
    public function index($perPage, $search)
    {
        $products = Product::search($search)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return ResponseHelper::success(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    /**
     * Get all products (custom pagination)
     */
    public function getAllPaginated($perPage, $search)
    {
        $products = Product::search($search)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return ResponseHelper::success(
            new PaginateResource($products, ProductResource::class),
            'Products retrieved successfully'
        );
    }

    /**
     * Store new product
     */
    public function store(array $data)
    {
        $product = Product::create($data);

        return ResponseHelper::success(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    /**
     * Show single product
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found', 404);
        }

        return ResponseHelper::success(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    /**
     * Update product
     */
    public function update($id, array $data)
    {
        $product = Product::find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found', 404);
        }

        $product->update($data);

        return ResponseHelper::success(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Delete product (soft delete)
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ResponseHelper::error('Product not found', 404);
        }

        $product->delete();

        return ResponseHelper::success(
            null,
            'Product deleted successfully'
        );
    }
}
```

### Step 5: Create API Resource

**File:** `app/Http/Resources/ProductResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'category' => $this->category,
            'image' => $this->image,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

### Step 6: Create Form Requests

**File:** `app/Http/Requests/Product/ProductStoreRequest.php`

```php
<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk harus diisi',
            'name.max' => 'Nama produk maksimal 255 karakter',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh negatif',
            'stock.required' => 'Stok harus diisi',
            'stock.integer' => 'Stok harus berupa angka bulat',
            'stock.min' => 'Stok tidak boleh negatif',
        ];
    }
}
```

**File:** `app/Http/Requests/Product/ProductUpdateRequest.php`

```php
<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Nama produk maksimal 255 karakter',
            'price.numeric' => 'Harga harus berupa angka',
            'price.min' => 'Harga tidak boleh negatif',
            'stock.integer' => 'Stok harus berupa angka bulat',
            'stock.min' => 'Stok tidak boleh negatif',
        ];
    }
}
```

### Step 7: Create Controller

**File:** `app/Http/Controllers/Api/V1/ProductController.php`

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');

        return $this->productRepository->index($perPage, $search);
    }

    /**
     * Display a listing with custom pagination
     */
    public function getAllPaginated(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');

        return $this->productRepository->getAllPaginated($perPage, $search);
    }

    /**
     * Store a newly created product
     */
    public function store(ProductStoreRequest $request)
    {
        return $this->productRepository->store($request->validated());
    }

    /**
     * Display the specified product
     */
    public function show($id)
    {
        return $this->productRepository->show($id);
    }

    /**
     * Update the specified product
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        return $this->productRepository->update($id, $request->validated());
    }

    /**
     * Remove the specified product
     */
    public function destroy($id)
    {
        return $this->productRepository->destroy($id);
    }
}
```

### Step 8: Register Repository in Service Provider

**File:** `app/Providers/RepositoryServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;  // ← Tambah ini
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\ProductRepository;  // ← Tambah ini
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);  // ← Tambah ini
    }

    public function boot(): void
    {
        //
    }
}
```

### Step 9: Add Routes

**File:** `routes/v1.php`

```php
use App\Http\Controllers\Api\V1\ProductController;  // ← Tambah ini

// ... existing routes ...

// Product Routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // ... existing routes ...

    // Products
    Route::apiResource('product', ProductController::class);
    Route::get('/product/all/paginated', [ProductController::class, 'getAllPaginated']);
});
```

### Step 10: Test API

**Create Product:**

```bash
curl -X POST http://localhost:8000/api/v1/product \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop ASUS ROG",
    "description": "Gaming laptop with RTX 4090",
    "price": 25000000,
    "stock": 10,
    "category": "Electronics"
  }'
```

**Get All Products:**

```bash
curl -X GET "http://localhost:8000/api/v1/product?search=laptop&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Get Single Product:**

```bash
curl -X GET http://localhost:8000/api/v1/product/{id} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Update Product:**

```bash
curl -X PUT http://localhost:8000/api/v1/product/{id} \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "price": 23000000,
    "stock": 8
  }'
```

**Delete Product:**

```bash
curl -X DELETE http://localhost:8000/api/v1/product/{id} \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**✅ Product Module Complete!**

---

## 🔐 PART 3: All Features Explained

### Feature 1: Rate Limiting

**Already configured!** No setup needed.

**How it works:**

-   Login: 5 attempts/minute
-   Register: 3 attempts/hour
-   API calls: 60/minute
-   Sensitive operations: 10/minute

**Custom rate limit:**

```php
Route::middleware('throttle:30,1')->get('/my-endpoint', ...);
```

### Feature 2: API Versioning

**Already set up!** Routes available at `/api/v1/*`

**Add V2:**

1. Create `routes/v2.php`
2. Copy controllers to `app/Http/Controllers/Api/V2/`
3. Register in `bootstrap/app.php`:

```php
Route::prefix('api/v2')
    ->middleware('api')
    ->group(base_path('routes/v2.php'));
```

### Feature 3: Activity Logging

**Foundation ready!** Model and migration included.

**Log activity:**

```php
use App\Models\ActivityLog;

ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'created',
    'model' => 'Product',
    'model_id' => $product->id,
    'description' => 'Product created: ' . $product->name,
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

**Get logs:**

```php
$logs = ActivityLog::byUser(auth()->id())->latest()->get();
```

### Feature 4: File Upload (Planned)

**To implement:**

1. Add storage link: `php artisan storage:link`
2. Upload file in controller:

```php
$path = $request->file('image')->store('products', 'public');
$product->update(['image' => $path]);
```

### Feature 5: Email Verification (Planned)

**To implement:**

1. User model implements `MustVerifyEmail`
2. Send verification: `$user->sendEmailVerificationNotification()`
3. Protect routes: `Route::middleware('verified')->group(...)`

### Feature 6: Password Reset (Planned)

**To implement:**

1. Create reset controller
2. Use `Password` facade for reset links
3. Create email template

### Feature 7: Refresh Token (Planned)

**To implement:**

1. Create separate token for refresh
2. Set expiration times
3. Create refresh endpoint

---

## 📖 PART 4: Documentation Website

**Already live!** Visit: `http://localhost:8000/dokumentasi`

**Features:**

-   ✅ All 14 documentation pages
-   ✅ Responsive design
-   ✅ Search-friendly
-   ✅ Markdown support
-   ✅ Beautiful UI

**Customize:**
Edit `app/Http/Controllers/DocumentationController.php` to add new docs.

---

## 🎯 PART 5: Quick Reference

### Authentication Endpoints

```bash
# Login
POST /api/v1/login
Body: { "email": "admin@example.com", "password": "password" }

# Register
POST /api/v1/register
Body: { "name": "John", "email": "john@example.com", "password": "secret123" }

# Logout
POST /api/v1/logout
Headers: Authorization: Bearer {token}

# Get Current User
GET /api/v1/me
Headers: Authorization: Bearer {token}
```

### User Endpoints

```bash
# List Users
GET /api/v1/user?search=john&per_page=10
Headers: Authorization: Bearer {token}

# Create User
POST /api/v1/user
Headers: Authorization: Bearer {token}
Body: { "name": "...", "email": "...", "password": "...", "role": "admin" }

# Update User
PUT /api/v1/user/{id}
Headers: Authorization: Bearer {token}
Body: { "name": "...", "email": "..." }

# Delete User
DELETE /api/v1/user/{id}
Headers: Authorization: Bearer {token}
```

### Response Format

**Success:**

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error:**

```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

**Validation Error:**

```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["Email sudah terdaftar"]
    }
}
```

---

## 🛠️ PART 6: Troubleshooting

### Issue: Routes not found

**Solution:**

```bash
php artisan route:clear
php artisan config:clear
php artisan route:list
```

### Issue: 500 Error

**Solution:**

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Issue: Migration error

**Solution:**

```bash
php artisan migrate:fresh --seed
```

### Issue: Token not working

**Solution:**
Check headers format:

```
Authorization: Bearer 1|xxxxx...
```

---

## 🔧 TROUBLESHOOTING: Documentation Website

### Issue: All routes showing 404

**Quick Fix:**

```bash
# 1. Clear all cache
php artisan route:clear
php artisan config:clear
php artisan cache:clear

# 2. Check routes exist
php artisan route:list | grep dokumentasi

# 3. Restart server
# Press Ctrl+C to stop, then:
php artisan serve
```

**Checklist:**

-   [ ] File `routes/web.php` exists with documentation routes
-   [ ] Controller `app/Http/Controllers/DocumentationController.php` exists
-   [ ] All `.md` files exist in root directory
-   [ ] Views exist in `resources/views/documentation/`
-   [ ] Server restarted after copying files

**Still not working?**

Check logs:

```bash
tail -f storage/logs/laravel.log
```

Enable debug in `.env`:

```env
APP_DEBUG=true
```

Then visit the URL again and check the detailed error message.

---

## 🎉 Congratulations!

Anda sekarang punya:

-   ✅ Production-ready API
-   ✅ Clean architecture dengan Repository Pattern
-   ✅ Rate limiting & security
-   ✅ API versioning
-   ✅ Beautiful documentation website
-   ✅ Complete CRUD example (Product)
-   ✅ 14 comprehensive documentation pages

**Next Steps:**

1. Build your own modules (follow Product example)
2. Customize documentation website
3. Add more features (file upload, email, etc)
4. Deploy to production!

**Happy Coding! 🚀**
