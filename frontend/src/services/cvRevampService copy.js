// src/services/apiService.js
import axios from "axios";

const API_URL = "https://careershyne.com/api/ai/cv-revamp";

export async function generateCvRevamp(formData) {
  try {
    const response = await axios.post(API_URL, formData, {
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