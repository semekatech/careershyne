import axios from "axios";

const API_URL = "https://careershyne.com/api/users";

export default {
  store: (formData) => {
    return axios.post(`${API_URL}/save`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },
  update: (id, formData) => {
    return axios.post(`${API_URL}/update/${id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },

  get: () => {
    return axios.get(`${API_URL}/all`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
;
  },
  fetch: (id) => {
    return axios.get(`${API_URL}/get/${id}`);
  },
  toggleStatus: (id, newStatus) => {
    return axios.put(`${API_URL}/${id}/toggle-status`, { status: newStatus });
  },
};
