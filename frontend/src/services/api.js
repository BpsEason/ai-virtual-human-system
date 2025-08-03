import axios from 'axios';
import { useRouter } from 'vue-router';

const api = axios.create({
  baseURL: import.meta.env.VITE_APP_API_URL || 'http://localhost/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  response => response,
  error => {
    // 處理 401 Unauthorized，可能 token 過期或無效
    if (error.response.status === 401) {
      localStorage.removeItem('token');
      // 可以選擇重新導向到登入頁面
      // const router = useRouter();
      // router.push({ name: 'auth' });
      console.log('Token expired or invalid. Redirecting to login...');
    }
    return Promise.reject(error);
  }
);

export default api;
