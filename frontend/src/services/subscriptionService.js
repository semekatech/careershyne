// services/subscriptionService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api", 
  timeout: 5000,
});

// Attach auth token if needed
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

const subscriptionService = {
  async getLimits() {
    try {
      const response = await api.post("/users/limits");
      return {
        cv: response.data.cv ?? 0,
        coverletters: response.data.coverLetters ?? 0,
        emails: response.data.emails ?? 0,
        checks: response.data.checks ?? 0,
        plan: response.data.plan ?? "Free",
      };
    } catch (err) {
      console.error("Failed to fetch subscription limits:", err);
      return {
        cv: 0,
        coverLetters: 0,
        emails: 0,
        plan: "Free",
      };
    }
  },
};

export default subscriptionService
