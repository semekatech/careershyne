// services/DashboardService.js

import axios from "axios";
const API_URL = "https://careershyne.com/api/dashboard";
export default {
  async getDashboardStats() {
    try {
      const res = await axios.get(`${API_URL}/promoter/stats`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return {
        pending: res.data.pending,
        approved: res.data.approved,
        team: res.data.team,
      };
    } catch (error) {
      console.error("Error loading dashboard stats:", error);
      throw error;
    }
  },
};
