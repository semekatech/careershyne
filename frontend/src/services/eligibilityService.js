import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});

const eligibilityService = {
  async checkEligibility(jobId) {
    try {
      const response = await api.post("/check-eligibility", { jobId });
      return response.data;
    } catch (err) {
      console.error("Error checking eligibility:", err);
      throw err;
    }
  },
};

export default eligibilityService;
