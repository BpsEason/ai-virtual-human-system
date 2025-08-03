<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">AI 虛擬人管理</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <CharacterForm />
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">虛擬人列表</h2>
        <div v-for="character in characters" :key="character.id" class="p-3 border-b last:border-b-0">
          <h3 class="font-bold">{{ character.name }}</h3>
          <p class="text-sm text-gray-600">{{ character.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import CharacterForm from '../components/CharacterForm.vue';

const characters = ref([
  { id: 1, name: 'AI 銷售員', description: '專注於銷售產品的 AI 虛擬人。' },
  { id: 2, name: '技術客服', description: '提供技術支援和疑難排解。' },
]);
</script>
