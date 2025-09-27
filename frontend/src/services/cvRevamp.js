// services/cvRevampService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});

const cvRevamp = {
  async revamp(jobId) {
    try {
      const response = await api.post("/cv-revamp", { jobId });
      return response.data; // ✅ unwrap data
    } catch (err) {
      console.error("Error revamping CV:", err.response?.data || err.message);
      throw err;
    }
  },
};

export default cvRevamp;
