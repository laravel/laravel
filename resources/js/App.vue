<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <router-view />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  // Initialize app
});
</script>

<style>
@import 'tailwindcss';
</style>
