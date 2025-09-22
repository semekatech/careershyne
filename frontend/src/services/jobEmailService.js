// src/services/apiService.js
import axios from "axios";

const API_URL = "https://careershyne.com/api/ai/email-template";

export async function generateJobEmail(payload) {
  try {
    const response = await axios.post(API_URL, payload, {
      headers: {
        "Content-Type": "application/json",
      },
    });
    return response.data;
  } catch (error) {
    console.error("API call failed:", error.response?.data || error.message);
    throw error;
  }
}
