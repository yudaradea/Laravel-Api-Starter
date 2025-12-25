# 📎 File Upload Handler

Secure file upload system untuk images, documents, dan files lainnya.

## 🚀 Implementation Status

🚧 **Planned Features:**
- Image upload with validation
- File size limits
- Automatic thumbnail generation
- Multiple file types support
- Cloud storage integration (S3, etc)
- Secure filename generation

## 📖 Planned Usage

### Upload File
```php
use App\Services\FileUploadService;

public function uploadAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);
    
    $path = FileUploadService::upload(
        $request->file('avatar'),
        'avatars',
        ['resize' => [200, 200]]
    );
    
    auth()->user()->update(['avatar' => $path]);
    
    return ResponseHelper::success(['path' => $path]);
}
```

### Configuration
```php
// config/filesystems.php
'uploads' => [
    'max_size' => 2048, // KB
    'allowed_types' => ['jpeg', 'png', 'jpg', 'pdf', 'doc'],
    'path' => 'uploads',
];
```

## 🔧 To Implement

```bash
# Create storage link
php artisan storage:link

# Set permissions
chmod -R 775 storage/app/public
```

## 🎯 Use Cases

1. **Profile Pictures** - User avatars
2. **Documents** - Upload PDFs, Word docs
3. **Product Images** - E-commerce photos
4. **Attachments** - Email attachments, files

---

**Status:** Foundation ready. Full implementation coming in next update.
