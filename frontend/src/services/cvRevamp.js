// services/cvRevampService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
});

const cvRevamp = {
  async revamp(jobId) {
    try {
      const token = localStorage.getItem("token");
      const response = await api.post(
        "/cv-revamp",
        { jobId },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      return response.data;
    } catch (err) {
      console.error("Error revamping CV:", err.response?.data || err.message);
      throw err;
    }
  },
};

export default cvRevamp;
