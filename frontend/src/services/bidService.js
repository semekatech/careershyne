// src/services/bidService.js
import axios from "axios";

const API_URL = "https://demo.ngumzo.com/api/bids";

export default {
  submitBid: (formData) => {
    return axios.post(`${API_URL}/submit`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
        "Content-Type": "application/json",
      },
    });
  },

  update: (id, formData) => {
    return axios.post(`${API_URL}/update/${id}`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
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
    return axios.get(`${API_URL}/get/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
  },

  approve: (id) => {
    return axios.post(`${API_URL}/${id}/approve`, {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
  },

  reject: (id) => {
    return axios.post(`${API_URL}/${id}/reject`, {}, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,
      },
    });
  },
};
