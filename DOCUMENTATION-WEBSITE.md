# 🌐 Documentation Website Setup

Panduan setup documentation website yang cantik dengan Tailwind CSS.

## 📦 What's Included

Website dokumentasi ini include:

-   ✅ Home page dengan semua kategori dokumentasi
-   ✅ Individual documentation pages dengan sidebar navigation
-   ✅ Responsive design (mobile-friendly)
-   ✅ Tailwind CSS CDN (no build step needed!)
-   ✅ Markdown parser untuk render .md files
-   ✅ Previous/Next navigation
-   ✅ Syntax highlighting untuk code blocks
-   ✅ Beautiful tables, lists, and blockquotes

## 🚀 Quick Start

### Step 1: Copy Files

Semua files sudah include di starter pack:

```bash
app/Http/Controllers/
└── DocumentationController.php

resources/views/documentation/
├── layout.blade.php
├── index.blade.php
└── show.blade.php

routes/
└── web.php
```

### Step 2: Access Documentation

Start Laravel server:

```bash
php artisan serve
```

Buka browser:

```plaintext
http://localhost:8000/dokumentasi
```

That's it! 🎉

## 📍 Routes

| URL                            | Description                 |
| ------------------------------ | --------------------------- |
| `/dokumentasi`                 | Home page (list semua docs) |
| `/dokumentasi/installation`    | Installation Guide          |
| `/dokumentasi/validation-flow` | Validation Flow             |
| `/dokumentasi/rate-limiting`   | Rate Limiting               |
| `/dokumentasi/{slug}`          | Any documentation page      |

## 🎨 Features

### 1. Home Page

**Features:**

-   Hero section dengan CTA buttons
-   Feature overview cards
-   Grouped documentation by category
-   Status badges (Ready/Planned)
-   Quick links section
-   Stats section

### 2. Documentation Page

**Features:**

-   Sidebar navigation dengan categories
-   Active link highlighting
-   Beautiful header dengan gradient
-   Parsed markdown content
-   Previous/Next navigation
-   Feedback section
-   Responsive design

### 3. Markdown Parser

**Supported syntax:**

-   Headers (h1, h2, h3, h4)
-   Bold & Italic
-   Code blocks with syntax highlighting
-   Inline code
-   Links
-   Unordered lists
-   Ordered lists
-   Tables
-   Blockquotes
-   Horizontal rules

## 🔧 Customization

### Change Colors

Edit `resources/views/documentation/layout.blade.php`:

```html
<!-- Change primary color from blue to green -->
<div class="bg-green-600 hover:bg-green-700">...</div>
```

### Add New Documentation

Edit `app/Http/Controllers/DocumentationController.php`:

```php
$this->docs = [
    // Add new doc
    'my-new-doc' => [
        'title' => 'My New Feature',
        'file' => 'MY-NEW-DOC.md',
        'icon' => '🎨',
        'category' => 'Advanced Features'
    ],
];
```

Create file `MY-NEW-DOC.md` di root project.

### Customize Layout

**Logo:** Edit navigation logo di `layout.blade.php`

**Footer:** Edit footer text di `layout.blade.php`

**Meta tags:** Add SEO meta tags di `<head>`

**Custom styles:** Add custom CSS di `<style>` tag

## 📱 Responsive Design

Documentation website fully responsive:

-   **Desktop:** Sidebar + content side by side
-   **Tablet:** Stacked layout dengan collapsible sidebar
-   **Mobile:** Full-width content, touch-friendly navigation

## 🎯 SEO Ready

Add meta tags untuk SEO:

```html
<!-- In layout.blade.php -->
<meta name="description" content="Laravel API Starter Pack Documentation" />
<meta name="keywords" content="laravel, api, documentation, starter pack" />
<meta property="og:title" content="Laravel API Documentation" />
<meta
    property="og:description"
    content="Complete guide for Laravel API Starter Pack"
/>
```

## 🚀 Deployment

### Production Build

No build step needed! Tailwind CDN handles everything.

### Custom Domain

Update links di layout:

```html
<!-- Change all /dokumentasi to your domain -->
<a href="https://docs.yourdomain.com">...</a>
```

### Nginx Configuration

```nginx
location /dokumentasi {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 💡 Tips

### Tip 1: Search Functionality

Add search box (requires JavaScript):

```html
<input
    type="text"
    id="search"
    placeholder="Search docs..."
    class="w-full px-4 py-2 rounded-lg border"
/>

<script>
    document.getElementById("search").addEventListener("input", function (e) {
        const query = e.target.value.toLowerCase();
        // Filter documentation list
    });
</script>
```

### Tip 2: Dark Mode

Add dark mode toggle:

```html
<button onclick="document.documentElement.classList.toggle('dark')">
    Toggle Dark Mode
</button>

<style>
    .dark {
        background: #1a202c;
        color: #fff;
    }
</style>
```

### Tip 3: Analytics

Add Google Analytics:

```html
<!-- In layout.blade.php before </head> -->
<script
    async
    src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"
></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag("js", new Date());
    gtag("config", "GA_MEASUREMENT_ID");
</script>
```

## 🎨 Advanced Customization

### Code Syntax Highlighting

Use Prism.js or highlight.js:

```html
<!-- Add to layout.blade.php -->
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css"
/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
```

### Table of Contents

Auto-generate TOC from headers:

```javascript
<script>
const headings = document.querySelectorAll('h2, h3');
const toc = document.getElementById('toc');

headings.forEach(heading => {
    const link = document.createElement('a');
    link.href = '#' + heading.id;
    link.textContent = heading.textContent;
    toc.appendChild(link);
});
</script>
```

## 📊 Performance

-   **Tailwind CDN:** ~50KB gzipped
-   **No JavaScript frameworks:** Pure HTML
-   **Lazy loading:** Images load on demand
-   **Cache-friendly:** Static markdown files

## 🔒 Security

Documentation is public by default. To add authentication:

```php
Route::middleware('auth')->prefix('dokumentasi')->group(function () {
    Route::get('/', [DocumentationController::class, 'index']);
    Route::get('/{slug}', [DocumentationController::class, 'show']);
});
```

## 🎉 Done!

Your documentation website is now live and beautiful!

Visit: `http://localhost:8000/dokumentasi`

---

**Happy documenting! 📚**
