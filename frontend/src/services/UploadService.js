// UploadService.js
import axios from "axios";

const API = "https://careershyne.com/api/ai/upload/";

export default {
  uploadFile: (file, onUploadProgress) => {
    const formData = new FormData();
    formData.append("file", file);

    return axios.post(API, formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
      onUploadProgress, // 👈 progress callback
    });
  },
};
