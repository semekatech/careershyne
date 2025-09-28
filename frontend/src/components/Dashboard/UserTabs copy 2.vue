<template>
  <div class="flex gap-6">
    <!-- Main Content (Tabs + Content) ~ 80% -->
    <div class="flex-1">
      <!-- Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
        <nav aria-label="Tabs" class="-mb-px flex space-x-8 overflow-x-auto">
          <a href="#" @click.prevent="activeTab = 'jobs'" :class="tabClass('jobs')">Featured Jobs</a>
          <a href="#" @click.prevent="activeTab = 'cv'" :class="tabClass('cv')">CV Revamp Generator</a>
          <a href="#" @click.prevent="activeTab = 'cover'" :class="tabClass('cover')">Cover Letters Generator</a>
          <a href="#" @click.prevent="activeTab = 'email'" :class="tabClass('email')">Email Templates Generator</a>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm">
        <component
          :is="currentTabComponent"
          @check-eligibility="checkEligibility"
          @revamp-cv="revampCV"
          @generate-cover="generateCoverLetter"
          @generate-email="generateEmailTemplate"
          @view-details="openModal"
        />
      </div>
    </div>

    <!-- Static Card ~ 20% -->
    <div class="w-1/5 bg-white dark:bg-background-dark p-4 rounded-lg shadow-md flex-shrink-0">
      <h3 class="text-lg font-semibold mb-2 text-text-light dark:text-text-dark">Quick Info</h3>
      <p class="text-subtext-light dark:text-subtext-dark text-sm">
        Tips, stats, or any important info goes here.
      </p>
      <ul class="mt-4 space-y-2 text-sm text-subtext-light dark:text-subtext-dark">
        <li>Tip 1: Keep your CV updated</li>
        <li>Tip 2: Check job deadlines</li>
        <li>Tip 3: Customize cover letters</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import FeaturedJobs from "@/components/Dashboard/FeaturedJobs.vue";
import CVRevamp from "@/components/Dashboard/CVRevamp.vue";
import CoverLetters from "@/components/Dashboard/CoverLetters.vue";
import EmailTemplates from "@/components/Dashboard/EmailTemplates.vue";

const activeTab = ref("jobs");

const currentTabComponent = computed(() => {
  switch(activeTab.value) {
    case 'jobs': return FeaturedJobs;
    case 'cv': return CVRevamp;
    case 'cover': return CoverLetters;
    case 'email': return EmailTemplates;
    default: return FeaturedJobs;
  }
});

// Tabs styling
const tabClass = (tab) => [
  "whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm",
  activeTab.value === tab
    ? "border-primary text-primary"
    : "border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600",
];

// Job actions (emit or console)
function checkEligibility(job) { console.log("Check eligibility:", job); }
function revampCV(job) { console.log("Revamp CV:", job); }
function generateCoverLetter(job) { console.log("Generate cover letter:", job); }
function generateEmailTemplate(job) { console.log("Generate email template:", job); }
function openModal(job) { console.log("Open modal:", job); }
</script>
