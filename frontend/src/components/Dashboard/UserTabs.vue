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
        <!-- <a href="#" @click.prevent="activeTab = 'interviews'" :class="tabClass('interviews')">Interviews</a> -->
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
<div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm">
  <h3 class="text-xl font-semibold mb-4">Subscription Info</h3>

  <p class="text-sm mb-3">
    <span class="font-medium">Plan:</span>
    <span class="text-primary">{{ limits.plan }}</span>
  </p>

  <ul class="space-y-3 text-sm">
    <li class="flex justify-between">
      <span>CV Revamps:</span>
      <span class="font-medium text-primary">{{ limits.cv }}</span>
    </li>
    <li class="flex justify-between">
      <span>Cover Letters:</span>
      <span class="font-medium text-blue-500">{{ limits.coverLetters }}</span>
    </li>
    <li class="flex justify-between">
      <span>Email Templates:</span>
      <span class="font-medium text-purple-500">{{ limits.emails }}</span>
    </li>
  </ul>
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
const currentTabComponent = computed(() => {
  switch (activeTab.value) {
    case "jobs": return FeaturedJobs;
    case "cv": return CVRevamp;
    case "cover": return CoverLetters;
    case "email": return EmailTemplates;
    default: return FeaturedJobs;
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
  limits.value = await subscriptionService.getLimits();
}

onMounted(fetchLimits);
</script>
