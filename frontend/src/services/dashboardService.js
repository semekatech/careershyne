// src/services/DashboardService.js

import axios from "axios";

const API_URL = "https://careershyne.com/api";

// ✅ Create axios instance
const axiosInstance = axios.create({
  baseURL: API_URL,
});

// ✅ Add interceptors directly here
axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

axiosInstance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      console.warn("Unauthorized! Redirecting to login...");
      localStorage.removeItem("token");
      window.location.href = "/login";
    } else if (!error.response) {
      console.error("Network error, please check your connection.");
    }
    return Promise.reject(error);
  }
);

export default {
  // 🟣 Admin / Dashboard Stats
  async getDashboardStats() {
    try {
      const response = await axiosInstance.get("/dashboard/stats");
      const data = response.data?.data || response.data || {};

      return {
        pending: data.pending ?? 0,
        approved: data.approved ?? 0,
        all: data.all ?? 0,
        totalAmount: data.totalAmount ?? 0,
        totalPendingAmount: data.totalPendingAmount ?? 0,
        totalApprovedAmount: data.totalApprovedAmount ?? 0,
        graphData: data.graphData ?? [],
      };
    } catch (error) {
      console.error("Error loading dashboard stats:", error);
      throw error;
    }
  },

  // 🟢 User Stats
  async getUserStats() {
    try {
      const response = await axiosInstance.get("/dashboard/user-stats");

      // ✅ This line ensures compatibility with any backend structure
      const data = response.data?.data || response.data || {};

      return {
        total_jobs: data.total_jobs ?? 0,
        total_applied: data.total_applied ?? 0,
        total_saved: data.total_saved ?? 0,
      };
    } catch (error) {
      console.error("Error loading user stats:", error);
      throw error;
    }
  },
};
