<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">使用者管理</h1>
    <div class="bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">使用者列表</h2>
      <ul>
        <li v-for="user in users" :key="user.id" class="flex items-center justify-between p-2 border-b last:border-b-0">
          <span>{{ user.name }} ({{ user.email }})</span>
          <span class="text-sm text-gray-500">{{ user.role }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const users = ref([
  { id: 1, name: 'Admin', email: 'admin@example.com', role: 'admin' },
  { id: 2, name: 'User One', email: 'user1@example.com', role: 'user' },
  { id: 3, name: 'User Two', email: 'user2@example.com', role: 'user' },
]);
</script>
