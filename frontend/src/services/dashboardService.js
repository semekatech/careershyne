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
      const { data } = await axiosInstance.get("/dashboard/stats");
      return {
        pending: data.pending,
        approved: data.approved,
        all: data.all,
        totalAmount: data.totalAmount,
        totalPendingAmount: data.totalPendingAmount,
        totalApprovedAmount: data.totalApprovedAmount,
        graphData: data.graphData,
      };
    } catch (error) {
      console.error("Error loading dashboard stats:", error);
      throw error;
    }
  },

  // 🟢 User Stats
  async getUserStats() {
    try {
      const { data } = await axiosInstance.get("/dashboard/user-stats");
      return {
        total_jobs: data.total_jobs,
        total_applied: data.total_applied,
        total_saved: data.total_saved,
      };
    } catch (error) {
      console.error("Error loading user stats:", error);
      throw error;
    }
  },
};
