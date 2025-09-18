// UploadService.js
import axios from "axios";

const API = "https://careershyne.com/api/ai/upload";

export default {
  // ❗ Modify the method to accept a token
  uploadFile: (file, recaptchaToken, onUploadProgress) => {
    const formData = new FormData();
    formData.append("file", file);
    // ❗ Append the reCAPTCHA token to the form data
    formData.append("recaptchaToken", recaptchaToken);

    return axios.post(API, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      onUploadProgress, // 👈 progress callback
    });
  },
};