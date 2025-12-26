# 🚀 Panduan Pengembangan Cepat (Quick Start Guide)

Panduan ini ditujukan untuk developer yang ingin menambahkan fitur atau modul baru ke dalam aplikasi ini menggunakan Repository Pattern yang sudah disediakan.

---

## 🏗️ Cara Membuat Module Baru

Katakanlah kita ingin membuat fitur manajemen **Produk (Product)**. Berikut adalah langkah-langkah standarnya.

### Langkah 1: Buat Model & Migration

Jalankan perintah Artisan untuk membuat Model beserta file migrasinya.

```bash
php artisan make:model Product -m
```

**Edit Model `app/Models/Product.php`:**

Kita menggunakan Trait `UUID` agar primary key otomatis menjadi UUID, dan `SoftDeletes` agar data tidak langsung hilang saat dihapus.

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

    // Scope untuk fitur pencarian
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

**Edit Migration `database/migrations/xxxx_xx_xx_create_products_table.php`:**

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->uuid('id')->primary(); // PENTING: Gunakan UUID
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('stock')->default(0);
        $table->softDeletes();
        $table->timestamps();
    });
}
```

Jalankan migrasi:

```bash
php artisan migrate
```

### Langkah 2: Buat Interface Repository

Buat file baru di `app/Interfaces/ProductRepositoryInterface.php`. Ini berfungsi sebagai kontrak agar kode kita rapi dan mudah di-testing.

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

### Langkah 3: Buat Repository Implementation

Buat file baru di `app/Repositories/ProductRepository.php`. Di sinilah logika database disimpan.

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
        // Menggunakan PaginateResource untuk format response yang konsisten
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

### Langkah 4: Daftarkan Repository

Agar Laravel tahu bahwa `ProductRepositoryInterface` harus menggunakan `ProductRepository`, daftarkan di `app/Providers/RepositoryServiceProvider.php`.

```php
// Di dalam method register()
$this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
```

### Langkah 5: Buat Form Request (Validasi)

Untuk menjaga Controller tetap bersih, buat class validasi terpisah.

```bash
php artisan make:request Product/ProductStoreRequest
php artisan make:request Product/ProductUpdateRequest
```

**Contoh `ProductStoreRequest`:**

```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'description' => 'nullable|string',
    ];
}
```

### Langkah 6: Buat API Resource (Transformasi Output)

Agar format JSON yang dikembalikan seragam.

```bash
php artisan make:resource ProductResource
```

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
        'price' => $this->price,
        'stock' => $this->stock,
        // ... field lainnya
    ];
}
```

### Langkah 7: Buat Controller

Terakhir, buat Controller untuk menghubungkan semuanya.

```bash
php artisan make:controller ProductController --api
```

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

    // Dependency Injection Repository
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;

        // Setup Permission Middleware (Opsional tapi Recommended)
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only('store');
        $this->middleware('permission:edit products')->only('update');
        $this->middleware('permission:delete products')->only('destroy');
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', '');
        return $this->productRepository->index($perPage, $search);
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

### Langkah 8: Daftarkan Route

Buka `routes/v1.php`:

```php
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('product', ProductController::class);
});
```

Selesai! Anda sudah memiliki module API `Products` yang lengkap, aman, dan terstandarisasi.

---

## 🛠️ Tips & Trik Pengembangan

### 1. Menggunakan Caching

Untuk performa tinggi, gunakan caching di Repository.

```php
use Illuminate\Support\Facades\Cache;

public function index($perPage, $search)
{
    $cacheKey = "products_{$perPage}_{$search}";

    return Cache::remember($cacheKey, 3600, function () use ($perPage, $search) {
        // Query database yang berat
        $products = Product::search($search)->paginate($perPage);
        return ResponseHelper::success(
            ProductResource::collection($products),
            'Products retrieved successfully'
        );
    });
}
```

### 2. File Upload Service

Gunakan `FileUploadService` untuk menangani upload gambar dengan mudah.

```php
use App\Services\FileUploadService;

// Di Repository
if (isset($data['image'])) {
    // Upload akan masuk ke storage/app/public/products/{hash}.jpg
    $data['image'] = FileUploadService::upload($data['image'], 'products');
}
```

### 3. Debugging

Jika ada masalah, gunakan perintah ini:

```bash
# Bersihkan cache config
php artisan config:clear

# Dump autoload composer (jika class tidak ditemukan)
composer dump-autoload

# Cek list route
php artisan route:list
```

---

Selamat Berkarya! 🚀
