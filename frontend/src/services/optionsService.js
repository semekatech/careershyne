// services/OptionsService.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://careershyne.com/api",
});

export default {
  getIndustries() {
    return api.get("/options/industries");
  },
  getEducationLevels() {
    return api.get("/options/education-levels");
  },
  getCounties() {
    return api.get("/options/counties");
  },
};
