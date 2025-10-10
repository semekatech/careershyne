// services/cvRevampService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/jobs",
});

// ✅ Request interceptor to attach the latest token dynamically
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

const cvRevamp = {
  async revamp(jobId) {
    try {
      const response = await api.post("/cv-revamp", { jobId });
      return response.data; // No need to manually add Authorization here
    } catch (err) {
      console.error("Error revamping CV:", err.response?.data || err.message);
      throw err;
    }
  },
};

export default cvRevamp;
