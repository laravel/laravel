<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-800">SENSUS - Responder</h1>
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
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Active Disaster Events</h3>
            <p class="text-3xl font-bold" :class="activeEvents > 0 ? 'text-red-600' : 'text-green-600'">
              {{ activeEvents }}
            </p>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Active Disaster Events</h2>
          <div v-if="loading" class="text-gray-600">Loading...</div>
          <div v-else-if="disasterEvents.length === 0" class="text-gray-600">No active disaster events</div>
          <div v-else class="space-y-4">
            <div
              v-for="event in disasterEvents"
              :key="event.id"
              class="border rounded-lg p-4"
              :class="event.type === 'fire' ? 'border-orange-300 bg-orange-50' : 'border-yellow-300 bg-yellow-50'"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h4 class="font-semibold text-gray-800 uppercase">{{ event.type }}</h4>
                  <p class="text-sm text-gray-600">Severity: {{ event.severity }}</p>
                  <p class="text-sm text-gray-600">Location: {{ event.location }}</p>
                  <p class="text-sm text-gray-600">Started: {{ new Date(event.started_at).toLocaleString() }}</p>
                </div>
                <button
                  v-if="event.status === 'active'"
                  @click="resolveEvent(event.id)"
                  class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
                >
                  Resolve
                </button>
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
              :class="node.status === 'online' ? 'border-green-300' : 'border-gray-300'"
            >
              <h4 class="font-semibold text-gray-700">{{ node.name }}</h4>
              <p class="text-sm text-gray-600">Zone: {{ node.zone?.name || 'N/A' }}</p>
              <p class="text-sm" :class="node.status === 'online' ? 'text-green-600' : 'text-red-600'">
                Status: {{ node.status.toUpperCase() }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const router = useRouter();
const authStore = useAuthStore();

const nodes = ref([]);
const disasterEvents = ref([]);
const systemState = ref(null);
const loading = ref(true);

const activeEvents = computed(() => disasterEvents.value.filter(e => e.status === 'active').length);

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

const fetchData = async () => {
  try {
    const [nodesRes, eventsRes, stateRes] = await Promise.all([
      api.get('/nodes'),
      api.get('/disaster-events'),
      api.get('/system-state'),
    ]);
    nodes.value = nodesRes.data;
    disasterEvents.value = eventsRes.data;
    systemState.value = stateRes.data;
  } catch (error) {
    console.error('Failed to fetch data:', error);
  } finally {
    loading.value = false;
  }
};

const resolveEvent = async (id) => {
  if (confirm('Are you sure you want to resolve this disaster event?')) {
    try {
      await api.put(`/disaster-events/${id}`, { status: 'resolved' });
      await fetchData();
    } catch (error) {
      console.error('Failed to resolve event:', error);
    }
  }
};

onMounted(() => {
  authStore.initialize();
  fetchData();
});
</script>
