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

      <!-- Jobs List -->
      <div v-else class="space-y-4">
        <div
          v-for="job in jobs.slice(0, 5)"
          :key="job.id"
          class="bg-card-light dark:bg-card-dark p-4 rounded-lg shadow-md border border-border-light dark:border-border-dark"
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
import JobService from "@/services/jobService";
import eligibilityService from "@/services/eligibilityService";
import coverLetterService from "@/services/coverLetter";
import cvRevampService from "@/services/cvRevamp";
import emailTemplateService from "@/services/emailTemplate";

import JobModal from "@/components/Dashboard/modals/JobModal.vue";
import EligibilityModal from "@/components/Dashboard/modals/EligibilityModal.vue";
import CvRevampModal from "@/components/Dashboard/modals/CvRevampModal.vue";
import CoverLetterModal from "@/components/Dashboard/modals/CoverLetterModal.vue";
import EmailTemplateModal from "@/components/Dashboard/modals/EmailTemplateModal.vue";
import Swal from "sweetalert2";

const jobs = ref([]);
const loading = ref(true);
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
  try {
    const data = await JobService.getJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    console.error("Error fetching jobs:", err);
    jobs.value = [];
  } finally {
    loading.value = false;
  }
}

function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}
function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
}

async function openEligibility(job) {
  const { isConfirmed } = await Swal.fire({
    title: "Ready?",
    text: "Ready to check eligibility for this job?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, check eligibility",
    cancelButtonText: "No, cancel",
  });

  if (!isConfirmed) return;
  selectedJob.value = job;
  showEligibilityModal.value = true;
  eligibilityProgress.value = 0;
  eligibilityResult.value = null;

  const interval = setInterval(() => {
    if (eligibilityProgress.value < 90) eligibilityProgress.value += 10;
  }, 400);

  try {
    const result = await eligibilityService.checkEligibility(job.id);
    clearInterval(interval);
    eligibilityProgress.value = 100;
    eligibilityResult.value = result;
  } catch {
    clearInterval(interval);
    eligibilityProgress.value = 100;
    eligibilityResult.value = { error: "Failed to check eligibility." };
  }
}

function closeEligibility() {
  showEligibilityModal.value = false;
  eligibilityProgress.value = 0;
  eligibilityResult.value = null;
}

async function openCvRevamp(job) {
  const { isConfirmed } = await Swal.fire({
    title: "Ready?",
    text: "Ready to generate a revamped CV for this job?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, check eligibility",
    cancelButtonText: "No, cancel",
  });

  if (!isConfirmed) return;
  selectedJob.value = job;
  showCvRevampModal.value = true;
  cvRevampProgress.value = 0;
  cvRevampResult.value = null;

  const interval = setInterval(() => {
    if (cvRevampProgress.value < 90) cvRevampProgress.value += 10;
  }, 400);
  try {
    const result = await cvRevampService.revamp(job.id);
    clearInterval(interval);
    cvRevampProgress.value = 100;
    cvRevampResult.value = result;
  } catch {
    clearInterval(interval);
    cvRevampProgress.value = 100;
    cvRevampResult.value = { error: "Failed to revamp CV." };
  }
}

function closeCvRevamp() {
  showCvRevampModal.value = false;
  cvRevampProgress.value = 0;
  cvRevampResult.value = null;
}

async function openCoverLetter(job) {
   const { isConfirmed } = await Swal.fire({
    title: "Ready?",
    text: "Ready to generate a cover letter for this job?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, check eligibility",
    cancelButtonText: "No, cancel",
  });

  if (!isConfirmed) return;
  selectedJob.value = job;
  showCoverLetterModal.value = true;
  coverLetterProgress.value = 0;
  coverLetterResult.value = null;

  const interval = setInterval(() => {
    if (coverLetterProgress.value < 90) coverLetterProgress.value += 10;
  }, 400);
  try {
    const result = await coverLetterService.generate(job.id);
    clearInterval(interval);
    coverLetterProgress.value = 100;
    coverLetterResult.value = result;
  } catch {
    clearInterval(interval);
    coverLetterProgress.value = 100;
    coverLetterResult.value = { error: "Failed to generate cover letter." };
  }
}

function closeCoverLetter() {
  showCoverLetterModal.value = false;
  coverLetterProgress.value = 0;
  coverLetterResult.value = null;
}

async function openEmailTemplate(job) {
   const { isConfirmed } = await Swal.fire({
    title: "Ready?",
    text: "Ready to generate an email template for this job?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, check eligibility",
    cancelButtonText: "No, cancel",
  });

  if (!isConfirmed) return;
  selectedJob.value = job;
  showEmailTemplateModal.value = true;
  emailTemplateProgress.value = 0;
  emailTemplateResult.value = null;

  const interval = setInterval(() => {
    if (emailTemplateProgress.value < 90) emailTemplateProgress.value += 10;
  }, 400);
  try {
    const result = await emailTemplateService.generate(job.id);
    clearInterval(interval);
    emailTemplateProgress.value = 100;
    emailTemplateResult.value = result;
  } catch {
    clearInterval(interval);
    emailTemplateProgress.value = 100;
    emailTemplateResult.value = { error: "Failed to generate email template." };
  }
}

function closeEmailTemplate() {
  showEmailTemplateModal.value = false;
  emailTemplateProgress.value = 0;
  emailTemplateResult.value = null;
}

onMounted(fetchJobs);
</script>
