<template>
  <div>
    <section>
      <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">
        Featured Jobs
      </h2>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg
          class="animate-spin h-8 w-8 text-primary"
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
        <p class="text-red-600 dark:text-red-400 font-medium mb-4">
          {{ error }}
        </p>
        <button
          @click="fetchJobs"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Retry
        </button>
      </div>

      <!-- Jobs List -->
      <div v-else class="space-y-4">
        <div
          v-for="job in jobs.slice(0, 5)"
          :key="job.id"
          class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700 dark:border-border-dark"
        >
          <div
            class="flex flex-col sm:flex-row justify-between items-start mb-3"
          >
            <div>
              <h3 class="text-lg font-semibold text-primary mb-1">
                {{ job.title }} - {{ job.county }}, {{ job.country }}
              </h3>
              <p class="text-subtext-light dark:text-subtext-dark mb-1">
                {{ job.company }} - {{ job.type }}
              </p>
              <div
                class="flex items-center text-sm text-subtext-light dark:text-subtext-dark space-x-3"
              >
                <div class="flex items-center">
                  <span class="material-icons text-base mr-1">location_on</span>
                  <span>{{ job.county }}, {{ job.country }}</span>
                </div>
                <div class="flex items-center">
                  <span class="material-icons text-base mr-1">event</span>
                  <span>Deadline: {{ formatDate(job.deadline) }}</span>
                </div>
              </div>
            </div>

            <!-- OPEN DETAILS MODAL -->
            <button
              class="mt-3 sm:mt-0 px-4 py-2 bg-primary text-white font-semibold rounded-full shadow-md hover:bg-indigo-700 transition-colors flex items-center whitespace-nowrap"
              @click="openModal(job)"
            >
              View Details
              <span class="material-icons text-base ml-2">arrow_forward</span>
            </button>
          </div>

          <!-- Action Buttons -->
          <div
            class="border-t border-border-light dark:border-border-dark pt-3"
          >
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2">
              <button
                @click="openEligibility(job)"
                class="flex items-center justify-center py-2 px-3 border border-green-500 text-green-500 font-semibold rounded-lg hover:bg-green-50 dark:hover:bg-green-900 transition-colors text-center"
              >
                <span class="material-icons text-lg mr-1">check_circle</span>
                Check Eligibility
              </button>
              <button
                @click="openCvRevamp(job)"
                class="flex items-center justify-center py-2 px-3 border border-blue-500 text-blue-500 font-semibold rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors text-center"
              >
                <span class="material-icons text-lg mr-1">description</span>
                CV Revamp
              </button>
              <button
                @click="openCoverLetter(job)"
                class="flex items-center justify-center py-2 px-3 border border-yellow-500 text-yellow-500 font-semibold rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900 transition-colors text-center"
              >
                <span class="material-icons text-lg mr-1">mail</span>
                Cover Letter
              </button>
              <button
                @click="openEmailTemplate(job)"
                class="flex items-center justify-center py-2 px-3 border border-purple-500 text-purple-500 font-semibold rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900 transition-colors text-center"
              >
                <span class="material-icons text-lg mr-1">drafts</span>
                Email Template
              </button>
            </div>
          </div>
        </div>
        <div class="flex justify-center mt-6">
          <router-link
            to="/browse-jobs"
            class="px-6 py-2 bg-primary text-white font-semibold rounded-full hover:bg-indigo-700 transition-colors"
          >
            View More Jobs
          </router-link>
        </div>
      </div>
    </section>

    <!-- MODALS -->
    <JobModal v-if="showModal" :job="selectedJob" @close="closeModal" />
    <EligibilityModal
      v-if="showEligibilityModal"
      :job="selectedJob"
      :result="eligibilityResult"
      :progress="eligibilityProgress"
      @close="closeEligibility"
    />
    <CvRevampModal
      v-if="showCvRevampModal"
      :job="selectedJob"
      :result="cvRevampResult"
      :progress="cvRevampProgress"
      @close="closeCvRevamp"
    />
    <CoverLetterModal
      v-if="showCoverLetterModal"
      :job="selectedJob"
      :result="coverLetterResult"
      :progress="coverLetterProgress"
      @close="closeCoverLetter"
    />
    <EmailTemplateModal
      v-if="showEmailTemplateModal"
      :job="selectedJob"
      :result="emailTemplateResult"
      :progress="emailTemplateProgress"
      @close="closeEmailTemplate"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";

