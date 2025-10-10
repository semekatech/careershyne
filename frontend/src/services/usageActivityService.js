// services/usageActivityService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com", // change to your API
  timeout: 5000,
});

// Optional: attach auth token
api.interceptors.request.use(config => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

const UsageActivityService = {
  getActivities() {
    // Example: GET /usage-activities
    return api.get("/users/usage-activities");
  },

  // Optional: fetch by user
  getActivitiesByUser(userId) {
    return api.get(`/users/${userId}/usage-activities`);
  },
};

export default UsageActivityService;
