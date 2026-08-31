import { defineStore } from 'pinia';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userRole: (state) => state.user?.role || null,
  },

  actions: {
    async login(email, password) {
      try {
        const response = await api.post('/login', { email, password });
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem('token', this.token);
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        return true;
      } catch (error) {
        console.error('Login failed:', error);
        return false;
      }
    },

    async logout() {
      try {
      await api.post('/logout');
      } catch (error) {
        console.error('Logout failed:', error);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        delete api.defaults.headers.common['Authorization'];
      }
    },

    async fetchUser() {
      if (!this.token) return;
      
      try {
      const response = await api.get('/user');
        this.user = response.data;
      } catch (error) {
        console.error('Failed to fetch user:', error);
        this.logout();
      }
    },

    initialize() {
      if (this.token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        this.fetchUser();
      }
    },
  },
});
