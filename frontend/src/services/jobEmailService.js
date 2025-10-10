// src/services/emailService.js
import axios from "axios";

// Create an Axios instance
const api = axios.create({
  baseURL: "https://careershyne.com",
});

// Attach interceptor to include auth token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

/**
 * Generate a job email template
 * @param {FormData} formData - Can contain email_text or email_file
 */
// src/services/emailService.js
export async function generateJobEmail(formData) {
  try {
    const response = await api.post("/ai/email-template", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  } catch (error) {
    console.error("API call failed:", error.response || error.message);
    throw error;
  }
}

