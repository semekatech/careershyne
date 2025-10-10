// src/services/cvOrderService.js
import axios from "axios";
const API_URL = "https://careershyne.com/api"; // backend base URL
// Submit new CV order
export const submitCvOrder = async (form) => {
  const formData = new FormData();

  // required fields
  formData.append("fullname", form.fullname);
  formData.append("email", form.email);
  formData.append("phone", form.phone);
  formData.append("type", form.type);
  if (form.cv) {
    formData.append("cv", form.cv);
  }
  formData.append("ref", form.ref);
  // optional extended fields
  formData.append("location", form.location || "");
  formData.append("careerGoal", form.careerGoal || "");
  formData.append("skills", form.skills || "");
  formData.append("linkedin", form.linkedin || "");
  formData.append("portfolio", form.portfolio || "");
  formData.append("coverRole", form.coverRole || "");
  formData.append("coverWhy", form.coverWhy || "");
  formData.append("coverStrengths", form.coverStrengths || "");
  // arrays -> stringify to JSON
  formData.append("education", JSON.stringify(form.education));
  formData.append("experience", JSON.stringify(form.experience));
  formData.append("certifications", JSON.stringify(form.certifications));

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
