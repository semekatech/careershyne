import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});

api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const { status } = error.response;
      if (status === 401) {
        console.warn("Unauthorized. Token may have expired.");
      } else if (status === 403) {
        console.warn(
          "Forbidden. You might have exceeded limits or lack permissions."
        );
      }
    }
    return Promise.reject(error);
  }
);

// Public API instance (no token required)
const publicApi = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
});

const JobService = {
  // Protected routes
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
  async getUsersJobs() {
    try {
      const response = await api.get("/user-jobs");
      return response.data;
    } catch (err) {
      console.error("Error fetching jobs:", err);
      throw err;
    }
  },

  // Public route
  async getPublicJobs() {
    try {
      const response = await publicApi.get("/public"); 
      return response.data;
    } catch (err) {
      console.error("Error fetching public jobs:", err);
      throw err;
    }
  },
};

export default JobService;
