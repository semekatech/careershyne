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
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8v8H4z"
        ></path>
      </svg>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="text-center py-10">
      <p class="text-red-600 dark:text-red-400 font-medium mb-4">{{ error }}</p>
      <button
        @click="fetchActivities"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors"
      >
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
          class="p-2 border border-gray-300 rounded w-full md:w-1/2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
        />
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">#</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Action</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Status</th>
              <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-300">Date</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="(activity, index) in paginatedActivities" :key="activity.id">
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ activity.message }}</td>
              <td class="px-4 py-2">
                <span
                  :class="statusBadge(activity.status)"
                  class="px-2 py-1 text-xs font-semibold rounded-full"
                >
                  {{ activity.status }}
                </span>
              </td>
              <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ formatDate(activity.created_at) }}</td>
            </tr>
            <tr v-if="paginatedActivities.length === 0">
              <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                No activities found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <button
          @click="prevPage"
          :disabled="currentPage <= 1"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
          Previous
        </button>
        <span class="text-gray-700 dark:text-gray-300">Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          @click="nextPage"
          :disabled="currentPage >= totalPages"
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded disabled:opacity-50 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import UsageActivityService from "@/services/usageActivityService";

const activities = ref([]);
const loading = ref(true);
const error = ref("");
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

async function fetchActivities() {
  loading.value = true;
  error.value = "";
  try {
    const response = await UsageActivityService.getActivities();
    activities.value = Array.isArray(response.data.data) ? response.data.data : [];
  } catch (err) {
    console.error(err);
    error.value = "Error fetching activities. Please try again later.";
  } finally {
    loading.value = false;
  }
}

const filteredActivities = computed(() => {
  if (!search.value) return activities.value;
  return activities.value.filter((a) =>
    [a.action, a.status, a.message].some((f) =>
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

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function formatDate(dateString) {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleString();
}

// --- Status Badge Styling ---
function statusBadge(status) {
  switch (status?.toLowerCase()) {
    case "success":
      return "bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100";
    case "pending":
      return "bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100";
    case "failed":
      return "bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100";
    default:
      return "bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100";
  }
}

onMounted(fetchActivities);
</script>
