import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  },
});

export default api;