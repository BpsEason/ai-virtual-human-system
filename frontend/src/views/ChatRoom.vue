<template>
  <div class="flex flex-col h-screen p-6">
    <h1 class="text-3xl font-bold mb-6">與 {{ characterName }} 聊天</h1>
    <MessageList :messages="messages" class="flex-grow overflow-y-auto mb-4" />
    <MessageInput @send-message="handleSendMessage" />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import MessageList from '../components/MessageList.vue';
import MessageInput from '../components/MessageInput.vue';

const route = useRoute();
const messages = ref([
  { id: 1, sender: 'ai', content: '哈囉！我是 AI 銷售員，請問有什麼能為您服務的嗎？' },
]);
const characterName = ref('AI 銷售員'); // 應從 API 取得

const handleSendMessage = (content) => {
  messages.value.push({
    id: messages.value.length + 1,
    sender: 'user',
    content: content
  });
  // TODO: 這裡要呼叫 chat.js 中的 sendMessage API
  // 模擬 AI 回覆
  setTimeout(() => {
    messages.value.push({
      id: messages.value.length + 1,
      sender: 'ai',
      content: `好的，我收到你的訊息：「${content}」。`
    });
  }, 1000);
};

onMounted(() => {
  const conversationId = route.params.conversationId;
  console.log('當前對話 ID:', conversationId);
  // TODO: 這裡應呼叫 chat.js 中的 getMessages API
});
</script>
