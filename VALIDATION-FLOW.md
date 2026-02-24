# 🔍 Validation Flow - How It Works

Panduan lengkap bagaimana Form Request Validation bekerja di Laravel API Starter Pack ini.

---

## 📊 Request Flow Diagram

```plaintext
1. Client Request (JSON)
   ↓
2. Route (routes/api.php atau routes/v1.php)
   ↓
3. Controller Method (UserController::store)
   ↓
4. Form Request Validation (UserStoreRequest)
   ↓ (jika valid)
5. Repository (UserRepository::store)
   ↓
6. Model (User::create)
   ↓
7. Response (JSON)
```

**Key Point:** Laravel otomatis jalankan validation SEBELUM masuk ke controller method!

---

## 🎯 Complete Example: Create User

Mari kita ikuti flow lengkap dari awal sampai akhir.

### **Step 1: Client Request**

User mengirim POST request:

```http
POST /api/user
Content-Type: application/json
Authorization: Bearer {token}

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secret123",
    "role": "admin"
}
```

### **Step 2: Route**

Request masuk ke route yang sudah didefinisikan:

**File:** `routes/v1.php`

```php
use App\Http\Controllers\Api\V1\UserController;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('user', UserController::class);
});
```

Laravel routing mengarahkan ke `UserController::store()`

### **Step 3: Controller**

**File:** `app/Http/Controllers/api/UserController.php`

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Store a newly created user
     */
    public function store(UserStoreRequest $request)
    {
        // Data sudah OTOMATIS ter-validasi oleh Laravel!
        // $request->validated() hanya return data yang valid
        return $this->userRepository->store($request->validated());
    }
}
```

**⚡ Magic Happens Here:**

Perhatikan parameter `UserStoreRequest $request` - ini adalah **Form Request**.

Laravel otomatis:

1. ✅ Instantiate `UserStoreRequest`
2. ✅ Jalankan method `authorize()`
3. ✅ Jalankan method `rules()`
4. ✅ Validate request data
5. ✅ **Jika GAGAL:** Return 422 dengan error messages
6. ✅ **Jika SUKSES:** Lanjut ke method `store()`

### **Step 4: Form Request Validation**

**File:** `app/Http/Requests/User/UserStoreRequest.php`

```php
<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // true = allow all authenticated users
        // Bisa diubah untuk check permissions
        return true;
    }

    /**
     * Get the validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string|exists:roles,name',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.exists' => 'Role tidak valid',
        ];
    }
}
```

**Validation Rules Explained:**

| Rule                 | Meaning                               |
| -------------------- | ------------------------------------- |
| `required`           | Field wajib diisi                     |
| `string`             | Harus berupa string                   |
| `max:255`            | Maksimal 255 karakter                 |
| `email`              | Harus format email valid              |
| `unique:users,email` | Email harus unik di table users       |
| `min:6`              | Minimal 6 karakter                    |
| `sometimes`          | Optional, hanya divalidasi jika ada   |
| `exists:roles,name`  | Harus exist di table roles kolom name |

**✅ Jika Valid:**

```php
// $request->validated() return:
[
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'secret123',
    'role' => 'admin'
]
```

Controller method dilanjutkan.

**❌ Jika Invalid:**

Laravel otomatis return response:

```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["Email sudah terdaftar"]
    }
}
```

HTTP Status: **422 Unprocessable Entity**

Controller method **TIDAK** dijalankan!

### **Step 5: Repository**

Jika validation sukses, controller call repository:

**File:** `app/Repositories/UserRepository.php`

```php
<?php

namespace App\Repositories;

use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Store new user
     */
    public function store(array $data)
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Create user
        $user = User::create($data);

        // Assign role
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        } else {
            $user->assignRole('user');
        }

        // Return formatted response
        return ResponseHelper::success(
            new UserResource($user),
            'User created successfully',
            201
        );
    }
}
```

### **Step 6: Model**

Model handle database insertion:

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, UUID, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

### **Step 7: Final Response**

Response dikembalikan ke client:

```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": "9d8f7a6b-5c4d-3e2f-1a0b-9c8d7e6f5a4b",
        "name": "John Doe",
        "email": "john@example.com",
        "roles": ["admin"],
        "created_at": "2025-12-25T10:00:00.000000Z",
        "updated_at": "2025-12-25T10:00:00.000000Z"
    }
}
```

HTTP Status: **201 Created**

---

## 📝 All Form Requests in Starter Pack

### **Authentication Requests**

| Request                | File                                         | Used In                      | Validation            |
| ---------------------- | -------------------------------------------- | ---------------------------- | --------------------- |
| `LoginStoreRequest`    | `app/Http/Requests/LoginStoreRequest.php`    | `AuthController::login()`    | email, password       |
| `RegisterStoreRequest` | `app/Http/Requests/RegisterStoreRequest.php` | `AuthController::register()` | name, email, password |

### **User Management Requests**

| Request                     | File                                                   | Used In                            | Validation                             |
| --------------------------- | ------------------------------------------------------ | ---------------------------------- | -------------------------------------- |
| `UserStoreRequest`          | `app/Http/Requests/User/UserStoreRequest.php`          | `UserController::store()`          | name, email, password, role            |
| `UserUpdateRequest`         | `app/Http/Requests/User/UserUpdateRequest.php`         | `UserController::update()`         | name, email (unique except self), role |
| `UserUpdatePasswordRequest` | `app/Http/Requests/User/UserUpdatePasswordRequest.php` | `UserController::updatePassword()` | password + confirmation                |

---

## 🔍 Detail Each Form Request

### **1. LoginStoreRequest**

**File:** `app/Http/Requests/LoginStoreRequest.php`

```php
public function rules(): array
{
    return [
        'email' => 'required|email',
        'password' => 'required|string',
    ];
}

