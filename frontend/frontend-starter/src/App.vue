<script setup>
import { RouterView } from "vue-router";
import { onMounted } from "vue";
import { useAuthStore } from "./stores/auth";

const authStore = useAuthStore();

onMounted(() => {
    if (authStore.token) {
        authStore.fetchUser();
    }
});
</script>

<template>
    <notifications position="top right" />
    <RouterView />
</template>

<style>
/* Global styles can go here if needed */
</style>
