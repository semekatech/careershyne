// src/services/ProfileService.js
import axios from "axios";

const API = "https://careershyne.com/api/api/auth/";

// Create axios instance
const api = axios.create({
  baseURL: API,
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

export default {
  updateProfile(formData) {
    return api.post("profile-setup", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  },

  getProfile() {
    return api.get("verify-token");
  },

  fetchProfile() {
    return api.get("fetch-profile");
  },
  updatePassword(payload) {
    return api.post("update-password", payload);
  },
};
