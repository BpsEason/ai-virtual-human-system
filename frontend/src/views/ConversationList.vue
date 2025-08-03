<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">我的對話列表</h1>
    <div class="space-y-4">
      <ConversationItem v-for="convo in conversations" :key="convo.id" :conversation="convo" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ConversationItem from '../components/ConversationItem.vue';

const conversations = ref([
  { id: 1, character: { name: 'AI 銷售員' }, last_message: '你好，我可以為你介紹什麼產品嗎？' },
  { id: 2, character: { name: '技術客服' }, last_message: '好的，請提供你的訂單編號。' },
]);
</script>
