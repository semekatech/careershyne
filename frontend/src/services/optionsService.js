// services/OptionsService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com",
});

export default {
  getIndustries() {
    return api.get("/industries");
  },
  getEducationLevels() {
    return api.get("/education-levels");
  },
  getCounties() {
    return api.get("/counties");
  },
};
