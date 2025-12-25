<script setup>
import { ref } from "vue";
import api from "../api";
import { useRouter } from "vue-router";

const router = useRouter();
const form = ref({
    name: "",
    email: "",
    password: "",
    role: "user", // Default role
});
const loading = ref(false);
const error = ref(null);

const createUser = async () => {
    loading.value = true;
    error.value = null;
    try {
        await api.post("/user", form.value); // Assuming /users endpoint exists for admin
        router.push("/dashboard"); // Or back to user list
    } catch (err) {
        error.value = err.response?.data?.message || "Failed to create user";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Create New User</h2>

        <div v-if="error" class="mb-4 bg-red-100 text-red-700 p-3 rounded">
            {{ error }}
        </div>

        <form @submit.prevent="createUser" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Name</label
                >
                <input
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Email</label
                >
                <input
                    v-model="form.email"
                    type="email"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Password</label
                >
                <input
                    v-model="form.password"
                    type="password"
                    required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700"
                    >Role</label
                >
                <select
                    v-model="form.role"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                >
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="pt-4">
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
                >
                    {{ loading ? "Creating..." : "Create User" }}
                </button>
            </div>
        </form>
    </div>
</template>
