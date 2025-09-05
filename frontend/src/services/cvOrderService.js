// src/services/cvOrderService.js
import axios from "axios";
const API_URL = "https://careershyne.com/api"; // backend base URL
// Submit new CV order
export const submitCvOrder = async (form) => {
  const formData = new FormData();
  formData.append("fullname", form.fullname);
  formData.append("email", form.email);
  formData.append("type", form.type);
  formData.append("phone", form.phone);
  formData.append("cv", form.cv);
  const { data } = await axios.post(`${API_URL}/cv-orders`, formData, {
    headers: { "Content-Type": "multipart/form-data" },
  });
  return data;
};

// Get order by ID
export const getCvOrder = async (id) => {
  const { data } = await axios.get(`${API_URL}/cv-orders/${id}`);
  return data;
};
// Initiate M-Pesa STK push
export async function initiatePayment(payload) {
  const res = await axios.post(`/api/payments/initiate`, payload);
  return res.data;
}
// ✅ Check payment status (polling)
export async function checkPaymentStatus(trackID) {
  const { data } = await axios.post(`/api/payments/status`, {
    track_link: trackID,
  });
  return data;
}
