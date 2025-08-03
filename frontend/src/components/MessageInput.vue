<template>
  <form @submit.prevent="handleSend" class="flex">
    <input
      type="text"
      v-model="inputContent"
      placeholder="輸入訊息..."
      class="flex-grow px-4 py-2 border rounded-l-lg focus:outline-none"
    />
    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-r-lg hover:bg-blue-600">
      發送
    </button>
  </form>
</template>

<script setup>
import { ref } from 'vue';

const inputContent = ref('');
const emit = defineEmits(['sendMessage']);

const handleSend = () => {
  if (inputContent.value.trim()) {
    emit('sendMessage', inputContent.value);
    inputContent.value = '';
  }
};
</script>
