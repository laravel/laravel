<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-800">SENSUS - Security</h1>
          </div>
          <div class="flex items-center space-x-4">
            <span class="text-gray-600">{{ authStore.user?.name }}</span>
            <button
              @click="handleLogout"
              class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">System Status</h3>
            <div class="flex items-center">
              <span
                :class="systemState?.disaster_mode ? 'bg-red-500' : 'bg-green-500'"
                class="w-3 h-3 rounded-full mr-2"
              ></span>
              <span class="text-gray-600">
                {{ systemState?.disaster_mode ? 'DISASTER MODE' : 'NORMAL' }}
              </span>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Students on Campus</h3>
            <p class="text-3xl font-bold text-blue-600">{{ students.length }}</p>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">RFID Scans Today</h3>
            <p class="text-3xl font-bold text-green-600">{{ rfidLogs.length }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">RFID Scanner</h2>
            <form @submit.prevent="handleRfidScan">
              <div class="space-y-4">
                <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">RFID Tag</label>
                  <input
                    v-model="rfidTag"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Scan RFID tag"
                    required
                  />
                </div>
                <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">Action</label>
                  <select
                    v-model="rfidAction"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                  >
                    <option value="enter">Enter</option>
                    <option value="exit">Exit</option>
                  </select>
                </div>
                <button
                  type="submit"
                  class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700"
                >
                  Record Scan
                </button>
              </div>
            </form>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent RFID Logs</h2>
            <div v-if="loading" class="text-gray-600">Loading...</div>
            <div v-else-if="rfidLogs.length === 0" class="text-gray-600">No recent scans</div>
            <div v-else class="space-y-2 max-h-64 overflow-y-auto">
              <div
                v-for="log in rfidLogs"
                :key="log.id"
                class="border-b pb-2"
              >
                <p class="font-semibold">{{ log.student?.first_name }} {{ log.student?.last_name }}</p>
                <p class="text-sm text-gray-600">{{ log.action.toUpperCase() }} - {{ log.location }}</p>
                <p class="text-xs text-gray-500">{{ new Date(log.timestamp).toLocaleString() }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Node Status</h2>
          <div v-if="loading" class="text-gray-600">Loading...</div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="node in nodes"
              :key="node.id"
              class="border rounded-lg p-4"
              :class="node.status === 'online' ? 'border-green-300 bg-green-50' : 'border-red-300 bg-red-50'"
            >
              <h4 class="font-semibold text-gray-700">{{ node.name }}</h4>
              <p class="text-sm text-gray-600">Zone: {{ node.zone?.name || 'N/A' }}</p>
              <p class="text-sm font-bold" :class="node.status === 'online' ? 'text-green-600' : 'text-red-600'">
                {{ node.status.toUpperCase() }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const router = useRouter();
const authStore = useAuthStore();

const nodes = ref([]);
const students = ref([]);
const rfidLogs = ref([]);
const systemState = ref(null);
const loading = ref(true);

const rfidTag = ref('');
const rfidAction = ref('enter');

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

const fetchData = async () => {
  try {
    const [nodesRes, studentsRes, logsRes, stateRes] = await Promise.all([
      api.get('/nodes'),
      api.get('/students'),
      api.get('/rfid-logs'),
      api.get('/system-state'),
    ]);
    nodes.value = nodesRes.data;
    students.value = studentsRes.data;
    rfidLogs.value = logsRes.data;
    systemState.value = stateRes.data;
  } catch (error) {
    console.error('Failed to fetch data:', error);
  } finally {
    loading.value = false;
  }
};

const handleRfidScan = async () => {
  try {
    await api.post('/hardware/rfid-scan', {
      rfid_tag: rfidTag.value,
      action: rfidAction.value,
      location: 'Main Entrance',
    });
    rfidTag.value = '';
    await fetchData();
  } catch (error) {
    console.error('Failed to record RFID scan:', error);
    alert('Failed to record scan. Student not found.');
  }
};

onMounted(() => {
  authStore.initialize();
  fetchData();
});
</script>
