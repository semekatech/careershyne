<template>
  <div class="bg-card-light dark:bg-card-dark">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Show shimmer skeletons while loading -->
      <template v-if="loading">
        <div
          v-for="n in 3"
          :key="n"
          class="flex items-center justify-between bg-gray-200 dark:bg-gray-800 p-4 rounded-lg animate-pulse"
        >
          <div class="space-y-2">
            <div class="h-4 w-24 bg-gray-300 dark:bg-gray-700 rounded-md"></div>
            <div class="h-8 w-16 bg-gray-300 dark:bg-gray-700 rounded-md"></div>
          </div>
          <div class="h-10 w-10 bg-gray-300 dark:bg-gray-700 rounded-full"></div>
        </div>
      </template>

      <!-- Actual content -->
      <template v-else>
        <!-- Featured Jobs -->
        <div
          class="flex items-center justify-between bg-primary/10 dark:bg-primary/20 p-4 rounded-lg transition-all duration-300"
        >
          <div>
            <h3
              class="text-sm font-medium text-text-secondary-dark dark:text-text-secondary-light"
            >
              Featured Jobs
            </h3>
            <p
              class="text-2xl font-bold text-text-light dark:text-text-dark mt-2"
            >
              {{ totalJobs }}
            </p>
          </div>
          <span class="material-icons text-4xl text-primary">work_outline</span>
        </div>

        <!-- Saved Jobs -->
        <div
          class="flex items-center justify-between bg-primary/10 dark:bg-primary/20 p-4 rounded-lg transition-all duration-300"
        >
          <div>
            <h3
              class="text-sm font-medium text-text-secondary-dark dark:text-text-secondary-light"
            >
              Saved Jobs
            </h3>
            <p
              class="text-2xl font-bold text-text-light dark:text-text-dark mt-2"
            >
              {{ totalSaved }}
            </p>
          </div>
          <span class="material-icons text-4xl text-primary">
            bookmark_border
          </span>
        </div>

        <!-- Applied Jobs -->
        <div
          class="flex items-center justify-between bg-primary/10 dark:bg-primary/20 p-4 rounded-lg transition-all duration-300"
        >
          <div>
            <h3
              class="text-sm font-medium text-text-secondary-dark dark:text-text-secondary-light"
            >
              Applied Jobs
            </h3>
            <p
              class="text-2xl font-bold text-text-light dark:text-text-dark mt-2"
            >
              {{ totalApplied }}
            </p>
          </div>
          <span class="material-icons text-4xl text-primary">
            check_circle_outline
          </span>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import dashboardService from "@/services/dashboardService";

const totalJobs = ref(0);
const totalApplied = ref(0);
const totalSaved = ref(0);
const loading = ref(true);

async function fetchStats() {
  try {
    const stats = await dashboardService.getUserStats();
    totalJobs.value = stats.total_jobs ?? 0;
    totalApplied.value = stats.total_applied ?? 0;
    totalSaved.value = stats.total_saved ?? 0;
  } catch (err) {
    console.error("Failed to fetch dashboard stats:", err);
  } finally {
    loading.value = false;
  }
}

onMounted(fetchStats);
</script>
