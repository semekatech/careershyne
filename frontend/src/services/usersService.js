import axios from "axios";

const API_URL = "https://careershyne.com/users";

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
  },
  fetch: (id) => {
    return axios.get(`${API_URL}/get/${id}`);
  },
  toggleStatus: (id, newStatus) => {
    return axios.put(`${API_URL}/${id}/toggle-status`, { status: newStatus });
  },
 impersonate(userId) {
  return axios.post(
    `${API_URL}/users/${userId}/impersonate`,
    {}, // empty body
    {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    }
  );
}

};
