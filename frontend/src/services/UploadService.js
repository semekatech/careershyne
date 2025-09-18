// UploadService.js
import axios from "axios";

const API = "https://careershyne.com/api/ai/upload";

export default {
  uploadFile: (file, recaptchaToken, onUploadProgress) => {
    const formData = new FormData();
    formData.append("cvFile", file); // match backend expected key
    formData.append("recaptchaToken", recaptchaToken);

    return axios.post(API, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      onUploadProgress,
    });
  },
};
