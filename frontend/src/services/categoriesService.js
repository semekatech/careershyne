// services/OptionsService.js
import axios from "axios";

// Create an Axios instance
const api = axios.create({
  baseURL: "https://careershyne.com/api",
});

// ✅ Apply request interceptor
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
    if (error.response && error.response.status === 401) {
      console.warn("Unauthorized, logging out...");
      localStorage.removeItem("token");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default {
  getIndustries() {
    return api.get("/categories");
  },
  store(data) {
    return api.post("/categories", data);
  },
  update(id, data) {
    return api.put(`/categories/${id}`, data);
  },
  delete(id) {
    return api.delete(`/categories/${id}`);
  },
};
