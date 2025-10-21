// src/services/renewPlan.js
import axios from "axios";

// Create Axios instance with baseURL and timeout
const api = axios.create({
  baseURL: "https://careershyne.com/api",
  timeout: 30000,
});

// Attach auth token if present
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => Promise.reject(error));

// Renew plan service
const renewPlanService = {
  // Initiate M-Pesa STK push
async initiatePayment(payload) {
  const payloadWithType = { ...payload, payment_type: "subscription" };
  try {
    console.log("🚀 Initiating payment with payload:", payloadWithType);
    const { data } = await api.post("/payments/pay", payloadWithType);
    console.log("✅ Payment initiation response:", data);
    return data;
  } catch (error) {
    console.error("❌ Payment initiation failed:", {
      message: error.message,
      status: error.response?.status,
      response: error.response?.data,
    });
    throw error;
  }
},

  // Check payment status (polling)
  async checkPaymentStatus(trackID) {
    const { data } = await api.post("/payments/status", {
      track_link: trackID,
    });
    return data;
  },

  async getPlan(orderID) {
    const { data } = await api.get(`/plans/${orderID}`);
    return data;
  }
};

export default renewPlanService;
