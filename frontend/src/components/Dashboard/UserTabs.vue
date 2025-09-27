<template>
  <div>
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
      <nav aria-label="Tabs" class="-mb-px flex space-x-8 overflow-x-auto">
        <a href="#" @click.prevent="activeTab = 'jobs'" :class="tabClass('jobs')">Featured Jobs</a>
        <a href="#" @click.prevent="activeTab = 'cv'" :class="tabClass('cv')">CV Revamp</a>
        <a href="#" @click.prevent="activeTab = 'cover'" :class="tabClass('cover')">Cover Letters</a>
        <a href="#" @click.prevent="activeTab = 'email'" :class="tabClass('email')">Email Templates</a>
        <!-- <a href="#" @click.prevent="activeTab = 'interviews'" :class="tabClass('interviews')">Interviews</a> -->
      </nav>
    </div>

    <!-- Tab Content Full Width -->
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
</template>

<script setup>
import { ref, computed } from "vue";
import FeaturedJobs from "@/components/Dashboard/FeaturedJobs.vue";
import CVRevamp from "@/components/Dashboard/CVRevamp.vue";
import CoverLetters from "@/components/Dashboard/CoverLetters.vue";
import EmailTemplates from "@/components/Dashboard/EmailTemplates.vue";
// import Interviews from "@/components/Interviews.vue";

const activeTab = ref("jobs");

const currentTabComponent = computed(() => {
  switch(activeTab.value) {
    case 'jobs': return FeaturedJobs;
    case 'cv': return CVRevamp;
    case 'cover': return CoverLetters;
    case 'email': return EmailTemplates;
    // case 'interviews': return Interviews;
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

// Job actions
function checkEligibility(job) { console.log("Check eligibility:", job); }
function revampCV(job) { console.log("Revamp CV:", job); }
function generateCoverLetter(job) { console.log("Generate cover letter:", job); }
function generateEmailTemplate(job) { console.log("Generate email template:", job); }
function openModal(job) { console.log("Open modal:", job); }
</script>
