// src/stores/auth.js
import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    token: localStorage.getItem("token") || null,
    user: JSON.parse(localStorage.getItem("user")) || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
  },

  actions: {
    setToken(token) {
      this.token = token;
      localStorage.setItem("token", token);
    },

    setUser(user) {
      this.user = user;
      localStorage.setItem("user", JSON.stringify(user));
    },

    clearToken() {
      this.token = null;
      this.user = null;
      localStorage.removeItem("token");
      localStorage.removeItem("user");
    },

    clearUser() {
      this.user = null;
      localStorage.removeItem("user");
    },

    async refreshUser() {
      try {
        const response = await axios.get("https://demo.ngumzo.com/api/me", {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        });
        this.setUser(response.data);
      } catch (error) {
        console.error("Failed to refresh user:", error);
      }
    },
  },
});
