// src/services/apiService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api",
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export async function generateCvRevamp(formData) {
  try {
    const response = await api.post("/ai/cv-revamp", formData, {
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
