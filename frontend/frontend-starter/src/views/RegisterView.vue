<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";
import { notify } from "@kyvg/vue3-notification";

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const handleRegister = async () => {
    const success = await authStore.register(form.value);
    if (success) {
        notify({
            title: "Account Created",
            text: "Welcome to the platform!",
            type: "success",
        });
        router.push("/dashboard");
    } else {
        notify({
            title: "Registration Failed",
            text: authStore.error || "Could not create account",
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
            class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
        >
            <div
                class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100 relative overflow-hidden"
            >
                <div
                    class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-indigo-600"
                ></div>

                <div class="text-center">
                    <h2
                        class="text-3xl font-extrabold text-gray-900 tracking-tight"
                    >
                        Create Account
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Join us and start building today
                    </p>
                </div>

                <form class="mt-8 space-y-5" @submit.prevent="handleRegister">
                    <div class="space-y-4">
                        <div>
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Full Name</label
                            >
                            <input
                                v-model="form.name"
                                id="name"
                                name="name"
                                type="text"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="John Doe"
                            />
                        </div>
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
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
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
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="••••••••"
                            />
                        </div>
                        <div>
                            <label
                                for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-1"
                                >Confirm Password</label
                            >
                            <input
                                v-model="form.password_confirmation"
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="••••••••"
                            />
                        </div>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="authStore.loading"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition shadow-lg shadow-blue-500/30"
                        >
                            {{
                                authStore.loading
                                    ? "Creating account..."
                                    : "Create Account"
                            }}
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <router-link
                                to="/login"
                                class="font-medium text-blue-600 hover:text-blue-500 transition"
                            >
                                Log in
                            </router-link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
