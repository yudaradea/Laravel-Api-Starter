@extends('documentation.layout')

@section('title', $doc['title'])

@section('content')
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="sticky top-20">
                <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                    <a href="/dokumentasi" class="flex items-center text-blue-600 hover:text-blue-800 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Home
                    </a>
                    <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wide">Documentation</h3>
                    <nav class="space-y-1">
                        @foreach ($grouped as $category => $categoryDocs)
                            <div class="mb-4">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 px-3">
                                    {{ $category }}
                                </div>
                                @foreach ($categoryDocs as $docSlug => $docItem)
                                    <a href="/dokumentasi/{{ $docSlug }}"
                                        class="sidebar-link flex items-center px-3 py-2 text-sm rounded-lg transition {{ $docSlug === $slug ? 'active' : 'text-gray-700 hover:bg-gray-100' }}">
                                        <span class="mr-2">{{ $docItem['icon'] }}</span>
                                        <span class="truncate">{{ $docItem['title'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
            <div class="bg-white rounded-lg shadow-lg">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-8 rounded-t-lg">
                    <div class="flex items-center mb-2">
                        <span class="text-5xl mr-4">{{ $doc['icon'] }}</span>
                        <div>
                            <h1 class="text-3xl font-bold">{{ $doc['title'] }}</h1>
                            <p class="text-blue-100 text-sm">{{ $doc['category'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div class="prose prose-lg max-w-none">
                        {!! $content !!}
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="border-t border-gray-200 p-6 bg-gray-50 rounded-b-lg">
                    <div class="flex justify-between items-center">
                        @php
                            // Get all documentation slugs
                            $allDocs = array_keys($docs);
                            $currentIndex = array_search($slug, $allDocs);
                            $prevSlug = $currentIndex > 0 ? $allDocs[$currentIndex - 1] : null;
                            $nextSlug = $currentIndex < count($allDocs) - 1 ? $allDocs[$currentIndex + 1] : null;
                        @endphp

                        @if ($prevSlug)
                            <a href="/dokumentasi/{{ $prevSlug }}"
                                class="flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                <div class="text-left">
                                    <div class="text-xs text-gray-500">Previous</div>
                                    <div>{{ $docs[$prevSlug]['title'] }}</div>
                                </div>
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if ($nextSlug)
                            <a href="/dokumentasi/{{ $nextSlug }}"
                                class="flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">Next</div>
                                    <div>{{ $docs[$nextSlug]['title'] }}</div>
                                </div>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @else
                            <div></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
                <div class="flex items-start">
                    <div class="text-3xl mr-4">💡</div>
                    <div>
                        <h3 class="font-bold text-gray-900 mb-2">Was this page helpful?</h3>
                        <p class="text-sm text-gray-600 mb-4">Help us improve this documentation by providing feedback.</p>
                        <div class="flex space-x-3">
                            <button
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                👍 Yes
                            </button>
                            <button
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                👎 No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
