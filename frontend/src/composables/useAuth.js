import { ref, onMounted } from 'vue';
import api from '../services/api';

const user = ref(null);
const isLoading = ref(false);

export function useAuth() {

  const fetchUser = async () => {
    isLoading.value = true;
    try {
      const response = await api.get('/me');
      user.value = response.data.data;
    } catch (error) {
      user.value = null;
      localStorage.removeItem('token');
      console.error('Failed to fetch user:', error);
    } finally {
      isLoading.value = false;
    }
  };

  const checkAuth = () => {
    const token = localStorage.getItem('token');
    if (token && !user.value) {
      fetchUser();
    }
  };

  onMounted(() => {
    checkAuth();
  });

  const isAuthenticated = () => !!user.value;
  const isAdmin = () => user.value && user.value.role === 'admin';

  return {
    user,
    isLoading,
    isAuthenticated,
    isAdmin,
    fetchUser,
    checkAuth,
  };
}
