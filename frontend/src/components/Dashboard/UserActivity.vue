<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <!-- Loader -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <svg
        class="animate-spin h-10 w-10 text-primary"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="text-center py-10">
      <p class="text-red-600 dark:text-red-400 font-medium mb-4">{{ error }}</p>
      <button @click="fetchActivities" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors">
        Retry
      </button>
    </div>

    <!-- Usage Activities List -->
    <div v-else>
      <!-- Search -->
      <div class="mb-4">
        <input
          v-model="search"
          placeholder="Search activities..."
          class="p-2 border rounded w-full md:w-1/2"
        />
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full border border-border-light dark:border-border-dark rounded-lg">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-2 text-left">Action</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Message</th>
              <th class="px-4 py-2 text-left">Tokens Used</th>
              <th class="px-4 py-2 text-left">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="activity in paginatedActivities"
              :key="activity.id"
              class="border-t border-border-light dark:border-border-dark"
            >
              <td class="px-4 py-2">{{ activity.action }}</td>
              <td class="px-4 py-2 capitalize">{{ activity.status }}</td>
              <td class="px-4 py-2">{{ activity.message }}</td>
              <td class="px-4 py-2">{{ activity.tokens_used }}</td>
              <td class="px-4 py-2">{{ formatDate(activity.created_at) }}</td>
            </tr>
            <tr v-if="paginatedActivities.length === 0">
              <td colspan="5" class="px-4 py-6 text-center text-gray-500">No activities found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <button @click="prevPage" :disabled="currentPage <= 1" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
          Previous
        </button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage >= totalPages" class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50">
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import UsageActivityService from "@/services/usageActivityService"; // create this

// --- State ---
const activities = ref([]);
const loading = ref(true);
const error = ref("");
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

// --- Fetch Activities ---
async function fetchActivities() {
  loading.value = true;
  error.value = "";
  try {
    const data = await UsageActivityService.getActivities();
    activities.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    error.value = "Error fetching activities. Please try again later.";
  } finally {
    loading.value = false;
  }
}

// --- Computed: Filter + Pagination ---
const filteredActivities = computed(() => {
  if (!search.value) return activities.value;
  return activities.value.filter(a =>
    [a.action, a.status, a.message].some(f =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

const paginatedActivities = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredActivities.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredActivities.value.length / perPage.value)
);

// --- Pagination ---
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// --- Helper ---
function formatDate(dateString) {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleString();
}

// --- Lifecycle ---
onMounted(fetchActivities);
</script>