public function messages(): array
{
    return [
        'email.required' => 'Email harus diisi',
        'email.email' => 'Format email tidak valid',
        'password.required' => 'Password harus diisi',
    ];
}
```

**Usage:**

```php
// AuthController.php
public function login(LoginStoreRequest $request)
{
    return $this->authRepository->login($request->validated());
}
```

**Test:**

```bash
# Valid request
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Invalid request (missing email)
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"password":"password"}'

# Response: 422
# {
#   "message": "The email field is required.",
#   "errors": {
#     "email": ["Email harus diisi"]
#   }
# }
```

### **2. RegisterStoreRequest**

**File:** `app/Http/Requests/RegisterStoreRequest.php`

```php
public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
    ];
}
```

**Usage:**

```php
// AuthController.php
public function register(RegisterStoreRequest $request)
{
    return $this->authRepository->register($request->validated());
}
```

### **3. UserStoreRequest**

Already explained above in the complete example.

### **4. UserUpdateRequest**

**File:** `app/Http/Requests/User/UserUpdateRequest.php`

```php
public function rules(): array
{
    // Get user ID from route parameter
    $userId = $this->route('user');

    return [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $userId,
        'role' => 'sometimes|string|exists:roles,name',
    ];
}
```

**Important:** `unique:users,email,' . $userId` → Ignore current user saat check unique email

**Usage:**

```php
// UserController.php
public function update(UserUpdateRequest $request, $id)
{
    return $this->userRepository->update($id, $request->validated());
}
```

### **5. UserUpdatePasswordRequest**

**File:** `app/Http/Requests/User/UserUpdatePasswordRequest.php`

```php
public function rules(): array
{
    return [
        'password' => 'required|string|min:6|confirmed',
    ];
}
```

**Note:** `confirmed` rule requires `password_confirmation` field with matching value.

**Request Example:**

```json
{
    "password": "newsecret123",
    "password_confirmation": "newsecret123"
}
```

