// src/services/ProfileService.js
import axios from "axios";

const API = "https://careershyne.com/api/auth/";

export default {
  updateProfile(formData) {
    return axios.post(API + "profile-setup", formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
        "Content-Type": "multipart/form-data",
      },
    });
  },

  // ✅ Fetch user profile
  getProfile() {
    return axios.get(API + "verify-token", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
  },
};
