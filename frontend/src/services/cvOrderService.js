// src/services/cvOrderService.js
import axios from "axios";

const API_URL = "http://localhost:8000/api"; // change to your backend

export const submitCvOrder = async (form) => {
  const formData = new FormData();
  formData.append("fullname", form.fullname);
  formData.append("email", form.email);
  formData.append("phone", form.phone);
  formData.append("cv", form.cv);

  const { data } = await axios.post(`${API_URL}/cv-orders`, formData, {
    headers: { "Content-Type": "multipart/form-data" },
  });

  return data;
};
