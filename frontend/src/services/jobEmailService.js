// src/services/apiService.js
import axios from "axios";

const API_URL = "https://careershyne.com/api/ai/email-template";

export async function generateJobEmail(payload) {
  try {
    const headers = {};

    // If payload is FormData (PDF, file uploads), let browser set content-type
    if (!(payload instanceof FormData)) {
      headers["Content-Type"] = "application/json";
    }

    const response = await axios.post(API_URL, payload, { headers });
    return response.data;
  } catch (error) {
    console.error("API call failed:", error.response?.data || error.message);
    throw error;
  }
}
