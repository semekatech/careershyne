<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <!-- Loader -->
    <div v-if="loading" class="flex justify-center items-center py-20">
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

    <!-- Error -->
    <div v-else-if="error" class="text-center py-10">
      <p class="text-red-600 dark:text-red-400 font-medium mb-4">{{ error }}</p>
      <button
        @click="fetchJobs"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors"
      >
        Retry
      </button>
    </div>

    <!-- Jobs List -->
    <div v-else class="relative">
      <!-- Search -->
      <div class="mb-4">
        <input
          v-model="search"
          placeholder="Search jobs..."
          class="p-2 border rounded w-full md:w-1/2"
        />
      </div>

      <!-- Jobs -->
      <div class="space-y-4">
        <div
          v-for="job in paginatedJobs"
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

            <!-- View Details Button -->
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
                Eligibility
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

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <button
          @click="prevPage"
          :disabled="currentPage <= 1"
          class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
        >
          Previous
        </button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          @click="nextPage"
          :disabled="currentPage >= totalPages"
          class="px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>

      <!-- 🔒 Overlay: Always shown after jobs load -->
      <div
        v-if="!loading && !limitsLoading && totalLimits <= 0"
        class="absolute left-0 right-0 top-[10%] bottom-0 bg-white/40 dark:bg-black/40 backdrop-blur-[3px]
 flex flex-col items-center justify-start pt-10 text-center z-20 md:z-30"
      >
        <div
          class="bg-white/90 dark:bg-gray-800/90 p-8 rounded-2xl shadow-xl max-w-md w-[90%]"
        >
          <div class="flex flex-col items-center space-y-4">
            <span class="material-icons text-6xl text-gray-500">lock</span>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
              Access Locked
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
              Kindly choose a plan to unlock job listings.
            </p>
            <button
              @click="goToPlans"
              class="mt-4 px-6 py-2 bg-primary text-white font-semibold rounded-full shadow hover:bg-indigo-700 transition"
            >
              Choose Plan
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
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
import { ref, computed, onMounted } from "vue";
import Swal from "sweetalert2";

import JobService from "@/services/jobService";
import eligibilityService from "@/services/eligibilityService";
import cvRevampService from "@/services/cvRevamp";
import coverLetterService from "@/services/coverLetter";
import emailTemplateService from "@/services/emailTemplate";

import JobModal from "@/components/Dashboard/modals/JobModal.vue";
import EligibilityModal from "@/components/Dashboard/modals/EligibilityModal.vue";
import CvRevampModal from "@/components/Dashboard/modals/CvRevampModal.vue";
import CoverLetterModal from "@/components/Dashboard/modals/CoverModal.vue";
import EmailTemplateModal from "@/components/Dashboard/modals/EmailTemplateModal.vue";
import subscriptionService from "@/services/subscriptionService";
import { useRouter } from "vue-router";
const router = useRouter();

// --- State ---
const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

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

// --- Fetch Jobs ---
async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const data = await JobService.getJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    error.value =
      err.response?.status === 403
        ? "Access denied. Upgrade your plan to access jobs."
        : "Error fetching jobs. Please try again later.";
  } finally {
    loading.value = false;
  }
}
const limits = ref({
  plan: null,
  cv: 0,
  coverletters: 0,
  emails: 0,
  checks: 0,
});

// ✅ Computed total of all limits
const totalLimits = computed(() => {
  const { cv, coverletters, emails, checks } = limits.value;
  return (cv || 0) + (coverletters || 0) + (emails || 0) + (checks || 0);
});
// --- Computed: Filter + Pagination ---
const filteredJobs = computed(() => {
  if (!search.value) return jobs.value;
  return jobs.value.filter((job) =>
    [job.title, job.company, job.county, job.country].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

const paginatedJobs = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredJobs.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredJobs.value.length / perPage.value)
);

// --- Pagination ---
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// --- Modals & Actions ---
function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}
function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
}

async function handleAction({
  job,
  serviceFn,
  modalRef,
  progressRef,
  resultRef,
}) {
  const { isConfirmed } = await Swal.fire({
    title: "Ready?",
    text: `Proceed with action for "${job.title}"?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, proceed",
    cancelButtonText: "Cancel",
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
    resultRef.value = {
      template: result.data?.template || "",
      error: result.success === false ? result.message : null,
    };
  } catch (err) {
    if (err.response?.status === 403) {
      resultRef.value = {
        template: "",
        error: "Access denied (403). Tokens limit exceeded.",
      };
    } else {
      resultRef.value = { template: "", error: err.message || "Action failed" };
    }
  }
}

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

// --- Helper ---
function formatDate(dateString) {
  if (!dateString) return "-";
  return new Date(dateString).toLocaleDateString();
}
const limitsLoading = ref(true);

async function fetchLimits() {
  try {
    const data = await subscriptionService.getLimits();
    limits.value = data || limits.value;
  } catch (error) {
    console.error("Error fetching limits:", error);
  } finally {
    limitsLoading.value = false;
  }
}
function goToPlans() {
  router.push("/my-plans");
}
// --- Lifecycle ---
onMounted(async () => {
  await fetchJobs();
  await fetchLimits();
});
</script>
