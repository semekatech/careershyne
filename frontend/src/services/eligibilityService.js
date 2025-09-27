// services/eligibilityService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
});

// ✅ Request interceptor to dynamically attach the latest token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

const eligibilityService = {
  async checkEligibility(jobId) {
    try {
      const response = await api.post("/check-eligibility", { jobId });
      return response.data;
    } catch (err) {
      console.error("Error checking eligibility:", err.response?.data || err.message);
      throw err;
    }
  },
};

export default eligibilityService;
