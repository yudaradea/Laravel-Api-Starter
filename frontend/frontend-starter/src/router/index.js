import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

import HomeView from "../views/HomeView.vue";
import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";
import DashboardView from "../views/DashboardView.vue";
import DashboardHome from "../views/DashboardHome.vue";
import ProfileView from "../views/ProfileView.vue";
import AdminUserCreateView from "../views/AdminUserCreateView.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "home",
            component: HomeView,
        },
        {
            path: "/login",
            name: "login",
            component: LoginView,
            meta: { guest: true },
        },
        {
            path: "/register",
            name: "register",
            component: RegisterView,
            meta: { guest: true },
        },
        {
            path: "/dashboard",
            component: DashboardView,
            meta: { requiresAuth: true },
            children: [
                {
                    path: "",
                    name: "dashboard",
                    component: DashboardHome,
                },
                {
                    path: "profile",
                    name: "profile",
                    component: ProfileView,
                },
                {
                    path: "users",
                    name: "user-list",
                    component: () => import("../views/UserListView.vue"),
                    meta: { requiresAdmin: true },
                },
                {
                    path: "users/create",
                    name: "create-user",
                    component: AdminUserCreateView,
                    meta: { requiresAdmin: true },
                },
                {
                    path: "roles",
                    name: "role-list",
                    component: () => import("../views/RoleManagerView.vue"),
                    meta: { requiresSuperAdmin: true },
                },
            ],
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Wait for auth check initialization if needed (basic check based on token presence)
    const isAuthenticated = authStore.isAuthenticated;

    // If requires auth and not authenticated
    if (to.meta.requiresAuth && !isAuthenticated) {
        return next("/login");
    }

    // If requires guest (login implementation) and is authenticated
    if (to.meta.guest && isAuthenticated) {
        return next("/dashboard");
    }

    // Admin Check
    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        return next("/dashboard"); // Unauthorized redirect
    }

    // Super Admin Check
    if (to.meta.requiresSuperAdmin && !authStore.isSuperAdmin) {
        return next("/dashboard"); // Unauthorized redirect
    }

    next();
});

export default router;
