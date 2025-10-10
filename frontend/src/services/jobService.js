import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/jobs",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});

// Request interceptor: adds the token dynamically in case it changes
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

// Response interceptor: handle errors globally
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const { status } = error.response;
      if (status === 401) {
        console.warn("Unauthorized. Token may have expired.");
        // Optionally redirect to login page
      } else if (status === 403) {
        console.warn("Forbidden. You might have exceeded limits or lack permissions.");
      }
    }
    return Promise.reject(error);
  }
);

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

  async getUsersJobs() {
    try {
      const response = await api.get("/user-jobs");
      return response.data;
    } catch (err) {
      console.error("Error fetching jobs:", err);
      throw err;
    }
  },
};

export default JobService;
