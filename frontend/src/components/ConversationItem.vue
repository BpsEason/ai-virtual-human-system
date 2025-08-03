<template>
  <div class="bg-white p-4 rounded shadow flex items-center justify-between cursor-pointer hover:bg-gray-50">
    <div>
      <h3 class="font-bold text-lg">{{ conversation.character.name }}</h3>
      <p class="text-sm text-gray-600">{{ conversation.last_message }}</p>
    </div>
    <div class="text-sm text-gray-400">
      {{ conversation.updated_at || '剛剛' }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  conversation: {
    type: Object,
    required: true,
  },
});
</script>
