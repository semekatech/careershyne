// UploadService.js
import axios from "axios";

const API = "https://careershyne.com/api/ai/upload";

export default {
  uploadFile: (file, recaptchaToken, onUploadProgress) => {
    const formData = new FormData();
    formData.append("file", file); // ✅ match Laravel's $request->file('file')

    return axios.post(API, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      onUploadProgress,
    });
  },
};
