
import axios from 'axios';
const API = 'https://careershyne.com/api/api/auth/';
export default {
  post: (data) => {
    return axios.post(API + 'login', data); 
  }
  
};
