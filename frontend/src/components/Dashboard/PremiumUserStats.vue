<template>
  <div class="bg-card-light dark:bg-card-dark   ">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
      <!-- Total Jobs -->
      <div class="flex items-center justify-between bg-primary/10 dark:bg-primary/20 p-4 rounded-lg">
        <div>
          <h3 class="text-sm font-medium text-text-secondary-dark dark:text-text-secondary-light">Total Jobs</h3>
          <p class="text-2xl font-bold text-text-light dark:text-text-dark mt-2">{{ totalJobs }}</p>
        </div>
        <span class="material-icons text-4xl text-primary">work_outline</span>
      </div>

      <!-- Total Applied -->
      <div class="flex items-center justify-between bg-primary/10 dark:bg-primary/20 p-4 rounded-lg">
        <div>
          <h3 class="text-sm font-medium text-text-secondary-dark dark:text-text-secondary-light">Total Applied</h3>
          <p class="text-2xl font-bold text-text-light dark:text-text-dark mt-2">{{ totalApplied }}</p>
        </div>
        <span class="material-icons text-4xl text-primary">check_circle_outline</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import dashboardService from "@/services/dashboardService"; // your API service

const totalJobs = ref(0);
const totalApplied = ref(0);

async function fetchStats() {
  try {
    const { data } = await dashboardService.getUserStats();
    totalJobs.value = data.total_jobs ?? 0;
    totalApplied.value = data.total_applied ?? 0;
  } catch (err) {
    console.error("Failed to fetch dashboard stats:", err);
  }
}

onMounted(() => {
  fetchStats();
});
</script>
