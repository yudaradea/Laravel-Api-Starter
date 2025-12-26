<script setup>
import { ref, onMounted } from "vue";
import api from "../api";

const roles = ref([]);
const permissions = ref([]);
const loading = ref(false);
const error = ref(null);

const showRoleModal = ref(false);
const modalMode = ref("create"); // 'create' or 'edit'
const roleForm = ref({
    id: null,
    name: "",
    selectedPermissions: [],
});
const saving = ref(false);
const saveError = ref("");
const saveSuccess = ref("");

const fetchRoles = async () => {
    loading.value = true;
    try {
        const response = await api.get("/roles");
        roles.value = response.data.data;
    } catch (err) {
        error.value = "Failed to load roles";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const fetchPermissions = async () => {
    try {
        const response = await api.get("/permissions");
        permissions.value = response.data.data;
    } catch (err) {
        console.error("Failed to load permissions", err);
    }
};

onMounted(() => {
    fetchRoles();
    fetchPermissions();
});

const openCreateModal = () => {
    modalMode.value = "create";
    roleForm.value = {
        id: null,
        name: "",
        selectedPermissions: [],
    };
    saveError.value = "";
    saveSuccess.value = "";
    showRoleModal.value = true;
};

const openEditModal = (role) => {
    modalMode.value = "edit";
    roleForm.value = {
        id: role.id,
        name: role.name,
        selectedPermissions: role.permissions || [], // array of permission names
    };
    saveError.value = "";
    saveSuccess.value = "";
    showRoleModal.value = true;
};

const closeModal = () => {
    showRoleModal.value = false;
    saveError.value = "";
    saveSuccess.value = "";
};

const submitRole = async () => {
    saving.value = true;
    saveError.value = "";
    saveSuccess.value = "";

    try {
        const payload = {
            name: roleForm.value.name,
            permissions: roleForm.value.selectedPermissions,
        };

        if (modalMode.value === "create") {
            await api.post("/roles", payload);
            saveSuccess.value = "Role created successfully!";
        } else {
            await api.put(`/roles/${roleForm.value.id}`, payload);
            saveSuccess.value = "Role updated successfully!";
        }

        // Refresh roles
        await fetchRoles();

        setTimeout(() => {
            closeModal();
        }, 1500);
    } catch (err) {
        saveError.value = err.response?.data?.message || "Failed to save role";
    } finally {
        saving.value = false;
    }
};

const deleteRole = async (role) => {
    if (
        !confirm(
            `Are you sure you want to delete role "${role.name}"? This cannot be undone.`
        )
    )
        return;

    try {
        await api.delete(`/roles/${role.id}`);
        fetchRoles();
    } catch (err) {
        alert(err.response?.data?.message || "Failed to delete role");
    }
};

// Colors for badges
const getRoleBadgeColor = (roleName) => {
    const colors = {
        "super-admin": "bg-purple-100 text-purple-800",
        admin: "bg-blue-100 text-blue-800",
        user: "bg-green-100 text-green-800",
    };
    return colors[roleName] || "bg-gray-100 text-gray-800";
};

// --- Create Permission Logic ---
const showPermissionModal = ref(false);
const permissionForm = ref({ name: "" });
const permSaving = ref(false);
const permSuccess = ref("");
const permError = ref("");

const openPermissionModal = () => {
    permissionForm.value.name = "";
    permSuccess.value = "";
    permError.value = "";
    showPermissionModal.value = true;
};

const closePermissionModal = () => {
    showPermissionModal.value = false;
};

const submitPermission = async () => {
    permSaving.value = true;
    permSuccess.value = "";
    permError.value = "";

    try {
        await api.post("/permissions", { name: permissionForm.value.name });
        permSuccess.value = "Permission created successfully!";
        await fetchPermissions();

        // Auto close after success
        setTimeout(() => {
            closePermissionModal();
        }, 1500);
    } catch (err) {
        permError.value =
            err.response?.data?.message || "Failed to create permission";
    } finally {
        permSaving.value = false;
    }
};
</script>

<template>
    <div>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div
                class="p-6 border-b border-gray-200 flex justify-between items-center"
            >
                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        Role & Permission Management
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Create roles and assign permissions
                    </p>
                </div>
                <div class="flex space-x-2">
                    <button
                        @click="openPermissionModal"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                    >
                        + Add Permission
                    </button>
                    <button
                        @click="openCreateModal"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                    >
                        + Create New Role
                    </button>
                </div>
            </div>

            <div v-if="loading" class="p-6 text-center text-gray-500">
                Loading roles...
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
                                Role Name
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Permissions Count
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Assigned Permissions
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr
                            v-for="role in roles"
                            :key="role.id"
                            class="hover:bg-gray-50 transition"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    :class="getRoleBadgeColor(role.name)"
                                    class="px-2 inline-flex text-xs leading-5 font-bold rounded-full uppercase"
                                >
                                    {{ role.name }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                            >
                                {{ role.permissions_count }} permissions
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <div class="flex flex-wrap gap-1 max-w-md">
                                    <span
                                        v-for="perm in role.permissions.slice(
                                            0,
                                            5
                                        )"
                                        :key="perm"
                                        class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs"
                                    >
                                        {{ perm }}
                                    </span>
                                    <span
                                        v-if="role.permissions.length > 5"
                                        class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded text-xs"
                                    >
                                        +{{ role.permissions.length - 5 }}
                                        more
                                    </span>
                                    <span
                                        v-if="role.permissions.length === 0"
                                        class="italic text-gray-400 text-xs"
                                    >
                                        No specific permissions
                                    </span>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <button
                                    @click="openEditModal(role)"
                                    class="text-indigo-600 hover:text-indigo-900 mr-4 font-semibold"
                                    :class="{
                                        'opacity-50 cursor-not-allowed':
                                            role.name === 'super-admin',
                                    }"
                                    :disabled="role.name === 'super-admin'"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteRole(role)"
                                    class="text-red-600 hover:text-red-900 font-semibold"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': [
                                            'super-admin',
                                            'admin',
                                            'user',
                                        ].includes(role.name),
                                    }"
                                    :disabled="
                                        [
                                            'super-admin',
                                            'admin',
                                            'user',
                                        ].includes(role.name)
                                    "
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Role Modal -->
        <div
            v-if="showRoleModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    @click="closeModal"
                ></div>

                <span
                    class="hidden sm:inline-block sm:align-middle sm:h-screen"
                    aria-hidden="true"
                    >&#8203;</span
                >

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full"
                >
                    <form @submit.prevent="submitRole">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mt-3 text-center sm:mt-0 sm:text-left w-full"
                                >
                                    <h3
                                        class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2"
                                        id="modal-title"
                                    >
                                        {{
                                            modalMode === "create"
                                                ? "Create New Role"
                                                : "Edit Role & Permissions"
                                        }}
                                    </h3>

                                    <div
                                        v-if="saveSuccess"
                                        class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                                    >
                                        {{ saveSuccess }}
                                    </div>
                                    <div
                                        v-if="saveError"
                                        class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
                                    >
                                        {{ saveError }}
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1"
                                                >Role Name</label
                                            >
                                            <input
                                                v-model="roleForm.name"
                                                type="text"
                                                required
                                                placeholder="e.g. editor, moderator"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                            />
                                            <p
                                                class="text-xs text-gray-500 mt-1"
                                            >
                                                The role name should be unique
                                                and lowercase (kebab-case
                                                recommended).
                                            </p>
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                                >Permissions</label
                                            >
                                            <div
                                                class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto border rounded p-3 bg-gray-50"
                                            >
                                                <div
                                                    v-for="perm in permissions"
                                                    :key="perm.id"
                                                    class="flex items-center space-x-2"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        :id="perm.name"
                                                        :value="perm.name"
                                                        v-model="
                                                            roleForm.selectedPermissions
                                                        "
                                                        class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                                                    />
                                                    <label
                                                        :for="perm.name"
                                                        class="text-sm text-gray-700 cursor-pointer select-none"
                                                    >
                                                        {{ perm.name }}
                                                    </label>
                                                </div>
                                            </div>
                                            <p
                                                class="text-xs text-gray-500 mt-2 text-right"
                                            >
                                                <span class="font-bold">{{
                                                    roleForm.selectedPermissions
                                                        .length
                                                }}</span>
                                                permissions selected
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
                                :disabled="saving"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{ saving ? "Saving..." : "Save Role" }}
                            </button>
                            <button
                                type="button"
                                @click="closeModal"
                                :disabled="saving"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Permission Modal -->
        <div
            v-if="showPermissionModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    @click="closePermissionModal"
                ></div>
                <span
                    class="hidden sm:inline-block sm:align-middle sm:h-screen"
                    aria-hidden="true"
                    >&#8203;</span
                >
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                >
                    <form @submit.prevent="submitPermission">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3
                                class="text-lg leading-6 font-medium text-gray-900 mb-4 border-b pb-2"
                            >
                                Add New Permission
                            </h3>

                            <div
                                v-if="permSuccess"
                                class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                            >
                                {{ permSuccess }}
                            </div>
                            <div
                                v-if="permError"
                                class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
                            >
                                {{ permError }}
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-1"
                                    >Permission Name</label
                                >
                                <input
                                    v-model="permissionForm.name"
                                    type="text"
                                    required
                                    placeholder="e.g. view products, edit products"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Recommended format: action resource (e.g.
                                    create posts)
                                </p>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse"
                        >
                            <button
                                type="submit"
                                :disabled="permSaving"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-800 text-base font-medium text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{
                                    permSaving ? "Adding..." : "Add Permission"
                                }}
                            </button>
                            <button
                                type="button"
                                @click="closePermissionModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
