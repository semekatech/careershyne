// src/stores/profile.js
import { defineStore } from "pinia";
import ProfileService from "../services/ProfileService";

export const useProfileStore = defineStore("profile", {
  state: () => ({
    profile: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchProfile() {
      this.loading = true;
      this.error = null;
      try {
        const res = await ProfileService.getProfile();
        this.profile = res.data.user;
      } catch (err) {
        console.error("Error fetching profile:", err.response?.data || err.message);
        this.error = "Failed to load profile";
      } finally {
        this.loading = false;
      }
    },

    async updateProfile(formData) {
      this.loading = true;
      this.error = null;
      try {
        const res = await ProfileService.updateProfile(formData);
        this.profile = res.data.user;
      } catch (err) {
        console.error("Error updating profile:", err.response?.data || err.message);
        this.error = "Failed to update profile";
      } finally {
        this.loading = false;
      }
    },
  },
});
