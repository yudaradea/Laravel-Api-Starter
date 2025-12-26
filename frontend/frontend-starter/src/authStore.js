import { onMounted } from "vue";
import { useAuthStore } from "./stores/auth";

const authStore = useAuthStore();
onMounted(() => {
    if (authStore.token) {
        authStore.fetchUser();
    }
});
