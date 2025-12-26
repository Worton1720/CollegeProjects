<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const router = useRouter();
const authStore = useAuthStore();
const mobileMenuOpen = ref(false);
const showUserMenu = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
    if (mobileMenuOpen.value) {
        showUserMenu.value = false;
    }
};

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
    showUserMenu.value = false;
};

const handleLogout = () => {
    authStore.logout();
    closeMobileMenu();
    router.push("/");
};

// Close menus when clicking outside
const handleClickOutside = () => {
    showUserMenu.value = false;
};
</script>

<template>
    <nav class="navbar">
        <div class="navbar-inner">
            <!-- Left Section -->
            <div class="navbar-left">
                <button class="mobile-menu-btn" @click="toggleMobileMenu">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <router-link to="/" class="logo" @click="closeMobileMenu">
                    <svg width="32" height="32" viewBox="0 0 32 32">
                        <rect width="32" height="32" rx="6" fill="#ff0000" />
                        <path d="M12 10L22 16L12 22V10Z" fill="white" />
                    </svg>
                    <span class="logo-text">VideoHub</span>
                </router-link>
            </div>

            <!-- Center Section -->
            <div class="navbar-center">
                <div class="navbar-menu">
                    <router-link to="/" class="nav-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <span>Главная</span>
                    </router-link>

                    <router-link 
                        v-if="authStore.isUser" 
                        to="/upload" 
                        class="nav-link"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span>Загрузить</span>
                    </router-link>

                    <router-link 
                        v-if="authStore.isAdmin" 
                        to="/admin" 
                        class="nav-link admin-link"
                    >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Админ</span>
                    </router-link>
                </div>
            </div>

            <!-- Right Section -->
            <div class="navbar-right">
                <template v-if="authStore.isAuthenticated">
                    <!-- User Menu Button -->
                    <div class="user-menu-container" v-click-outside="handleClickOutside">
                        <button @click="toggleUserMenu" class="user-menu-btn">
                            <div class="user-avatar">
                                {{ authStore?.user?.email?.[0]?.toUpperCase() ?? "U" }}
                            </div>
                        </button>

                        <!-- User Dropdown -->
                        <Transition name="dropdown">
                            <div v-if="showUserMenu" class="user-dropdown">
                                <div class="dropdown-header" v-if="authStore.user">
                                    <div class="user-avatar-large">
                                        {{ authStore.user.email?.[0]?.toUpperCase() ?? "U" }}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">{{ authStore.user.email?.split("@")[0] }}</div>
                                        <div class="user-email">{{ authStore.user.email }}</div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <router-link to="/profile" class="dropdown-item" @click="showUserMenu = false">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span>Мой профиль</span>
                                </router-link>

                                <router-link 
                                    v-if="authStore.isAdmin" 
                                    to="/admin" 
                                    class="dropdown-item"
                                    @click="showUserMenu = false"
                                >
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                    <span>Панель админа</span>
                                </router-link>

                                <div class="dropdown-divider"></div>

                                <button @click="handleLogout" class="dropdown-item logout-item">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    <span>Выйти</span>
                                </button>
                            </div>
                        </Transition>
                    </div>
                </template>

                <template v-else>
                    <router-link to="/login" class="btn-login">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span>Войти</span>
                    </router-link>
                </template>
            </div>
        </div>

        <!-- Mobile Sidebar Menu -->
        <Transition name="sidebar">
            <div v-if="mobileMenuOpen" class="mobile-sidebar-overlay" @click="closeMobileMenu">
                <div class="mobile-sidebar" @click.stop>
                    <div class="mobile-sidebar-header">
                        <router-link to="/" class="mobile-logo" @click="closeMobileMenu">
                            <svg width="28" height="28" viewBox="0 0 32 32">
                                <rect width="32" height="32" rx="6" fill="#ff0000" />
                                <path d="M12 10L22 16L12 22V10Z" fill="white" />
                            </svg>
                            <span>VideoHub</span>
                        </router-link>
                        <button @click="closeMobileMenu" class="close-sidebar-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mobile-sidebar-content">
                        <!-- User Info (if authenticated) -->
                        <div v-if="authStore.isAuthenticated && authStore.user" class="mobile-user-card">
                            <div class="mobile-user-avatar">
                                {{ authStore.user.email?.[0]?.toUpperCase() ?? "U" }}
                            </div>
                            <div class="mobile-user-info">
                                <div class="mobile-user-name">{{ authStore.user.email?.split("@")[0] }}</div>
                                <div class="mobile-user-email">{{ authStore.user.email }}</div>
                            </div>
                        </div>

                        <div class="mobile-nav-section">
                            <router-link to="/" class="mobile-nav-item" @click="closeMobileMenu">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <span>Главная</span>
                            </router-link>

                            <template v-if="authStore.isAuthenticated">
                                <router-link 
                                    v-if="authStore.isUser"
                                    to="/upload" 
                                    class="mobile-nav-item" 
                                    @click="closeMobileMenu"
                                >
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <span>Загрузить видео</span>
                                </router-link>

                                <router-link to="/profile" class="mobile-nav-item" @click="closeMobileMenu">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                    <span>Мой профиль</span>
                                </router-link>

                                <router-link 
                                    v-if="authStore.isAdmin"
                                    to="/admin" 
                                    class="mobile-nav-item admin-item" 
                                    @click="closeMobileMenu"
                                >
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                    <span>Панель админа</span>
                                </router-link>

                                <div class="mobile-nav-divider"></div>

                                <button @click="handleLogout" class="mobile-nav-item logout-item">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                        <polyline points="16 17 21 12 16 7"/>
                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
                                    <span>Выйти</span>
                                </button>
                            </template>

                            <template v-else>
                                <router-link to="/login" class="mobile-nav-item" @click="closeMobileMenu">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                        <polyline points="10 17 15 12 10 7"/>
                                        <line x1="15" y1="12" x2="3" y2="12"/>
                                    </svg>
                                    <span>Войти</span>
                                </router-link>

                                <router-link to="/register" class="mobile-nav-item register-item" @click="closeMobileMenu">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="8.5" cy="7" r="4"/>
                                        <line x1="20" y1="8" x2="20" y2="14"/>
                                        <line x1="23" y1="11" x2="17" y2="11"/>
                                    </svg>
                                    <span>Создать аккаунт</span>
                                </router-link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </nav>
