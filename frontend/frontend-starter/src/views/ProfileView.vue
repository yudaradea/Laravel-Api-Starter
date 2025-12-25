<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();

const form = ref({
    name: "",
    email: "",
    phone: "",
    address: "",
    bio: "",
    avatar: null,
    password: "", // Optional for updates
    current_password: "", // Required for password change usually, but keeping simple
});

const fileInput = ref(null);
const previewUrl = ref(null);
const message = ref("");
const error = ref("");

onMounted(() => {
    if (authStore.user) {
        form.value.name = authStore.user.name;
        form.value.email = authStore.user.email;
        if (authStore.user.profile) {
            form.value.phone = authStore.user.profile.phone || "";
            form.value.address = authStore.user.profile.address || "";
            form.value.bio = authStore.user.profile.bio || "";
        }
    }
});

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.avatar = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const updateProfile = async () => {
    message.value = "";
    error.value = "";

    // Using FormData for file upload
    const formData = new FormData();
    formData.append("name", form.value.name);
    formData.append("email", form.value.email);
    formData.append("phone", form.value.phone);
    formData.append("address", form.value.address);
    formData.append("bio", form.value.bio);

    if (form.value.avatar) {
        formData.append("avatar", form.value.avatar);
    }

    // In a real app we might handle password separately or require current password
    // Skipping password update logic here to keep it simple as requested "like database"

    // Method spoofing for Laravel if using PUT with FormData (Laravel quirk) or just POST
    formData.append("_method", "POST");

    const result = await authStore.updateProfile(formData);

    if (result.success) {
        message.value = "Profile updated successfully!";
    } else {
        error.value = result.message;
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Profile Settings</h2>
        </div>

        <div class="p-6">
            <div
                v-if="message"
                class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
            >
                {{ message }}
            </div>
            <div
                v-if="error"
                class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
            >
                {{ error }}
            </div>

            <form @submit.prevent="updateProfile" class="space-y-6">
                <!-- Avatar Section -->
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <img
                            v-if="previewUrl"
                            :src="previewUrl"
                            class="h-16 w-16 object-cover rounded-full"
                        />
                        <img
                            v-else-if="authStore.user?.profile?.avatar_url"
                            :src="authStore.user.profile.avatar_url"
                            class="h-16 w-16 object-cover rounded-full"
                        />
                        <div
                            v-else
                            class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500"
                        >
                            <span class="text-2xl">?</span>
                        </div>
                    </div>
                    <label class="block">
                        <span class="sr-only">Choose profile photo</span>
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept="image/*"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"
                        />
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Name</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Email</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Phone</label
                        >
                        <input
                            v-model="form.phone"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Address</label
                        >
                        <input
                            v-model="form.address"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700"
                            >Bio</label
                        >
                        <textarea
                            v-model="form.bio"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2"
                        ></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="authStore.loading"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        {{ authStore.loading ? "Saving..." : "Save Changes" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
