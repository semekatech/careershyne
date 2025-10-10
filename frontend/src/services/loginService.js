
import axios from 'axios';
const API = 'https://careershyne.com/auth/';
export default {
  post: (data) => {
    return axios.post(API + 'login', data); 
  }
  
};
