import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});

const JobService = {
  async createJob(payload) {
    try {
      const response = await api.post("/add", payload);
      return response.data;
    } catch (err) {
      console.error("Error saving job:", err);
      throw err;
    }
  },

  async getJobs() {
    try {
      const response = await api.get("/all");
      return response.data;
    } catch (err) {
      console.error("Error fetching jobs:", err);
      throw err;
    }
  },
};

export default JobService;
