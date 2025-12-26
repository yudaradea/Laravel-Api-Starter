<script setup>
import { ref, onMounted } from "vue";
import api from "../api";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();
const users = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref({});
const roles = ref([]);
const showEditModal = ref(false);
const editingUser = ref(null);
const editForm = ref({
    name: "",
    email: "",
    role: "",
});
const editLoading = ref(false);
const editError = ref("");
const editSuccess = ref("");

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

const fetchRoles = async () => {
    try {
        const response = await api.get("/roles");
        roles.value = response.data.data;
    } catch (err) {
        console.error("Failed to load roles", err);
    }
};

const openEditModal = (user) => {
    editingUser.value = user;
    editForm.value = {
        name: user.name,
        email: user.email,
        role: user.roles && user.roles.length > 0 ? user.roles[0].name : "",
    };
    editError.value = "";
    editSuccess.value = "";
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingUser.value = null;
    editForm.value = {
        name: "",
        email: "",
        role: "",
    };
    editError.value = "";
    editSuccess.value = "";
};

const updateUser = async () => {
    editLoading.value = true;
    editError.value = "";
    editSuccess.value = "";

    try {
        await api.put(`/user/${editingUser.value.id}`, editForm.value);
        editSuccess.value = "User updated successfully!";

        // Refresh user list
        await fetchUsers(pagination.value.current_page);

        // Close modal after 1 second
        setTimeout(() => {
            closeEditModal();
        }, 1000);
    } catch (err) {
        editError.value =
            err.response?.data?.message || "Failed to update user";
    } finally {
        editLoading.value = false;
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

const getRoleBadgeColor = (roleName) => {
    const colors = {
        "super-admin": "bg-purple-100 text-purple-800",
        admin: "bg-blue-100 text-blue-800",
        user: "bg-green-100 text-green-800",
    };
    return colors[roleName] || "bg-gray-100 text-gray-800";
};

onMounted(() => {
    fetchUsers();
    fetchRoles();
});
</script>

<template>
    <div>
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
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold"
                                        >
                                            {{
                                                user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
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
                                    :class="getRoleBadgeColor(role.name)"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mr-1"
                                >
                                    {{ role.name }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{
                                    new Date(
                                        user.created_at
                                    ).toLocaleDateString()
                                }}
                            </td>
                            <td
                                v-if="authStore.isAdmin"
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <button
                                    @click="openEditModal(user)"
                                    class="text-indigo-600 hover:text-indigo-900 mr-4"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteUser(user.id)"
                                    class="text-red-600 hover:text-red-900"
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
                        :disabled="
                            pagination.current_page === pagination.last_page
                        "
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
                                    pagination.current_page ===
                                    pagination.last_page
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

        <!-- Edit User Modal -->
        <div
            v-if="showEditModal"
            class="fixed z-10 inset-0 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            >
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    @click="closeEditModal"
                ></div>

                <!-- Center modal -->
                <span
                    class="hidden sm:inline-block sm:align-middle sm:h-screen"
                    aria-hidden="true"
                    >&#8203;</span
                >

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                >
                    <form @submit.prevent="updateUser">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mt-3 text-center sm:mt-0 sm:text-left w-full"
                                >
                                    <h3
                                        class="text-lg leading-6 font-medium text-gray-900 mb-4"
                                        id="modal-title"
                                    >
                                        Edit User
                                    </h3>

                                    <div
                                        v-if="editSuccess"
                                        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                                    >
                                        {{ editSuccess }}
                                    </div>
                                    <div
                                        v-if="editError"
                                        class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
                                    >
                                        {{ editError }}
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Name</label
                                            >
                                            <input
                                                v-model="editForm.name"
                                                type="text"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                            />
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Email</label
                                            >
                                            <input
                                                v-model="editForm.email"
                                                type="email"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                            />
                                        </div>

                                        <div v-if="authStore.isSuperAdmin">
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Role</label
                                            >
                                            <select
                                                v-model="editForm.role"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                            >
                                                <option value="">
                                                    Select a role
                                                </option>
                                                <option
                                                    v-for="role in roles"
                                                    :key="role.id"
                                                    :value="role.name"
                                                >
                                                    {{ role.name }} ({{
                                                        role.permissions_count
                                                    }}
                                                    permissions)
                                                </option>
                                            </select>
                                            <p
                                                class="mt-1 text-xs text-gray-500"
                                            >
                                                Only super-admin can assign
                                                roles
                                            </p>
                                        </div>
                                        <div v-else>
                                            <label
                                                class="block text-sm font-medium text-gray-700"
                                                >Role</label
                                            >
                                            <input
                                                :value="editForm.role"
                                                type="text"
                                                disabled
                                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm border p-2 cursor-not-allowed"
                                            />
                                            <p
                                                class="mt-1 text-xs text-gray-500"
                                            >
                                                You don't have permission to
                                                change roles
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                        >
                            <button
                                type="submit"
                                :disabled="editLoading"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ editLoading ? "Saving..." : "Save Changes" }}
                            </button>
                            <button
                                type="button"
                                @click="closeEditModal"
                                :disabled="editLoading"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
