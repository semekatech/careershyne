// src/services/renewPlan.js
import axios from "axios";

// Create Axios instance with baseURL and timeout
const api = axios.create({
  baseURL: "https://careershyne.com",
  timeout: 5000,
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
  const { data } = await api.post("/payments/pay", payloadWithType);
  return data;
},


  // Check payment status (polling)
  async checkPaymentStatus(trackID) {
    const { data } = await api.post("/payments/status", {
      track_link: trackID,
    });
    return data;
  },

  // Optional: get subscription/order info
  async getPlan(orderID) {
    const { data } = await api.get(`/plans/${orderID}`);
    return data;
  }
};

export default renewPlanService;
