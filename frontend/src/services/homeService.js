
import axios from "axios";
const API_URL = "https://demo.ngumzo.com/api/campaign";
export default {
  async getAll() {
    const res = await axios.get(`${API_URL}/fetch-all`);
    return res.data;
  },
};
