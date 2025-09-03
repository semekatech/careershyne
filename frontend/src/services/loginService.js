// LoginService.js
import axios from 'axios';
const API = 'https://demo.ngumzo.com/api/auth/';

export default {
  post: (data) => {
    return axios.post(API + 'login', data); 
  }
  
};