---

## 🎨 Customization Examples

### **Example 1: Custom Authorization**

Hanya admin yang bisa create users:

```php
class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check if user has admin role
        return auth()->user()->hasRole('admin');
    }
}
```

**Response jika unauthorized:**

```json
{
    "message": "This action is unauthorized."
}
```

HTTP Status: **403 Forbidden**

### **Example 2: Complex Validation Rules**

```php
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'email' => [
            'required',
            'email',
            'unique:users,email',
            // Block specific email
            Rule::notIn(['admin@example.com', 'test@example.com']),
        ],
        'password' => [
            'required',
            'min:8',
            // Must have lowercase
            'regex:/[a-z]/',
            // Must have uppercase
            'regex:/[A-Z]/',
            // Must have number
            'regex:/[0-9]/',
            // Must have special char
            'regex:/[@$!%*#?&]/',
        ],
        'age' => 'required|integer|min:18|max:100',
        'phone' => 'required|regex:/^[0-9]{10,15}$/',
    ];
}

public function messages(): array
{
    return [
        'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial',
        'phone.regex' => 'Format nomor telepon tidak valid (10-15 digit)',
    ];
}
```

### **Example 3: Conditional Validation**

Jika role = admin, butuh special_code:

```php
public function rules(): array
{
    $rules = [
        'name' => 'required|string',
        'email' => 'required|email',
        'role' => 'required|string',
    ];

    // If role is admin, require special code
    if ($this->input('role') === 'admin') {
        $rules['special_code'] = 'required|string|size:10';
    }

    return $rules;
}
```

### **Example 4: Custom Validation Rule**

```php
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'email' => [
            'required',
            'email',
            // Custom unique check (exclude soft deleted)
            Rule::unique('users')->where(function ($query) {
                return $query->whereNull('deleted_at');
            }),
        ],
    ];
}
```

### **Example 5: After Validation Hook**

Auto-format data after validation:

```php
protected function passedValidation()
{
    // Convert email to lowercase
    $this->merge([
        'email' => strtolower($this->email),
        'name' => ucwords(strtolower($this->name)),
    ]);
}
```

### **Example 6: Prepare For Validation**

Modify data before validation:

```php
protected function prepareForValidation()
{
    // Remove spaces from phone number
    $this->merge([
        'phone' => preg_replace('/\s+/', '', $this->phone ?? ''),
    ]);
}
```

---

## 🧪 Testing Validation

### **Test 1: Valid Request**

```bash
curl -X POST http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "secret123",
    "role": "admin"
  }'
```

**Expected:** 201 Created

```json
{
  "success": true,
  "message": "User created successfully",
  "data": { ... }
}
```

### **Test 2: Invalid - Empty Name**

```bash
curl -X POST http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "",
    "email": "john@example.com",
    "password": "secret123"
  }'
```

**Expected:** 422 Unprocessable Entity

```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["Nama harus diisi"]
    }
}
```

### **Test 3: Invalid - Duplicate Email**

```bash
curl -X POST http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "admin@example.com",
    "password": "secret123"
  }'
```

**Expected:** 422 Unprocessable Entity

```json
{
    "message": "The email has already been taken.",
    "errors": {
        "email": ["Email sudah terdaftar"]
    }
}
```

### **Test 4: Invalid - Short Password**

```bash
curl -X POST http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "123"
  }'
```

**Expected:** 422 Unprocessable Entity

```json
{
    "message": "The password field must be at least 6 characters.",
    "errors": {
        "password": ["Password minimal 6 karakter"]
    }
}
```

### **Test 5: Invalid - Multiple Errors**

```bash
curl -X POST http://localhost:8000/api/user \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "",
    "email": "invalid-email",
    "password": "123"
  }'
```

**Expected:** 422 Unprocessable Entity

