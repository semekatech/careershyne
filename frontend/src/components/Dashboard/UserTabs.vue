<template>
  <div>
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
      <nav aria-label="Tabs" class="-mb-px flex space-x-8 overflow-x-auto">
        <a
          href="#"
          @click.prevent="activeTab = 'jobs'"
          :class="tabClass('jobs')"
          >Featured Jobs</a
        >
        <a href="#" @click.prevent="activeTab = 'cv'" :class="tabClass('cv')"
          >CV Revamp Generator</a
        >
        <a
          href="#"
          @click.prevent="activeTab = 'cover'"
          :class="tabClass('cover')"
          >Cover Letters Generator</a
        >
        <a
          href="#"
          @click.prevent="activeTab = 'email'"
          :class="tabClass('email')"
          >Email Templates Generators</a
        >
      </nav>
    </div>

    <div class="grid grid-cols-[75%_23%] gap-8 items-start">
      <!-- Left content -->
      <div
        class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm"
      >
        <component
          :is="currentTabComponent"
          @check-eligibility="checkEligibility"
          @revamp-cv="revampCV"
          @generate-cover="generateCoverLetter"
          @generate-email="generateEmailTemplate"
          @view-details="openModal"
        />
      </div>

      <!-- Right sidebar -->
      <div
        class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm"
      >
        <template v-if="loading">
          <!-- Loader -->
          <div class="flex justify-center items-center py-20">
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
        </template>

        <template v-else>
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
              Subscription Info
            </h2>
            <span class="material-icons text-gray-400 dark:text-gray-500"
              >info</span
            >
          </div>

          <div class="mb-6">
            <div
              class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700"
            >
              <span class="text-gray-600 dark:text-gray-400">Plan:</span>
              <span class="font-semibold text-orange-500">{{
                limits.plan
              }}</span>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="material-icons text-primary mr-3"
                  >edit_document</span
                >
                <span class="text-gray-600 dark:text-gray-300"
                  >CV Revamps:</span
                >
              </div>
              <span class="font-semibold text-lg text-primary">{{
                limits.cv
              }}</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="material-icons text-primary mr-3"
                  >description</span
                >
                <span class="text-gray-600 dark:text-gray-300"
                  >Cover Letters:</span
                >
              </div>
              <span class="font-semibold text-lg text-primary">{{
                limits.coverLetters
              }}</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="material-icons text-primary mr-3">email</span>
                <span class="text-gray-600 dark:text-gray-300"
                  >Email Templates:</span
                >
              </div>
              <span class="font-semibold text-lg text-primary">{{
                limits.emails
              }}</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span class="material-icons text-primary mr-3"
                  >check_circle</span
                >
                <span class="text-gray-600 dark:text-gray-300"
                  >Eligibility:</span
                >
              </div>
              <span class="font-semibold text-lg text-primary">{{
                limits.checks
              }}</span>
            </div>
          </div>

          <div class="mt-8 text-center">
            <button
              class="w-full bg-primary text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary dark:focus:ring-offset-gray-800 transition duration-150 ease-in-out"
            >
              Upgrade Plan
            </button>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import subscriptionService from "@/services/subscriptionService";
import FeaturedJobs from "@/components/Dashboard/FeaturedJobs.vue";
import CVRevamp from "@/components/Dashboard/CVRevamp.vue";
import CoverLetters from "@/components/Dashboard/CoverLetters.vue";
import EmailTemplates from "@/components/Dashboard/EmailTemplates.vue";

const activeTab = ref("jobs");
const loading = ref(true);

const currentTabComponent = computed(() => {
  switch (activeTab.value) {
    case "jobs":
      return FeaturedJobs;
    case "cv":
      return CVRevamp;
    case "cover":
      return CoverLetters;
    case "email":
      return EmailTemplates;
    default:
      return FeaturedJobs;
  }
});

// Tabs styling
const tabClass = (tab) => [
  "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm",
  activeTab.value === tab
    ? "border-primary text-primary"
    : "border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600",
];

// Subscription limits
const limits = ref({
  cv: 0,
  coverLetters: 0,
  emails: 0,
  plan: "Free",
});

async function fetchLimits() {
  loading.value = true;
  try {
    limits.value = await subscriptionService.getLimits();
  } finally {
    loading.value = false;
  }
}

onMounted(fetchLimits);
</script>
