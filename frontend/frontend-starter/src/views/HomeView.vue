<script setup></script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
        <!-- Header/Navigation -->
        <nav
            class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span
                            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent"
                            >API Starter</span
                        >
                    </div>
                    <div class="flex space-x-4">
                        <router-link
                            to="/login"
                            class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            >Login</router-link
                        >
                        <router-link
                            to="/register"
                            class="bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-blue-700 transition shadow-lg shadow-blue-600/30"
                            >Get Started</router-link
                        >
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div
            class="flex-grow flex items-center justify-center relative overflow-hidden"
        >
            <!-- Background Elements -->
            <div
                class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10"
            >
                <div
                    class="absolute -top-[30%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-100 blur-3xl opacity-50 animate-blob"
                ></div>
                <div
                    class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-indigo-100 blur-3xl opacity-50 animate-blob animation-delay-2000"
                ></div>
            </div>

            <div class="text-center max-w-4xl px-4 sm:px-6 lg:px-8 z-10">
                <h1
                    class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight mb-6"
                >
                    Build Faster with <br />
                    <span
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent"
                        >Modern Stack</span
                    >
                </h1>
                <p
                    class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed"
                >
                    Production-ready Laravel API starter pack featuring Vue 3,
                    TailwindCSS, and a robust backend architecture.
                </p>

                <div class="mt-10 flex justify-center gap-4">
                    <router-link
                        to="/register"
                        class="px-8 py-4 bg-blue-600 text-white rounded-full font-bold text-lg hover:bg-blue-700 transition transform hover:-translate-y-1 shadow-xl shadow-blue-600/30"
                    >
                        Start Building
                    </router-link>
                    <a
                        href="/dokumentasi"
                        class="px-8 py-4 bg-white text-gray-700 border border-gray-200 rounded-full font-bold text-lg hover:bg-gray-50 transition transform hover:-translate-y-1 shadow-sm"
                    >
                        Documentation
                    </a>
                </div>

                <div
                    class="mt-16 grid grid-cols-2 gap-8 md:grid-cols-4 grayscale hover:grayscale-0 transition-all duration-500 opacity-70"
                >
                    <!-- Icons or logos could go here -->
                    <div class="flex flex-col items-center">
                        <span class="text-2xl mb-2">⚡</span>
                        <span class="font-semibold">Fast</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl mb-2">🛡️</span>
                        <span class="font-semibold">Secure</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl mb-2">🧩</span>
                        <span class="font-semibold">Modular</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-2xl mb-2">🎨</span>
                        <span class="font-semibold">Modern</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}
</style>
