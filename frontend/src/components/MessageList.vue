<template>
  <div class="space-y-4">
    <div v-for="message in messages" :key="message.id" class="flex" :class="message.sender === 'user' ? 'justify-end' : 'justify-start'">
      <div class="p-3 rounded-lg max-w-sm" :class="message.sender === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200'">
        {{ message.content }}
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  messages: {
    type: Array,
    required: true,
  },
});
</script>
