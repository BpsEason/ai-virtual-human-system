<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">{{ isLogin ? '登入' : '註冊' }}</h2>
      <form @submit.prevent="submitForm">
        <div v-if="!isLogin" class="mb-4">
          <label for="name" class="block text-gray-700">姓名</label>
          <input type="text" id="name" v-model="form.name" class="w-full px-3 py-2 border rounded mt-1" required />
        </div>
        <div class="mb-4">
          <label for="email" class="block text-gray-700">電子郵件</label>
          <input type="email" id="email" v-model="form.email" class="w-full px-3 py-2 border rounded mt-1" required />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-gray-700">密碼</label>
          <input type="password" id="password" v-model="form.password" class="w-full px-3 py-2 border rounded mt-1" required />
        </div>
        <div v-if="!isLogin" class="mb-4">
          <label for="password_confirmation" class="block text-gray-700">確認密碼</label>
          <input type="password" id="password_confirmation" v-model="form.password_confirmation" class="w-full px-3 py-2 border rounded mt-1" required />
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
          {{ isLogin ? '登入' : '註冊' }}
        </button>
      </form>
      <div class="mt-4 text-center">
        <button @click="isLogin = !isLogin" class="text-blue-500 hover:underline">
          {{ isLogin ? '還沒有帳號？點此註冊' : '已有帳號？點此登入' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../services/api';
import { useRouter } from 'vue-router';

const isLogin = ref(true);
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});
const router = useRouter();

const submitForm = async () => {
  try {
    const endpoint = isLogin.value ? '/login' : '/register';
    const response = await api.post(endpoint, form.value);
    localStorage.setItem('token', response.data.data.token);
    router.push({ name: 'dashboard' });
  } catch (error) {
    console.error('Auth failed:', error.response.data);
    alert(error.response.data.message);
  }
};
</script>
