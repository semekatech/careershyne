import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
  headers: {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  },
});

// Attach token to protected requests
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

// Handle API errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const { status } = error.response;
      if (status === 401) {
        console.warn("Unauthorized. Token may have expired.");
      } else if (status === 403) {
        console.warn("Forbidden. You might lack permissions.");
      }
    }
    return Promise.reject(error);
  }
);

// Public instance (no token)
const publicApi = axios.create({
  baseURL: "https://careershyne.com/api/jobs",
});

const JobService = {
  /** 🔒 Authenticated routes */
  async createJob(payload) {
    const response = await api.post("/add", payload);
    return response.data;
  },

  async getJobs() {
    const response = await api.get("/all");
    return response.data;
  },

  async getPersonalizedJobs() {
    const response = await api.get("/personalized-jobs");
    return response.data;
  },
 async markInterested(jobId) {
    const response = await api.post(`/jobs/${jobId}/interested`);
    return response.data;
  },
  async getUsersJobs() {
    const response = await api.get("/user-jobs");
    return response.data;
  },

  async getPublicJobs(
    page = 1,
    search = "",
    county = "",
    type = "",
    category = ""
  ) {
    try {
      const response = await publicApi.get("/public", {
        params: { page, search, county, type, category },
      });
      return response.data;
    } catch (err) {
      console.error("Error fetching public jobs:", err);
      throw err;
    }
  },
};

export default JobService;
