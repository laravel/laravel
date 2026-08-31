<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">SENSUS</h1>
        <p class="text-gray-600">Disaster Monitoring System</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
            Email
          </label>
          <input
            v-model="email"
            type="email"
            id="email"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your email"
            required
          />
        </div>

        <div class="mb-6">
          <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
            Password
          </label>
          <input
            v-model="password"
            type="password"
            id="password"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Enter your password"
            required
          />
        </div>

        <div v-if="error" class="mb-4 text-red-500 text-sm text-center">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
        >
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-600">
        <p>Demo accounts:</p>
        <p>admin@sensus.local / password</p>
        <p>security@sensus.local / password</p>
        <p>responder@sensus.local / password</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  const success = await authStore.login(email.value, password.value);

  if (success) {
    router.push('/dashboard');
  } else {
    error.value = 'Invalid credentials';
  }

  loading.value = false;
};
</script>
