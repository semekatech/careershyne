// services/cvRevampService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/api/jobs",
});

// ✅ Request interceptor to attach the latest token dynamically
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

const generate = {
  async generate(jobId) {
    try {
      const response = await api.post("/email-template", { jobId });
      return response.data; 
    } catch (err) {
      console.error("Error revamping CV:", err.response?.data || err.message);
      throw err;
    }
  },
};

export default generate;
