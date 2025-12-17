# 🚀 Laravel API Starter Pack

Starter pack Laravel API dengan Repository Pattern, Sanctum Authentication, dan Spatie Permission yang siap pakai untuk project baru.

## 📦 Tech Stack

- **Laravel**: 12.x
- **PHP**: 8.2+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Architecture**: Repository Pattern
- **UUID**: Primary Key

## ✨ Features

- ✅ Authentication (Login, Register, Logout, Me)
- ✅ Role & Permission Management (Spatie)
- ✅ Repository Pattern
- ✅ UUID as Primary Key
- ✅ Soft Deletes
- ✅ API Resources & Pagination
- ✅ Form Request Validation
- ✅ Standardized API Response
- ✅ Search Functionality
- ✅ CORS Configuration

## 🏗️ Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php
│   ├── Requests/
│   │   └── LoginStoreRequest.php
│   └── Resources/
│       ├── PaginateResource.php
│       └── UserResource.php
├── Interfaces/
│   ├── AuthRepositoryInterface.php
│   └── UserRepositoryInterface.php
├── Repositories/
│   ├── AuthRepository.php
│   └── UserRepository.php
├── Models/
│   ├── User.php
│   └── PersonalAccessToken.php
├── Traits/
│   └── UUID.php
├── Helpers/
│   └── ResponseHelper.php
└── Providers/
    └── RepositoryServiceProvider.php
```

## 📥 Installation

### 1. Install Laravel
```bash
composer create-project laravel/laravel project-name
cd project-name
```

### 2. Install Dependencies
```bash
composer require laravel/sanctum
composer require spatie/laravel-permission
```

### 3. Copy Files
Copy semua file dari starter pack ke project Laravel Anda:

```bash
# Copy traits
cp -r app/Traits your-project/app/

# Copy helpers
cp -r app/Helpers your-project/app/

# Copy interfaces
cp -r app/Interfaces your-project/app/

# Copy repositories
cp -r app/Repositories your-project/app/

# Copy providers
cp app/Providers/RepositoryServiceProvider.php your-project/app/Providers/

# Copy controllers
cp app/Http/Controllers/AuthController.php your-project/app/Http/Controllers/

# Copy requests
cp -r app/Http/Requests your-project/app/Http/Requests/

# Copy resources
cp -r app/Http/Resources your-project/app/Http/Resources/

# Copy models
cp app/Models/User.php your-project/app/Models/
cp app/Models/PersonalAccessToken.php your-project/app/Models/

# Copy migrations
cp database/migrations/* your-project/database/migrations/

# Copy seeders
cp database/seeders/* your-project/database/seeders/

# Copy config
cp config/sanctum.php your-project/config/
cp config/permission.php your-project/config/

# Copy routes
cp routes/api.php your-project/routes/
```

### 4. Update bootstrap/app.php (PENTING!)
**Laravel 12 tidak auto-load API routes!** Update `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ← Tambahkan baris ini!
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie Permission middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

### 5. Update bootstrap/providers.php
Tambahkan provider di `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class, // Add this
];
```

### 6. Environment Setup
Update `.env`:

```env
DB_CONNECTION=sqlite

# Atau gunakan MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

### 8. Generate Application Key
```bash
php artisan key:generate
```

## 🔑 Default Credentials

### Super Admin
- Email: `admin@example.com`
- Password: `password`

### Admin
- Email: `user@example.com`
- Password: `password`

## 📡 API Endpoints

**📖 Untuk cara protect routes dengan role/permission, lihat [MIDDLEWARE.md](MIDDLEWARE.md)**

### Authentication
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/login` | Login user | No |
| POST | `/api/register` | Register new user | No |
| POST | `/api/logout` | Logout user | Yes |
| GET | `/api/me` | Get current user | Yes |

### User Management
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/user` | Get all users | Yes |
| GET | `/api/user/all/paginated` | Get paginated users | Yes |
| GET | `/api/user/{id}` | Get user detail | Yes |
| POST | `/api/user` | Create user | Yes |
| PUT | `/api/user/{id}` | Update user | Yes |
| DELETE | `/api/user/{id}` | Delete user | Yes |
| PUT | `/api/user/{id}/update-password` | Update password | Yes |

## 🧪 Testing API

### 1. Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

Response:
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "access_token": "your-token-here",
    "token_type": "Bearer"
  }
}
```

### 2. Get Current User
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer your-token-here"
```

### 3. Get All Users (Paginated)
```bash
curl -X GET "http://localhost:8000/api/user/all/paginated?search=admin&per_page=10&page=1" \
  -H "Authorization: Bearer your-token-here"
```

## 🔨 How to Add New Module

### 1. Create Migration
```bash
php artisan make:migration create_products_table
```

### 2. Create Model
```bash
php artisan make:model Product
```

Add UUID trait dan soft deletes:
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
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
    }
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
        return ResponseHelper::success(null, 'Product deleted successfully');
    }
}
```

### 5. Register di RepositoryServiceProvider
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

### 9. Add Routes
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('product', ProductController::class);
    Route::get('/product/all/paginated', [ProductController::class, 'getAllPaginated']);
});
```

## 🎯 Best Practices

1. **Always use Repository Pattern** - Jangan taruh business logic di controller
2. **Use Form Requests** - Validasi terpisah dari controller
3. **Use API Resources** - Format response yang konsisten
4. **Use UUID** - Lebih secure dari auto-increment
5. **Use Soft Deletes** - Data integrity
6. **Use Search Scope** - Reusable search logic
7. **Use ResponseHelper** - Konsisten response format

## 🔒 Security

- CORS sudah dikonfigurasi
- Sanctum untuk API authentication
- Password di-hash otomatis
- Token expiration bisa diatur di config/sanctum.php
- Soft deletes untuk data integrity

## 📝 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // your data here
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["error detail"]
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "data": [...],
    "current_page": 1,
    "last_page": 10,
    "per_page": 10,
    "total": 100,
    "from": 1,
    "to": 10
  }
}
```

## 🤝 Contributing

Jika ingin menambahkan fitur atau memperbaiki bug, silakan buat pull request.

## 📄 License

MIT License

## 👨‍💻 Author

Created with ❤️ by Fadel

---

**Happy Coding! 🚀**
