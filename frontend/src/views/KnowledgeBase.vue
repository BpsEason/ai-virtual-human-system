<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">知識庫管理</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <DocumentUploader />
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">已上傳文件</h2>
        <ul class="space-y-2">
          <li v-for="doc in documents" :key="doc.id" class="flex items-center justify-between p-2 border rounded">
            <span>{{ doc.file_name }}</span>
            <span class="text-sm text-gray-500">{{ doc.status }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import DocumentUploader from '../components/DocumentUploader.vue';

const documents = ref([
  { id: 1, file_name: 'product_manual.pdf', status: 'processed' },
  { id: 2, file_name: 'faq.docx', status: 'uploaded' },
]);
</script>
