<template>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <!-- Left Section -->
                <div class="footer-left">
                    <router-link to="/" class="footer-brand">
                        <svg width="24" height="24" viewBox="0 0 32 32">
                            <rect
                                width="32"
                                height="32"
                                rx="6"
                                fill="#ff0000"
                            />
                            <path d="M12 10L22 16L12 22V10Z" fill="white" />
                        </svg>
                        <span class="brand-name">VideoHub</span>
                    </router-link>
                    <p class="footer-tagline">Делитесь видео с миром</p>
                </div>

                <!-- Center Section - Links -->
                <!-- <div class="footer-links">
                    <div class="link-group">
                        <router-link to="/" class="footer-link">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"
                                />
                            </svg>
                            Главная
                        </router-link>
                        <router-link
                            v-if="authStore.isAuthenticated"
                            to="/profile"
                            class="footer-link"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            Профиль
                        </router-link>
                        <router-link
                            v-if="authStore.isAdmin"
                            to="/admin"
                            class="footer-link admin-link"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                />
                            </svg>
                            Админ-панель
                        </router-link>
                    </div>
                </div> -->

                <!-- Right Section -->
                <div class="footer-right">
                    <div class="footer-copyright">
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <path d="M14.5 9a3.5 3.5 0 1 0 0 7H16" />
                        </svg>
                        <span>{{ currentYear }} VideoHub</span>
                    </div>
                    <p class="footer-info">Сделано с любовью к видео</p>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <div class="footer-stats">
                    <div class="stat-item">
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                            />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span>Сообщество создателей</span>
                    </div>
                    <div class="stat-divider">•</div>
                    <div class="stat-item">
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <polygon
                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
                            />
                        </svg>
                        <span>Качественный контент</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();
const currentYear = computed(() => new Date().getFullYear());
</script>

<style scoped>
.footer {
    background: #0f0f0f;
    border-top: 1px solid #3d3d3d;
    margin-top: auto;
    padding: 32px 0 20px;
}

.footer-container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 0 24px;
}

.footer-content {
    display: flex;
    grid-template-columns: 2fr auto 1fr;
    justify-content: space-between;
    gap: 40px;
    align-items: start;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #3d3d3d;
}

/* Left Section */
.footer-left {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.footer-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    margin-bottom: 4px;
    transition: opacity 0.2s;
}

.footer-brand:hover {
    opacity: 0.8;
}

.brand-name {
    font-size: 18px;
    font-weight: 600;
    color: #f1f1f1;
    letter-spacing: -0.5px;
}

.footer-tagline {
    font-size: 13px;
    color: #717171;
    margin: 0;
}

/* Center Section - Links */
.footer-links {
    display: flex;
    align-items: center;
}

.link-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.footer-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 500;
    color: #aaaaaa;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.2s;
}

.footer-link:hover {
    color: #f1f1f1;
    background: #3d3d3d;
}

.footer-link.router-link-active {
    color: #f1f1f1;
}

.admin-link {
    color: #ff6b6b;
}

.admin-link:hover {
    background: rgba(255, 107, 107, 0.1);
}

/* Right Section */
.footer-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.footer-copyright {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #aaaaaa;
}

.footer-copyright svg {
    opacity: 0.5;
}

.footer-info {
    font-size: 12px;
    color: #717171;
    margin: 0;
}

/* Bottom Bar */
.footer-bottom {
    display: flex;
    justify-content: center;
}

.footer-stats {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background: #1a1a1a;
    border-radius: 20px;
    border: 1px solid #3d3d3d;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #aaaaaa;
}

.stat-item svg {
    color: #065fd4;
    opacity: 0.8;
}

.stat-divider {
    color: #3d3d3d;
    font-size: 12px;
}

/* Responsive */
@media (max-width: 768px) {
    .footer {
        padding: 24px 0 16px;
    }

    .footer-container {
        padding: 0 16px;
    }

    .footer-content {
        grid-template-columns: 1fr;
        gap: 24px;
        text-align: center;
    }

    .footer-left {
        align-items: center;
    }

    .link-group {
        flex-direction: row;
        justify-content: center;
        flex-wrap: wrap;
    }

    .footer-right {
        align-items: center;
    }

    .footer-stats {
        flex-direction: column;
        gap: 8px;
        padding: 16px;
        width: 100%;
    }

    .stat-divider {
        display: none;
    }
}

@media (max-width: 480px) {
    .footer-brand {
        flex-direction: column;
        gap: 8px;
    }

    .brand-name {
        font-size: 16px;
    }

    .link-group {
        gap: 4px;
    }

    .footer-link {
        padding: 8px 12px;
        font-size: 12px;
    }

    .footer-stats {
        padding: 12px;
    }

    .stat-item {
        font-size: 11px;
    }
}
</style>
