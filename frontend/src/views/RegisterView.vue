<script setup>
import { reactive, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: "",
    password: "",
});

const showPassword = ref(false);

const passwordStrength = computed(() => {
    const password = form.password;
    if (!password) return { level: 0, text: "", color: "" };

    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;

    if (strength <= 2) return { level: 1, text: "Слабый", color: "#ff6b6b" };
    if (strength <= 3) return { level: 2, text: "Средний", color: "#ffa500" };
    return { level: 3, text: "Сильный", color: "#00c853" };
});

const handleRegister = async () => {
    try {
        await authStore.register(form);
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
                    <h1>Создать аккаунт</h1>
                    <p class="auth-subtitle">Присоединяйтесь к сообществу</p>
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

                <!-- Register Form -->
                <form @submit.prevent="handleRegister" class="auth-form">
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
                        <div class="password-wrapper">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="form-input"
                                placeholder="Минимум 6 символов"
                                minlength="6"
                                required
                                autocomplete="new-password"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="password-toggle"
                            >
                                <svg
                                    v-if="!showPassword"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"
                                    />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg
                                    v-else
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"
                                    />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>

                        <!-- Password Strength Indicator -->
                        <Transition name="fade">
                            <div
                                v-if="form.password.length > 0"
                                class="password-strength"
                            >
                                <div class="strength-bars">
                                    <div
                                        v-for="i in 3"
                                        :key="i"
                                        class="strength-bar"
                                        :class="{
                                            active: i <= passwordStrength.level,
                                        }"
                                        :style="{
                                            backgroundColor:
                                                i <= passwordStrength.level
                                                    ? passwordStrength.color
                                                    : '',
                                        }"
                                    ></div>
                                </div>
                                <span
                                    class="strength-text"
                                    :style="{ color: passwordStrength.color }"
                                >
                                    {{ passwordStrength.text }}
                                </span>
                            </div>
                        </Transition>

                        <p class="input-hint">
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            Используйте буквы, цифры и символы для безопасности
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="btn-submit"
                        :disabled="authStore.loading"
                    >
                        <span v-if="!authStore.loading">Создать аккаунт</span>
                        <div v-else class="loader-spinner">
                            <div class="spinner"></div>
                            <span>Регистрация...</span>
                        </div>
                    </button>
                </form>

                <!-- Footer -->
                <div class="auth-footer">
                    <div class="divider">
                        <span>или</span>
                    </div>
                    <p class="footer-text">
                        Уже есть аккаунт?
                        <router-link to="/login" class="link-primary">
                            Войти
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

.password-wrapper {
    position: relative;
}

.form-input {
    padding: 14px 16px;
    background: #121212;
    border: 1px solid #3d3d3d;
    border-radius: 8px;
    font-size: 14px;
    color: #f1f1f1;
    transition: all 0.2s;
    width: 100%;
}

.password-wrapper .form-input {
    padding-right: 48px;
}

.form-input::placeholder {
    color: #717171;
}

.form-input:focus {
    outline: none;
    border-color: #065fd4;
    background: #1a1a1a;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #aaaaaa;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.password-toggle:hover {
    color: #f1f1f1;
}

/* Password Strength */
.password-strength {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 4px;
}

.strength-bars {
    display: flex;
    gap: 4px;
    flex: 1;
}

.strength-bar {
    height: 4px;
    flex: 1;
    background: #3d3d3d;
    border-radius: 2px;
    transition: all 0.3s;
}

.strength-bar.active {
    opacity: 1;
}

.strength-text {
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.input-hint {
    font-size: 12px;
    color: #717171;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}

.input-hint svg {
    flex-shrink: 0;
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

.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
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
