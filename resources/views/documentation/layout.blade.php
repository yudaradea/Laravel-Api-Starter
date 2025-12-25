<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Documentation') - Laravel API Starter Pack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Highlight.js for syntax highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Syntax highlighting tweaks */
        pre {
            background: #1e1e1e;
            /* VS Code Dark */
            color: #e5e7eb;
            /* Light gray text */
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            /* Increased margin bottom */
            margin-top: 1rem;
            overflow-x: auto;
            border: 1px solid #374151;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }

        pre code {
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 0.95rem;
            /* Slightly larger */
            line-height: 1.7;
            color: inherit;
            /* Inherit light gray */
        }

        /* Override highlight.js background to use parent pre background */
        .hljs {
            background: transparent !important;
            padding: 0 !important;
            color: inherit !important;
        }

        /* Sidebar active link */
        .sidebar-link.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Typography enhancements */
        .prose h1,
        .prose h2,
        .prose h3 {
            color: #111827;
        }

        .prose p {
            color: #374151;
        }

        .prose a {
            color: #2563eb;
            text-decoration: none;
        }

        .prose a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dokumentasi" class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Laravel API Starter</h1>
                            <p class="text-xs text-gray-500">Documentation</p>
                        </div>
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="https://github.com" target="_blank" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center text-gray-600">
                <p class="text-sm">Laravel API Starter Pack &copy; 2025</p>
                <p class="text-xs mt-2">Built with ❤️ using Laravel 12 & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <!-- Highlight.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/json.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            hljs.highlightAll();
        });
    </script>
</body>

</html>
