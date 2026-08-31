<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-800">SENSUS - Admin</h1>
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">System Configuration</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Disaster Mode</label>
                <button
                  @click="toggleDisasterMode"
                  :class="systemState?.disaster_mode ? 'bg-red-600' : 'bg-green-600'"
                  class="text-white px-4 py-2 rounded-md hover:opacity-90"
                >
                  {{ systemState?.disaster_mode ? 'Disable Disaster Mode' : 'Enable Disaster Mode' }}
                </button>
              </div>
              <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Occupancy Check</label>
                <button
                  @click="toggleOccupancyCheck"
                  :class="systemState?.check_occupancy ? 'bg-blue-600' : 'bg-gray-600'"
                  class="text-white px-4 py-2 rounded-md hover:opacity-90"
                >
                  {{ systemState?.check_occupancy ? 'Disable Occupancy Check' : 'Enable Occupancy Check' }}
                </button>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add New Node</h2>
            <form @submit.prevent="addNode">
              <div class="space-y-4">
                <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">Node Name</label>
                  <input
                    v-model="newNode.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                    required
                  />
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Location X</label>
                    <input
                      v-model="newNode.location_x"
                      type="number"
                      step="0.000001"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md"
                      required
                    />
                  </div>
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Location Y</label>
                    <input
                      v-model="newNode.location_y"
                      type="number"
                      step="0.000001"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md"
                      required
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-gray-700 text-sm font-bold mb-2">Zone</label>
                  <select
                    v-model="newNode.zone_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                  >
                    <option value="">Select Zone</option>
                    <option v-for="zone in zones" :key="zone.id" :value="zone.id">
                      {{ zone.name }}
                    </option>
                  </select>
                </div>
                <button
                  type="submit"
                  class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700"
                >
                  Add Node
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Manage Nodes</h2>
          <div v-if="loading" class="text-gray-600">Loading...</div>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="node in nodes" :key="node.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ node.name }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ node.zone?.name || 'N/A' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="node.status === 'online' ? 'text-green-600' : 'text-red-600'"
                    >
                      {{ node.status.toUpperCase() }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button
                      @click="deleteNode(node.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
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
const zones = ref([]);
const systemState = ref(null);
const loading = ref(true);

const newNode = ref({
  name: '',
  location_x: 0,
  location_y: 0,
  zone_id: '',
});

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

const fetchData = async () => {
  try {
    const [nodesRes, zonesRes, stateRes] = await Promise.all([
      api.get('/nodes'),
      api.get('/zones'),
      api.get('/system-state'),
    ]);
    nodes.value = nodesRes.data;
    zones.value = zonesRes.data;
    systemState.value = stateRes.data;
  } catch (error) {
    console.error('Failed to fetch data:', error);
  } finally {
    loading.value = false;
  }
};

const toggleDisasterMode = async () => {
  try {
    await api.put('/system-state', {
      disaster_mode: !systemState.value.disaster_mode,
      check_occupancy: systemState.value.check_occupancy,
    });
    await fetchData();
  } catch (error) {
    console.error('Failed to toggle disaster mode:', error);
  }
};

const toggleOccupancyCheck = async () => {
  try {
    await api.put('/system-state', {
      disaster_mode: systemState.value.disaster_mode,
      check_occupancy: !systemState.value.check_occupancy,
    });
    await fetchData();
  } catch (error) {
    console.error('Failed to toggle occupancy check:', error);
  }
};

const addNode = async () => {
  try {
    await api.post('/nodes', newNode.value);
    newNode.value = { name: '', location_x: 0, location_y: 0, zone_id: '' };
    await fetchData();
  } catch (error) {
    console.error('Failed to add node:', error);
  }
};

const deleteNode = async (id) => {
  if (confirm('Are you sure you want to delete this node?')) {
    try {
      await api.delete(`/nodes/${id}`);
      await fetchData();
    } catch (error) {
      console.error('Failed to delete node:', error);
    }
  }
};

onMounted(() => {
  authStore.initialize();
  fetchData();
});
</script>
