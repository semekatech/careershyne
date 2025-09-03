import axios from 'axios';

const API = 'https://demo.ngumzo.com/api/auth/';

export default {
  post: (formData) => {
    return axios.post(API + 'register', formData); 
}
};