```json
{
    "message": "The name field is required. (and 2 more errors)",
    "errors": {
        "name": ["Nama harus diisi"],
        "email": ["Format email tidak valid"],
        "password": ["Password minimal 6 karakter"]
    }
}
```

---

## ⚡ Benefits of Form Request Validation

### **1. Clean Controller**

❌ **Without Form Request:**

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
    ], [
        'name.required' => 'Nama harus diisi',
        'email.unique' => 'Email sudah terdaftar',
        // ... more messages
    ]);

    return $this->userRepository->store($validated);
}
```

✅ **With Form Request:**

```php
public function store(UserStoreRequest $request)
{
    return $this->userRepository->store($request->validated());
}
```

### **2. Reusable**

Same validation bisa dipakai di multiple controllers.

### **3. Testable**

Easy to unit test validation rules.

### **4. Auto Response**

Laravel auto return 422 on failure - no manual handling needed.

### **5. Type Safety**

`$request->validated()` only returns validated data - extra fields removed.

### **6. Organized**

All validation logic in one place - easy to maintain.

---

## 📚 Laravel Validation Rules Reference

### **Common Rules**

| Rule                  | Description                     | Example                            |
| --------------------- | ------------------------------- | ---------------------------------- |
| `required`            | Field wajib ada                 | `'name' => 'required'`             |
| `nullable`            | Field boleh null                | `'phone' => 'nullable'`            |
| `sometimes`           | Validate only if present        | `'role' => 'sometimes'`            |
| `string`              | Must be string                  | `'name' => 'string'`               |
| `integer`             | Must be integer                 | `'age' => 'integer'`               |
| `numeric`             | Must be numeric                 | `'price' => 'numeric'`             |
| `boolean`             | Must be boolean                 | `'active' => 'boolean'`            |
| `array`               | Must be array                   | `'tags' => 'array'`                |
| `email`               | Must be valid email             | `'email' => 'email'`               |
| `url`                 | Must be valid URL               | `'website' => 'url'`               |
| `date`                | Must be valid date              | `'birthday' => 'date'`             |
| `min:value`           | Minimum value/length            | `'password' => 'min:6'`            |
| `max:value`           | Maximum value/length            | `'name' => 'max:255'`              |
| `between:min,max`     | Between min and max             | `'age' => 'between:18,65'`         |
| `in:foo,bar`          | Must be in list                 | `'status' => 'in:active,inactive'` |
| `not_in:foo,bar`      | Must NOT be in list             | `'role' => 'not_in:banned'`        |
| `unique:table,column` | Must be unique in table         | `'email' => 'unique:users,email'`  |
| `exists:table,column` | Must exist in table             | `'role_id' => 'exists:roles,id'`   |
| `confirmed`           | Must match `field_confirmation` | `'password' => 'confirmed'`        |
| `regex:pattern`       | Must match regex pattern        | `'phone' => 'regex:/^[0-9]+$/'`    |

[Full documentation](https://laravel.com/docs/validation#available-validation-rules)

---

## 🎓 Best Practices

1. ✅ **Always use Form Requests** for validation
2. ✅ **Keep validation rules simple** and readable
3. ✅ **Use custom messages** in Bahasa Indonesia
4. ✅ **Separate concerns** - validation in Request, business logic in Repository
5. ✅ **Use `sometimes`** for optional fields
6. ✅ **Use `nullable`** when null is acceptable
7. ✅ **Validate early** - fail fast before processing
8. ✅ **Test validation** with unit tests
9. ✅ **Document complex rules** with comments
10. ✅ **Reuse validation** across multiple endpoints

---

## 🎉 Conclusion

Form Request Validation di Laravel sangat powerful dan mudah digunakan:

-   ✅ Automatic validation sebelum masuk controller
-   ✅ Clean separation of concerns
-   ✅ Reusable dan testable
-   ✅ Auto-formatted error responses
-   ✅ Type-safe dengan `validated()` method

**Happy Validating! 🚀**
