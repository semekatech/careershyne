// services/DashboardService.js

import axios from "axios";
const API_URL = "https://demo.ngumzo.com/api/dashboard";
export default {
  async getDashboardStats() {
    try {
      const res = await axios.get(`${API_URL}/stats`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      return {
        campaigns: res.data.campaigns,
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
