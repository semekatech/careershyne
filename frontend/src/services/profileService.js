// ProfileService.js
import axios from "axios";

const API = "https://careershyne.com/api/auth/";

export default {

  updateProfile(formData) {
    return axios.post(API + "profile-setup", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  },
};
