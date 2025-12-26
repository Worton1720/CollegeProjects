import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const routes = [
    {
        path: "/",
        name: "Home",
        component: () => import("@/views/HomeView.vue"),
    },
    {
        path: "/login",
        name: "Login",
        component: () => import("@/views/LoginView.vue"),
        meta: { guestOnly: true },
    },
    {
        path: "/register",
        name: "Register",
        component: () => import("@/views/RegisterView.vue"),
        meta: { guestOnly: true },
    },
    {
        path: "/video/:id",
        name: "VideoDetail",
        component: () => import("@/views/VideoDetailView.vue"),
    },
    {
        path: "/upload",
        name: "Upload",
        component: () => import("@/views/UploadView.vue"),
        meta: { requiresAuth: true, requiresUser: true },
    },
    {
        path: "/profile",
        name: "Profile",
        component: () => import("@/views/ProfileView.vue"),
        meta: { requiresAuth: true },
    },
    {
        path: "/admin",
        name: "Admin",
        component: () => import("@/views/AdminView.vue"),
        meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: () => import("@/views/NotFoundView.vue"),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    // Инициализация аутентификации при первой загрузке
    if (!authStore.user && authStore.token) {
        authStore.initializeAuth();
    }

    // Проверка требований маршрута
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: "Login", query: { redirect: to.fullPath } });
    }

    if (to.meta.requiresUser && !authStore.isUser) {
        return next({ name: "Home" });
    }

    if (to.meta.requiresAdmin && !authStore.isAdmin) {
        return next({ name: "Home" });
    }

    if (to.meta.guestOnly && authStore.isAuthenticated) {
        return next({ name: "Home" });
    }

    next();
});

export default router;
