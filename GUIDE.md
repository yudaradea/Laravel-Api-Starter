# 🚀 Quick Start Guide

## Cara Cepat Menggunakan Starter Pack

### Metode 1: Manual Installation

```bash
# 1. Create Laravel project
composer create-project laravel/laravel my-api-project
cd my-api-project

# 2. Install dependencies
composer require laravel/sanctum
composer require spatie/laravel-permission

# 3. Copy all files from starter pack
cp -r /path/to/starter-pack/app/* app/
cp -r /path/to/starter-pack/database/* database/
cp /path/to/starter-pack/routes/api.php routes/
cp /path/to/starter-pack/config/*.php config/

# 4. Update bootstrap/providers.php
# Add this line:
# App\Providers\RepositoryServiceProvider::class,

# 5. Setup database
touch database/database.sqlite

# 6. Update .env
DB_CONNECTION=sqlite

# 7. Run migrations and seeders
php artisan migrate:fresh --seed

# 8. Start server
php artisan serve
```

### Metode 2: Using Installation Script

```bash
# Give permission
chmod +x install.sh

# Run installation
./install.sh my-api-project
```

## Testing API

### 1. Import Postman Collection
Import `postman_collection.json` ke Postman

### 2. Test Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### 3. Get Current User
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Module Generator

Untuk mempercepat pembuatan module baru, gunakan template berikut:

### 1. Create Model
```bash
php artisan make:model Product
```

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
    ];

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
}
```

### 2. Create Migration
```bash
php artisan make:migration create_products_table
```

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('stock')->default(0);
        $table->softDeletes();
        $table->timestamps();
    });
}
```

### 3. Create Interface
```php
<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function index($perPage, $search);
    public function getAllPaginated($perPage, $search);
    public function store(array $data);
    public function show($id);
    public function update(array $data, $id);
    public function destroy($id);
}
```

### 4. Create Repository
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
    public function index($perPage, $search)
    {
        $products = Product::search($search)->paginate($perPage);
        return ResponseHelper::success(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    }

    public function getAllPaginated($perPage, $search)
    {
        $products = Product::search($search)->paginate($perPage);
        return ResponseHelper::success(
            new PaginateResource($products, ProductResource::class),
            'Products retrieved successfully'
        );
    }

    public function store(array $data)
    {
        $product = Product::create($data);
        return ResponseHelper::success(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return ResponseHelper::success(
            new ProductResource($product),
            'Product retrieved successfully'
        );
    }

    public function update(array $data, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return ResponseHelper::success(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return ResponseHelper::success(
            null,
            'Product deleted successfully'
        );
    }
}
```

### 5. Register in RepositoryServiceProvider
```php
$this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
```

### 6. Create Controller
```php
<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        return $this->productRepository->index($perPage, $search);
    }

    public function getAllPaginated(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        return $this->productRepository->getAllPaginated($perPage, $search);
    }

    public function store(ProductStoreRequest $request)
    {
        return $this->productRepository->store($request->validated());
    }

    public function show($id)
    {
        return $this->productRepository->show($id);
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        return $this->productRepository->update($request->validated(), $id);
    }

    public function destroy($id)
    {
        return $this->productRepository->destroy($id);
    }
}
```

### 7. Create Form Requests
```bash
php artisan make:request Product/ProductStoreRequest
php artisan make:request Product/ProductUpdateRequest
```

### 8. Create Resource
```bash
php artisan make:resource ProductResource
```

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
```

### 9. Add Routes
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('product', ProductController::class);
    Route::get('/product/all/paginated', [ProductController::class, 'getAllPaginated']);
});
```

## Tips & Tricks

### 1. Caching
```php
// In your repository
use Illuminate\Support\Facades\Cache;

public function index($perPage, $search)
{
    $cacheKey = "products_{$perPage}_{$search}";
    
    return Cache::remember($cacheKey, 3600, function () use ($perPage, $search) {
        $products = Product::search($search)->paginate($perPage);
        return ResponseHelper::success(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    });
}
```

### 2. File Upload
```php
// In your repository
public function store(array $data)
{
    if (isset($data['image'])) {
        $data['image'] = $data['image']->store('products', 'public');
    }
    
    $product = Product::create($data);
    return ResponseHelper::success(
        new ProductResource($product),
        'Product created successfully',
        201
    );
}
```

### 3. Relationships
```php
// In your repository
public function index($perPage, $search)
{
    $products = Product::with(['category', 'supplier'])
        ->search($search)
        ->paginate($perPage);
        
    return ResponseHelper::success(
        ProductResource::collection($products),
        'Products retrieved successfully'
    );
}
```

### 4. Middleware Permission
```php
// In your route - Protect by role
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('product', ProductController::class);
});

// Protect by permission
Route::middleware(['auth:sanctum', 'permission:view products'])->group(function () {
    Route::get('/product', [ProductController::class, 'index']);
});

// Protect by role OR permission
Route::middleware(['auth:sanctum', 'role_or_permission:admin|view products'])->group(function () {
    Route::get('/product', [ProductController::class, 'index']);
});

// Or in controller constructor
public function __construct(ProductRepositoryInterface $productRepository)
{
    $this->productRepository = $productRepository;
    
    // Apply middleware to specific methods
    $this->middleware('permission:view products')->only(['index', 'show']);
    $this->middleware('permission:create products')->only('store');
    $this->middleware('permission:edit products')->only('update');
    $this->middleware('permission:delete products')->only('destroy');
}

// Check in code
if ($user->hasRole('admin')) {
    // Do something
}

if ($user->hasPermissionTo('edit products')) {
    // Do something
}

// Multiple roles/permissions
if ($user->hasAnyRole(['admin', 'super-admin'])) {
    // Do something
}

if ($user->hasAllPermissions(['edit products', 'delete products'])) {
    // Do something
}
```

## Troubleshooting

### Error: Class not found
```bash
composer dump-autoload
```

### Error: SQLSTATE connection refused
```bash
# Check .env database configuration
# For SQLite, make sure database file exists
touch database/database.sqlite
```

### Error: Token mismatch
```bash
php artisan config:clear
php artisan cache:clear
```

## Additional Commands

```bash
# Clear all cache
php artisan optimize:clear

# Generate IDE helper
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate

# Code formatting
composer require --dev laravel/pint
./vendor/bin/pint

# Testing
php artisan test
```

---

Happy Coding! 🚀
