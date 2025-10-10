// services/DashboardService.js

import axios from "axios";
const API_URL = "https://careershyne.com/api/api/dashboard";
export default {
  async getDashboardStats() {
    try {
      const res = await axios.get(`${API_URL}/stats`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return {
        pending: res.data.pending,
        approved: res.data.approved,
        all: res.data.all,
        totalAmount: res.data.totalAmount,
        totalPendingAmount: res.data.totalPendingAmount,
        totalApprovedAmount: res.data.totalApprovedAmount,
        graphData: res.data.graphData,
      };
    } catch (error) {
      console.error("Error loading dashboard stats:", error);
      throw error;
    }
  },
};
