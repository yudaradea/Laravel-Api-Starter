<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";
import { notify } from "@kyvg/vue3-notification";

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
    email: "",
    password: "",
});

const handleLogin = async () => {
    const success = await authStore.login(form.value);
    if (success) {
        notify({
            title: "Success",
            text: "Logged in successfully",
            type: "success",
        });
        router.push("/dashboard");
    } else {
        notify({
            title: "Error",
            text: authStore.error || "Login failed",
            type: "error",
        });
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
        <!-- Simple Header -->
        <div class="absolute top-0 left-0 w-full p-6">
            <router-link
                to="/"
                class="flex items-center text-gray-600 hover:text-blue-600 transition font-medium"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                    />
                </svg>
                Back to Home
            </router-link>
        </div>

        <div
            class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans"
        >
            <div
                class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden"
            >
                <!-- Decorative gradient top -->
                <div
                    class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-600"
                ></div>

                <div class="text-center">
                    <h2
                        class="text-3xl font-extrabold text-gray-900 tracking-tight"
                    >
                        Welcome Back!
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Sign in to manage your account
                    </p>
                </div>

                <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                    <div class="space-y-4">
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Email Address</label
                            >
                            <input
                                v-model="form.email"
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="you@example.com"
                            />
                        </div>
                        <div>
                            <label
                                for="password"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Password</label
                            >
                            <input
                                v-model="form.password"
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="••••••••"
                            />
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="authStore.loading"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-blue-500/30"
                        >
                            <span
                                v-if="authStore.loading"
                                class="absolute left-0 inset-y-0 flex items-center pl-3"
                            >
                                <svg
                                    class="animate-spin h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                            </span>
                            {{
                                authStore.loading ? "Signing in..." : "Sign In"
                            }}
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <router-link
                                to="/register"
                                class="font-medium text-blue-600 hover:text-blue-500 transition-colors"
                            >
                                Sign up now
                            </router-link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
