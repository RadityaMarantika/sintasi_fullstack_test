import { defineStore } from 'pinia';
import { api } from 'boot/axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: null,
  }),

  actions: {
    async login(payload) {
      const res = await api.post('/login', payload);

      this.token = res.data.access_token;
      this.user = res.data.user;

      localStorage.setItem('token', this.token);
    },

    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
    },
  },
});
