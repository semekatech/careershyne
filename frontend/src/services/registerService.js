import axios from 'axios';

const API = 'https://careershyne.com/api/api/auth/';

export default {
  post: (formData) => {
    return axios.post(API + 'register', formData); 
}
};
