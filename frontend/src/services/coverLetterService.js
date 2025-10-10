// src/services/apiService.js
import axios from "axios";

// Create an Axios instance
const api = axios.create({
  baseURL: "https://careershyne.com/api/api",
});

// Add request interceptor to include token if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// --- Cover Letter Generation ---
export async function generateCoverLetter(formData) {
  try {
    const response = await api.post("/ai/cover-letter", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
    return response.data;
  } catch (error) {
    console.error("API call failed:", error.response || error.message);
    throw error;
  }
}
