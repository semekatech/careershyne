
import axios from 'axios';
const API = 'https://careershyne.com:8002/api/auth/';
export default {
  post: (data) => {
    return axios.post(API + 'login', data); 
  }
  
};
