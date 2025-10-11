import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api",
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

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
