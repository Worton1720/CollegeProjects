<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: "",
    password: "",
});

const handleLogin = async () => {
    try {
        await authStore.login(form);
        router.push("/");
    } catch (error) {
        // Error is handled by authStore
    }
};
</script>

<template>
    <div class="auth-view">
        <div class="auth-container">
            <div class="auth-card">
                <!-- Logo & Header -->
                <div class="auth-header">
                    <router-link to="/" class="auth-logo">
                        <svg width="48" height="48" viewBox="0 0 48 48">
                            <rect
                                width="48"
                                height="48"
                                rx="8"
                                fill="#ff0000"
                            />
                            <path d="M18 14L34 24L18 34V14Z" fill="white" />
                        </svg>
                    </router-link>
                    <h1>Добро пожаловать</h1>
                    <p class="auth-subtitle">
                        Войдите в аккаунт для продолжения
                    </p>
                </div>

                <!-- Error Banner -->
                <Transition name="slide-down">
                    <div v-if="authStore.error" class="error-banner">
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>{{ authStore.error }}</span>
                    </div>
                </Transition>

                <!-- Login Form -->
                <form @submit.prevent="handleLogin" class="auth-form">
                    <div class="form-group">
                        <label class="form-label">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                                />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                            Email
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="form-input"
                            placeholder="example@email.com"
                            required
                            autocomplete="email"
                        />
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    x="3"
                                    y="11"
                                    width="18"
                                    height="11"
                                    rx="2"
                                    ry="2"
                                />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            Пароль
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            class="form-input"
                            placeholder="Введите пароль"
                            required
                            autocomplete="current-password"
                        />
                    </div>

                    <button
                        type="submit"
                        class="btn-submit"
                        :disabled="authStore.loading"
                    >
                        <span v-if="!authStore.loading">Войти</span>
                        <div v-else class="loader-spinner">
                            <div class="spinner"></div>
                            <span>Вход...</span>
                        </div>
                    </button>
                </form>

                <!-- Footer -->
                <div class="auth-footer">
                    <div class="divider">
                        <span>или</span>
                    </div>
                    <p class="footer-text">
                        Нет аккаунта?
                        <router-link to="/register" class="link-primary">
                            Создать аккаунт
                        </router-link>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <router-link to="/" class="back-link">
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                На главную
            </router-link>
        </div>
    </div>
</template>

<style scoped>
.auth-view {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0f0f0f;
    padding: 40px 20px;
    position: relative;
}

.auth-view::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 300px;
    background: linear-gradient(
        180deg,
        rgba(255, 0, 0, 0.1) 0%,
        transparent 100%
    );
    pointer-events: none;
}

.auth-container {
    width: 100%;
    max-width: 440px;
    position: relative;
    z-index: 1;
}

.auth-card {
    background: #212121;
    border: 1px solid #3d3d3d;
    border-radius: 16px;
    padding: 48px 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

/* Header */
.auth-header {
    text-align: center;
    margin-bottom: 32px;
}

.auth-logo {
    display: inline-block;
    margin-bottom: 24px;
    transition: transform 0.2s;
}

.auth-logo:hover {
    transform: scale(1.05);
}

.auth-header h1 {
    font-size: 28px;
    font-weight: 600;
    color: #f1f1f1;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.auth-subtitle {
    font-size: 14px;
    color: #aaaaaa;
}

/* Error Banner */
.error-banner {
    background: rgba(204, 0, 0, 0.1);
    border: 1px solid rgba(255, 0, 0, 0.3);
    color: #ff6b6b;
    padding: 14px 16px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.error-banner svg {
    flex-shrink: 0;
}

/* Form */
.auth-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: #f1f1f1;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label svg {
    color: #aaaaaa;
}

.form-input {
    padding: 14px 16px;
    background: #121212;
    border: 1px solid #3d3d3d;
    border-radius: 8px;
    font-size: 14px;
    color: #f1f1f1;
    transition: all 0.2s;
}

.form-input::placeholder {
    color: #717171;
}

.form-input:focus {
    outline: none;
    border-color: #065fd4;
    background: #1a1a1a;
}

.btn-submit {
    background: #ff0000;
    color: white;
    padding: 14px 24px;
    border: none;
    border-radius: 24px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 48px;
}

.btn-submit:hover:not(:disabled) {
    background: #cc0000;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 0, 0, 0.3);
}

.btn-submit:active:not(:disabled) {
    transform: translateY(0);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.loader-spinner {
    display: flex;
    align-items: center;
    gap: 12px;
}

.spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Footer */
.auth-footer {
    margin-top: 32px;
}

.divider {
    position: relative;
    text-align: center;
    margin: 24px 0;
}

.divider::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 1px;
    background: #3d3d3d;
}

.divider span {
    position: relative;
    background: #212121;
    padding: 0 16px;
    font-size: 13px;
    color: #717171;
}

.footer-text {
    text-align: center;
    font-size: 14px;
    color: #aaaaaa;
}

.link-primary {
    color: #3ea6ff;
    font-weight: 500;
    text-decoration: none;
    margin-left: 4px;
    transition: color 0.2s;
}

.link-primary:hover {
    color: #65b8ff;
    text-decoration: underline;
}

/* Back Link */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
    color: #aaaaaa;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s;
    padding: 8px 12px;
    border-radius: 8px;
}

.back-link:hover {
    color: #f1f1f1;
    background: rgba(255, 255, 255, 0.05);
}

/* Transitions */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Responsive */
@media (max-width: 480px) {
    .auth-card {
        padding: 32px 24px;
        border-radius: 12px;
    }

    .auth-header h1 {
        font-size: 24px;
    }

    .form-input {
        padding: 12px 14px;
    }
}
</style>