import JobService from "@/services/jobService";
import eligibilityService from "@/services/eligibilityService";
import coverLetterService from "@/services/coverLetter";
import cvRevampService from "@/services/cvRevamp";
import emailTemplateService from "@/services/emailTemplate";

import JobModal from "@/components/Dashboard/modals/JobModal.vue";
import EligibilityModal from "@/components/Dashboard/modals/EligibilityModal.vue";
import CoverLetterModal from "@/components/Dashboard/modals/CoverModal.vue";
import CvRevampModal from "@/components/Dashboard/modals/CvRevampModal.vue";
import EmailTemplateModal from "@/components/Dashboard/modals/EmailTemplateModal.vue";

const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const selectedJob = ref(null);

const showModal = ref(false);
const showEligibilityModal = ref(false);
const showCvRevampModal = ref(false);
const showCoverLetterModal = ref(false);
const showEmailTemplateModal = ref(false);

const eligibilityProgress = ref(0);
const eligibilityResult = ref(null);
const cvRevampProgress = ref(0);
const cvRevampResult = ref(null);
const coverLetterProgress = ref(0);
const coverLetterResult = ref(null);
const emailTemplateProgress = ref(0);
const emailTemplateResult = ref(null);

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const data = await JobService.getJobs();
    if (Array.isArray(data.data)) jobs.value = data.data;
    else error.value = "No jobs found.";
  } catch (err) {
    error.value =
      err.response?.status === 403
        ? "Access denied. Upgrade your plan to access jobs."
        : "Error fetching jobs. Please try again later.";
  } finally {
    loading.value = false;
  }
}

// Modal handlers
function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}
function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
}

// Generic async action handler with 403 checks
async function handleAction({
  job,
  serviceFn,
  modalRef,
  progressRef,
  resultRef,
}) {
  const { isConfirmed } = await Swal.fire({
    title: "Ready?",
    text: `Ready to proceed for ${job.title}?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, proceed",
    cancelButtonText: "No, cancel",
  });
  if (!isConfirmed) return;

  selectedJob.value = job;
  modalRef.value = true;
  progressRef.value = 0;
  resultRef.value = null;

  const interval = setInterval(() => {
    if (progressRef.value < 90) progressRef.value += 10;
  }, 400);

  try {
    const result = await serviceFn(job.id);
    clearInterval(interval);
    progressRef.value = 100;
    resultRef.value = result;
  } catch (err) {
    clearInterval(interval);
    progressRef.value = 100;
    if (err.response?.status === 403)
      resultRef.value = {
        error: err.response.data.message || "Limit reached for this action.",
      };
    else resultRef.value = { error: "Action failed. Please try again later." };
  }
}

// Action wrappers
function openEligibility(job) {
  handleAction({
    job,
    serviceFn: eligibilityService.checkEligibility,
    modalRef: showEligibilityModal,
    progressRef: eligibilityProgress,
    resultRef: eligibilityResult,
  });
}
function openCvRevamp(job) {
  handleAction({
    job,
    serviceFn: cvRevampService.revamp,
    modalRef: showCvRevampModal,
    progressRef: cvRevampProgress,
    resultRef: cvRevampResult,
  });
}
function openCoverLetter(job) {
  handleAction({
    job,
    serviceFn: coverLetterService.generate,
    modalRef: showCoverLetterModal,
    progressRef: coverLetterProgress,
    resultRef: coverLetterResult,
  });
}
function openEmailTemplate(job) {
  handleAction({
    job,
    serviceFn: emailTemplateService.generate,
    modalRef: showEmailTemplateModal,
    progressRef: emailTemplateProgress,
    resultRef: emailTemplateResult,
  });
}

function closeEligibility() {
  showEligibilityModal.value = false;
  eligibilityProgress.value = 0;
  eligibilityResult.value = null;
}
function closeCvRevamp() {
  showCvRevampModal.value = false;
  cvRevampProgress.value = 0;
  cvRevampResult.value = null;
}
function closeCoverLetter() {
  showCoverLetterModal.value = false;
  coverLetterProgress.value = 0;
  coverLetterResult.value = null;
}
function closeEmailTemplate() {
  showEmailTemplateModal.value = false;
  emailTemplateProgress.value = 0;
  emailTemplateResult.value = null;
}

onMounted(fetchJobs);
</script>
