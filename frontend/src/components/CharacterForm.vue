<template>
  <div class="bg-white p-4 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">新增/編輯虛擬人</h2>
    <form @submit.prevent="submitForm">
      <div class="mb-4">
        <label for="name" class="block text-gray-700">名稱</label>
        <input type="text" id="name" class="w-full px-3 py-2 border rounded" />
      </div>
      <div class="mb-4">
        <label for="description" class="block text-gray-700">描述</label>
        <textarea id="description" class="w-full px-3 py-2 border rounded"></textarea>
      </div>
      <div class="mb-4">
        <label for="persona" class="block text-gray-700">Persona (JSON)</label>
        <textarea id="persona" class="w-full px-3 py-2 border rounded" rows="5"></textarea>
      </div>
      <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
        儲存
      </button>
    </form>
  </div>
</template>

<script setup>
// Form logic here
</script>
