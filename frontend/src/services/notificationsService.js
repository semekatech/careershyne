import axios from "axios";

const API_URL = "https://demo.ngumzo.com/api/notifications";

export default {
 

  get: () => {
    return axios.get(`${API_URL}/all`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
;
  },
 
};
