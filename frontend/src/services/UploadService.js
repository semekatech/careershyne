// services/UploadService.js
import axios from "axios";

const API = "https://careershyne.com/api/api/ai/upload";

export default {
  async uploadFile(file, hcaptchaToken, onUploadProgress) {
    const formData = new FormData();
    formData.append("file", file);
    formData.append("recaptchaToken", hcaptchaToken);

    return axios.post(API, formData, {
      headers: { "Content-Type": "multipart/form-data" },
      onUploadProgress,
    });
  },
};
