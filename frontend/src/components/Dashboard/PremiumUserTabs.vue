<template>
  <div id="user-tabs" class="">
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-4 sm:mb-6">
      <nav aria-label="Tabs" class="-mb-px flex flex-wrap gap-3 sm:gap-4 justify-left">
        <a
          href="#"
          @click.prevent="activeTab = 'jobs'"
          :class="tabClass('jobs')"
        >
          Personalized Jobs
        </a>
        <a href="#" @click.prevent="activeTab = 'saved'" :class="tabClass('saved')">
          Saved Jobs
        </a>
        <a href="#" @click.prevent="activeTab = 'all'" :class="tabClass('all')">
          All Jobs
        </a>
      </nav>
    </div>

    <!-- Full-width content -->
    <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm">
      <component :is="currentTabComponent" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

// Import new components
import PersonalizedJobs from "@/components/Dashboard/PersonalizedJobs.vue";
import SavedJobs from "@/components/Dashboard/SavedJobs.vue";
import AllJobs from "@/components/Dashboard/AllJobs.vue";

const activeTab = ref("jobs");

// Determine current tab component
const currentTabComponent = computed(() => {
  switch (activeTab.value) {
    case "jobs":
      return PersonalizedJobs;
    case "saved":
      return SavedJobs;
    case "all":
      return AllJobs;
    default:
      return PersonalizedJobs;
  }
});

// Tabs styling
const tabClass = (tab) => [
  "whitespace-nowrap py-3 sm:py-4 px-2 sm:px-4 border-b-2 font-medium text-sm sm:text-base",
  activeTab.value === tab
    ? "border-primary text-primary"
    : "border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600",
];
</script>
