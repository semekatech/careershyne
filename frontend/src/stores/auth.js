// src/stores/auth.js
import { defineStore } from "pinia";
import axios from "axios";
import router from "@/router";

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

    async refreshUser() {
      try {
        const response = await this.api.get("/me");
        this.setUser(response.data);
      } catch (error) {
        console.error("Failed to refresh user:", error);
      }
    },
  },

  persist: false,
  api: (() => {
    const instance = axios.create({
      baseURL: "https://careershyne.com/api",
    });

    // Request → always attach token
    instance.interceptors.request.use((config) => {
      const auth = useAuthStore();
      if (auth.token) {
        config.headers.Authorization = `Bearer ${auth.token}`;
      }
      return config;
    });

    // Response → auto logout on 401
    instance.interceptors.response.use(
      (res) => res,
      (error) => {
        const auth = useAuthStore();
        if (error.response && error.response.status === 401) {
          auth.clearToken();
          router.push("/login");
        }
        return Promise.reject(error);
      }
    );

    return instance;
  })(),
});