</template>

<style scoped>
/* Navbar */
.navbar {
    background: #0f0f0f;
    border-bottom: 1px solid #3d3d3d;
    position: sticky;
    top: 0;
    z-index: 1000;
    height: 56px;
}

.navbar-inner {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 16px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

/* Left Section */
.navbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.mobile-menu-btn {
    display: none;
    background: none;
    border: none;
    color: #f1f1f1;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.2s;
}

.mobile-menu-btn:hover {
    background: #3d3d3d;
}

.logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: opacity 0.2s;
}

.logo:hover {
    opacity: 0.9;
}

.logo-text {
    font-size: 20px;
    font-weight: 600;
    color: #f1f1f1;
    letter-spacing: -0.5px;
}

/* Center Section */
.navbar-center {
    flex: 1;
    display: flex;
    justify-content: center;
}

.navbar-menu {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    color: #f1f1f1;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
}

.nav-link:hover {
    background: #3d3d3d;
}

.nav-link.router-link-active {
    background: #272727;
}

.admin-link {
    color: #ff6b6b;
}

/* Right Section */
.navbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-login {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #065fd4;
    border: none;
    border-radius: 18px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-login:hover {
    background: #0c7ceb;
}

/* User Menu */
.user-menu-container {
    position: relative;
}

.user-menu-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    transition: background 0.2s;
}

.user-menu-btn:hover {
    background: #3d3d3d;
}

.user-avatar {
    width: 32px;
    height: 32px;
    background: #065fd4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: white;
}

/* User Dropdown */
.user-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 280px;
    background: #212121;
    border: 1px solid #3d3d3d;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    overflow: hidden;
}

.dropdown-header {
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-large {
    width: 40px;
    height: 40px;
    background: #065fd4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    color: white;
    flex-shrink: 0;
}

.user-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #f1f1f1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-email {
    font-size: 12px;
    color: #aaaaaa;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.dropdown-divider {
    height: 1px;
    background: #3d3d3d;
    margin: 4px 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    font-size: 14px;
    color: #f1f1f1;
    text-decoration: none;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    transition: background 0.2s;
}

.dropdown-item:hover {
    background: #3d3d3d;
}

.logout-item {
    color: #ff6b6b;
}

/* Mobile Sidebar */
.mobile-sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 2000;
    backdrop-filter: blur(4px);
}

.mobile-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 280px;
    max-width: 85vw;
    background: #212121;
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
}

.mobile-sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid #3d3d3d;
}

.mobile-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-size: 18px;
    font-weight: 600;
    color: #f1f1f1;
}

.close-sidebar-btn {
    background: none;
    border: none;
    color: #f1f1f1;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background 0.2s;
}

.close-sidebar-btn:hover {
    background: #3d3d3d;
}

.mobile-sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

.mobile-user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #1a1a1a;
    border-radius: 12px;
    margin-bottom: 16px;
}

.mobile-user-avatar {
    width: 48px;
    height: 48px;
    background: #065fd4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 600;
    color: white;
    flex-shrink: 0;
}

.mobile-user-info {
    flex: 1;
    min-width: 0;
}

.mobile-user-name {
    font-size: 15px;
    font-weight: 600;
    color: #f1f1f1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mobile-user-email {
    font-size: 13px;
    color: #aaaaaa;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mobile-nav-section {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.mobile-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #f1f1f1;
    text-decoration: none;
    background: none;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
    text-align: left;
    width: 100%;
}

.mobile-nav-item:hover {
    background: #3d3d3d;
}

.mobile-nav-item.router-link-active {
    background: #3d3d3d;
}

.admin-item {
    color: #ff6b6b;
}

.register-item {
    background: #065fd4;
    color: white;
}

.register-item:hover {
    background: #0c7ceb;
}

.logout-item {
    color: #ff6b6b;
}

.mobile-nav-divider {
    height: 1px;
    background: #3d3d3d;
    margin: 8px 0;
}

/* Transitions */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

.sidebar-enter-active,
.sidebar-leave-active {
    transition: opacity 0.3s ease;
}

.sidebar-enter-active .mobile-sidebar,
.sidebar-leave-active .mobile-sidebar {
    transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
    opacity: 0;
}

.sidebar-enter-from .mobile-sidebar {
    transform: translateX(-100%);
}

.sidebar-leave-to .mobile-sidebar {
    transform: translateX(-100%);
}

/* Responsive */
@media (max-width: 768px) {
    .mobile-menu-btn {
        display: block;
    }

    .navbar-center {
        display: none;
    }

    .logo-text {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .navbar-inner {
        padding: 0 12px;
    }

    .btn-login span {
        display: none;
    }
}
</style>