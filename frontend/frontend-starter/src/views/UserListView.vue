<script setup>
import { ref, onMounted } from "vue";
import api from "../api";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();
const users = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref({});

const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        // Use the paginated endpoint
        const response = await api.get(
            `/user/all/paginated?page=${page}&per_page=10`
        );
        users.value = response.data.data.data;
        pagination.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            total: response.data.data.total,
        };
    } catch (err) {
        error.value = "Failed to load users";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const deleteUser = async (id) => {
    if (!confirm("Are you sure you want to delete this user?")) return;

    try {
        await api.delete(`/user/${id}`);
        fetchUsers(pagination.value.current_page); // Refresh list
    } catch (err) {
        alert("Failed to delete user");
    }
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div
            class="p-6 border-b border-gray-200 flex justify-between items-center"
        >
            <h2 class="text-xl font-bold text-gray-800">Users List</h2>
            <span class="text-sm text-gray-500"
                >Total: {{ pagination.total || 0 }}</span
            >
        </div>

        <div v-if="loading" class="p-6 text-center text-gray-500">
            Loading users...
        </div>

        <div v-else-if="error" class="p-6 text-center text-red-500">
            {{ error }}
        </div>

        <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Name
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Email
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Role
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Joined
                        </th>
                        <th
                            v-if="authStore.isAdmin"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="hover:bg-gray-50 transition"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold"
                                    >
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ user.name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ user.email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                v-for="role in user.roles"
                                :key="role.name"
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 mr-1"
                            >
                                {{ role.name }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                        >
                            {{ new Date(user.created_at).toLocaleDateString() }}
                        </td>
                        <td
                            v-if="authStore.isAdmin"
                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                        >
                            <button
                                @click="deleteUser(user.id)"
                                class="text-red-600 hover:text-red-900 ml-4"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div
            v-if="pagination.last_page > 1"
            class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
        >
            <div class="flex-1 flex justify-between sm:hidden">
                <button
                    @click="fetchUsers(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    Previous
                </button>
                <button
                    @click="fetchUsers(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                    Next
                </button>
            </div>
            <div
                class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
            >
                <div>
                    <p class="text-sm text-gray-700">
                        Page
                        <span class="font-medium">{{
                            pagination.current_page
                        }}</span>
                        of
                        <span class="font-medium">{{
                            pagination.last_page
                        }}</span>
                    </p>
                </div>
                <div>
                    <nav
                        class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                        aria-label="Pagination"
                    >
                        <button
                            @click="fetchUsers(pagination.current_page - 1)"
                            :disabled="pagination.current_page === 1"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                        >
                            <span class="sr-only">Previous</span>
                            &larr;
                        </button>
                        <button
                            @click="fetchUsers(pagination.current_page + 1)"
                            :disabled="
                                pagination.current_page === pagination.last_page
                            "
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                        >
                            <span class="sr-only">Next</span>
                            &rarr;
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
