<script setup>
import { useAuthStore } from "../stores/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = async () => {
    await authStore.logout();
    router.push("/login");
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 py-6 px-4 hidden md:block">
            <div class="text-2xl font-bold mb-8">Admin Panel</div>
            <nav>
                <router-link
                    to="/dashboard"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700"
                >
                    Dashboard
                </router-link>
                <router-link
                    to="/dashboard/profile"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700"
                >
                    My Profile
                </router-link>

                <div
                    v-if="authStore.isAdmin"
                    class="mt-8 uppercase text-xs text-gray-400 font-semibold px-4 mb-2"
                >
                    Admin
                </div>
                <router-link
                    v-if="authStore.isAdmin"
                    to="/dashboard/users"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700"
                >
                    Users List
                </router-link>
                <router-link
                    v-if="authStore.isAdmin"
                    to="/dashboard/users/create"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white"
                    active-class="bg-gray-700"
                >
                    Create User
                </router-link>
            </nav>
        </div>

        <!-- Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <header
                class="flex justify-between items-center py-4 px-6 bg-white shadow-sm"
            >
                <div class="flex items-center">
                    <span class="text-gray-900 text-lg font-semibold"
                        >Welcome, {{ authStore.user?.name }}</span
                    >
                    <span
                        v-if="authStore.isAdmin"
                        class="ml-2 px-2 py-0.5 text-xs bg-indigo-100 text-indigo-800 rounded-full"
                        >Admin</span
                    >
                </div>
                <button
                    @click="handleLogout"
                    class="text-gray-600 hover:text-red-600 transition font-medium"
                >
                    Logout
                </button>
            </header>

            <!-- Main Content -->
            <main
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6"
            >
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>
