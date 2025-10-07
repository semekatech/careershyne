<template>
  <div id="user-tabs" class="">
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-4 sm:mb-6">
      <nav aria-label="Tabs" class="-mb-px flex flex-wrap gap-3 sm:gap-4">
        <a
          href="#"
          @click.prevent="activeTab = 'jobs'"
          :class="tabClass('jobs')"
        >
          Featured Jobs
        </a>
        <a href="#" @click.prevent="activeTab = 'cv'" :class="tabClass('cv')">
          CV Revamp
        </a>
        <a
          href="#"
          @click.prevent="activeTab = 'cover'"
          :class="tabClass('cover')"
        >
          Cover Letters
        </a>
        <a
          href="#"
          @click.prevent="activeTab = 'email'"
          :class="tabClass('email')"
        >
          Email Templates
        </a>
      </nav>
    </div>

    <div
      class="grid grid-cols-1 lg:grid-cols-[3fr_1fr] gap-4 sm:gap-6 items-start"
    >
      <!-- Left content -->
      <div
        class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm"
      >
        <component
          :is="currentTabComponent"
          @check-eligibility="
            performAction(eligibilityService.checkEligibility, 'eligibility')
          "
          @revamp-cv="performAction(cvRevampService.revamp, 'cv')"
          @generate-cover="
            performAction(coverLetterService.generate, 'coverLetters')
          "
          @generate-email="
            performAction(emailTemplateService.generate, 'emails')
          "
        />
      </div>

      <!-- Right sidebar -->
      <div
        class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm"
      >
        <template v-if="loading">
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
          <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
            Subscription Info
          </h2>

          <div class="space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-gray-600 dark:text-gray-400">Plan:</span>
              <span class="font-semibold text-orange-500">{{
                limits.plan
              }}</span>
            </div>

            <div class="flex justify-between items-center">
              <span class="text-gray-600 dark:text-gray-400">CV Revamps:</span>
              <span class="font-semibold text-primary">{{ limits.cv }}</span>
            </div>

            <div class="flex justify-between items-center">
              <span class="text-gray-600 dark:text-gray-400"
                >Cover Letters:</span
              >
              <span class="font-semibold text-primary">{{
                limits.coverletters
              }}</span>
            </div>

            <div class="flex justify-between items-center">
              <span class="text-gray-600 dark:text-gray-400"
                >Email Templates:</span
              >
              <span class="font-semibold text-primary">{{
                limits.emails
              }}</span>
            </div>

            <div class="flex justify-between items-center">
              <span class="text-gray-600 dark:text-gray-400">Eligibility:</span>
              <span class="font-semibold text-primary">{{
                limits.checks
              }}</span>
            </div>
          </div>

          <!-- Button section -->
          <div class="mt-8">
            <router-link
              to="/my-plans"
              class="block w-full text-center border border-orange-500 text-black font-bold py-3 px-6 rounded-lg hover:bg-orange-500 hover:text-white transition duration-150 ease-in-out"
            >
              Purchase More Credits
            </router-link>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import Swal from "sweetalert2";

import subscriptionService from "@/services/subscriptionService";
import eligibilityService from "@/services/eligibilityService";
import cvRevampService from "@/services/cvRevamp";
import coverLetterService from "@/services/coverLetter";
import emailTemplateService from "@/services/emailTemplate";

import FeaturedJobs from "@/components/Dashboard/FeaturedJobs.vue";
import CVRevamp from "@/components/Dashboard/CVRevamp.vue";
import CoverLetters from "@/components/Dashboard/CoverLetters.vue";
import EmailTemplates from "@/components/Dashboard/EmailTemplates.vue";

const activeTab = ref("jobs");
const loading = ref(true);
const limits = ref({
  cv: 0,
  coverLetters: 0,
  emails: 0,
  checks: 0,
  plan: "Free",
});

// Determine current tab component
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

// Fetch subscription limits
async function fetchLimits() {
  loading.value = true;
  try {
    limits.value = await subscriptionService.getLimits();
  } finally {
    loading.value = false;
  }
}

// Generic action handler with subscription refresh
async function performAction(serviceFn, type) {
  const { isConfirmed } = await Swal.fire({
    title: "Are You Ready?",
    text: `Do you want to proceed with ${type}?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, proceed",
    cancelButtonText: "No, cancel",
  });
  if (!isConfirmed) return;

  try {
    const response = await serviceFn();
    if (response.success) {
      Swal.fire("Success", `${type} completed successfully!`, "success");
      await fetchLimits();
    }
  } catch (err) {
    if (err.response?.status === 403) {
      Swal.fire(
        "Limit reached",
        err.response.data.message || `You have reached your ${type} limit.`,
        "error"
      );
    } else {
      Swal.fire(
        "Error",
        `Failed to perform ${type}. Please try again.`,
        "error"
      );
    }
  }
}

onMounted(fetchLimits);
</script>
