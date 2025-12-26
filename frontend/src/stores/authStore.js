import { defineStore } from 'pinia';
import { authAPI } from '@/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'ADMIN',
    isUser: (state) => state.user?.role === 'USER' || state.user?.role === 'ADMIN',
    isGuest: (state) => !state.user || state.user?.role === 'GUEST',
  },

  actions: {
    async register(credentials) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await authAPI.register(credentials);
        this.token = data.token;
        this.user = data.user;
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        return data;
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка регистрации';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async login(credentials) {
      this.loading = true;
      this.error = null;
      try {
        const { data } = await authAPI.login(credentials);
        this.token = data.token;
        this.user = data.user;
        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        return data;
      } catch (error) {
        this.error = error.response?.data?.error || 'Ошибка входа';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchUser() {
      if (!this.token) return;
      
      this.loading = true;
      try {
        const { data } = await authAPI.getMe();
        this.user = data.user;
        localStorage.setItem('user', JSON.stringify(data.user));
      } catch (error) {
        this.logout();
      } finally {
        this.loading = false;
      }
    },

    logout() {
      this.user = null;
      this.token = null;
      this.error = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    },

    initializeAuth() {
      const token = localStorage.getItem('token');
      const user = localStorage.getItem('user');
      
      if (token && user) {
        this.token = token;
        this.user = JSON.parse(user);
        // Проверяем валидность токена
        this.fetchUser();
      }
    },
  },
});