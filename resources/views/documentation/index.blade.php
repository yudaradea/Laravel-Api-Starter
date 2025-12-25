@extends('documentation.layout')

@section('title', 'Documentation Home')

@section('content')
    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-5xl font-extrabold text-gray-900 mb-4">
            🚀 Laravel API Starter Pack
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            Complete documentation for your production-ready API starter pack
        </p>
        <div class="flex justify-center space-x-4">
            <a href="/dokumentasi/complete-guide"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition">
                🎓 Complete Guide - Start Here!
            </a>
            <a href="https://github.com" target="_blank"
                class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition">
                View on GitHub
            </a>
        </div>
    </div>

    <!-- Features Overview -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">✨ Key Features</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center p-6 bg-blue-50 rounded-lg">
                <div class="text-4xl mb-3">🔐</div>
                <h3 class="text-lg font-bold mb-2">Security First</h3>
                <p class="text-sm text-gray-600">Sanctum auth, role-based permissions, rate limiting, CORS</p>
            </div>
            <div class="text-center p-6 bg-green-50 rounded-lg">
                <div class="text-4xl mb-3">📦</div>
                <h3 class="text-lg font-bold mb-2">Clean Architecture</h3>
                <p class="text-sm text-gray-600">Repository pattern, UUID, soft deletes, API versioning</p>
            </div>
            <div class="text-center p-6 bg-purple-50 rounded-lg">
                <div class="text-4xl mb-3">⚡</div>
                <h3 class="text-lg font-bold mb-2">Production Ready</h3>
                <p class="text-sm text-gray-600">Rate limiting, activity logs, validation, error handling</p>
            </div>
        </div>
    </div>

    <!-- Documentation Categories -->
    @foreach ($grouped as $category => $categoryDocs)
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center mr-3 text-lg">
                    {{ $loop->iteration }}
                </span>
                {{ $category }}
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($categoryDocs as $docSlug => $doc)
                    <a href="/dokumentasi/{{ $docSlug }}"
                        class="block bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="text-4xl">{{ $doc['icon'] }}</div>
                                @if (str_contains($doc['file'], 'ACTIVITY-LOG') ||
                                        str_contains($doc['file'], 'FILE-UPLOAD') ||
                                        str_contains($doc['file'], 'EMAIL-VERIFICATION') ||
                                        str_contains($doc['file'], 'PASSWORD-RESET') ||
                                        str_contains($doc['file'], 'REFRESH-TOKEN'))
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-semibold">Planned</span>
                                @else
                                    <span
                                        class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">Ready</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $doc['title'] }}</h3>
                            <p class="text-sm text-gray-600">Click to read documentation →</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Quick Links -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg p-8 text-white">
        <h2 class="text-3xl font-bold mb-6 text-center">🎯 Quick Start</h2>
        <div class="grid md:grid-cols-4 gap-4 text-center">
            <a href="/dokumentasi/complete-guide"
                class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition">
                <div class="text-3xl mb-2">🎓</div>
                <div class="font-semibold">Complete Guide</div>
            </a>
            <a href="/dokumentasi/installation"
                class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition">
                <div class="text-3xl mb-2">📥</div>
                <div class="font-semibold">Installation</div>
            </a>
            <a href="/dokumentasi/validation-flow"
                class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition">
                <div class="text-3xl mb-2">🔍</div>
                <div class="font-semibold">How it Works</div>
            </a>
            <a href="/dokumentasi/troubleshooting"
                class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 transition">
                <div class="text-3xl mb-2">🔧</div>
                <div class="font-semibold">Troubleshooting</div>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="mt-12 bg-white rounded-xl shadow-lg p-8">
        <div class="grid md:grid-cols-4 gap-6 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-600">15</div>
                <div class="text-sm text-gray-600 mt-1">Documentation Pages</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-green-600">7</div>
                <div class="text-sm text-gray-600 mt-1">Advanced Features</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-purple-600">8</div>
                <div class="text-sm text-gray-600 mt-1">Rate Limiters</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-orange-600">100%</div>
                <div class="text-sm text-gray-600 mt-1">Production Ready</div>
            </div>
        </div>
    </div>
@endsection
