import axios from 'axios';

const API = 'https://careershyne.com/api/auth/';

export default {
  post: (formData) => {
    return axios.post(API + 'register', formData); 
}
};
