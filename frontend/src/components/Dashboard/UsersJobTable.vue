<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        @input="onSearch"
        placeholder="Search jobs..."
        class="p-2 border rounded w-full md:w-1/2"
      />
    </div>

    <!-- Loader (initial) -->
    <div v-if="loading && jobs.length === 0" class="flex justify-center items-center py-20">
      <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
    </div>

    <!-- Error -->
    <div v-else-if="error && jobs.length === 0" class="text-center py-10">
      <p class="text-red-600 dark:text-red-400 font-medium mb-4">{{ error }}</p>
      <button @click="reloadAll" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors">
        Retry
      </button>
    </div>

    <!-- Jobs List -->
    <div v-else class="relative">
      <div class="space-y-4">
        <div
          v-for="job in jobs"
          :key="job.id"
          class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700 dark:border-border-dark"
        >
          <div class="flex flex-col sm:flex-row justify-between items-start mb-3">
            <div>
              <h3 class="text-lg font-semibold text-primary mb-1">
                {{ job.title }} <span class="text-sm font-normal">- {{ job.field_name || job.industry || '' }}</span>
              </h3>
              <p class="text-subtext-light dark:text-subtext-dark mb-1">
                {{ job.company }} <span class="mx-1">•</span> {{ job.type || 'Full-time' }}
              </p>

              <div class="flex items-center text-sm text-subtext-light dark:text-subtext-dark space-x-3">
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
          <div class="border-t border-border-light dark:border-border-dark pt-3">
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

      <!-- Loading more indicator -->
      <div v-if="loading && jobs.length > 0" class="flex justify-center py-4">
        <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
      </div>

      <!-- No jobs -->
      <div v-if="!loading && jobs.length === 0 && !error" class="text-center py-10 text-gray-500">
        No jobs found.
      </div>

      <!-- 🔒 Overlay: Access Locked -->
      <div
        v-if="!loading && !limitsLoading && totalLimits <= 0"
        class="absolute left-0 right-0 top-[10%] bottom-0 bg-white/40 dark:bg-black/40 backdrop-blur-[3px] flex flex-col items-center justify-start pt-10 text-center z-20 md:z-30"
      >
        <div class="bg-white/90 dark:bg-gray-800/90 p-8 rounded-2xl shadow-xl max-w-md w-[90%]">
          <div class="flex flex-col items-center space-y-4">
            <span class="material-icons text-6xl text-gray-500">lock</span>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Access Locked</h3>
            <p class="text-gray-600 dark:text-gray-400">Kindly choose a plan to unlock job listings.</p>
            <button @click="goToPlans" class="mt-4 px-6 py-2 bg-primary text-white font-semibold rounded-full shadow hover:bg-indigo-700 transition">
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
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import Swal from "sweetalert2";
import JobService from "@/services/jobService";
import eligibilityService from "@/services/eligibilityService";
import cvRevampService from "@/services/cvRevamp";
import coverLetterService from "@/services/coverLetter";
import emailTemplateService from "@/services/emailTemplate";
import subscriptionService from "@/services/subscriptionService";

import JobModal from "@/components/Dashboard/modals/JobModal.vue";
import EligibilityModal from "@/components/Dashboard/modals/EligibilityModal.vue";
import CvRevampModal from "@/components/Dashboard/modals/CvRevampModal.vue";
import CoverLetterModal from "@/components/Dashboard/modals/CoverModal.vue";
import EmailTemplateModal from "@/components/Dashboard/modals/EmailTemplateModal.vue";

import { useRouter } from "vue-router";
const router = useRouter();

// --- state ---
const jobs = ref([]);
const loading = ref(false);
const error = ref("");
const search = ref("");

const currentPage = ref(0);
const perPage = ref(20);
const lastPage = ref(false);

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

const limits = ref({ plan: null, cv: 0, coverletters: 0, emails: 0, checks: 0 });
const limitsLoading = ref(true);

// computed total limits
const totalLimits = computed(() => {
  const { cv, coverletters, emails, checks } = limits.value || {};
  return (cv || 0) + (coverletters || 0) + (emails || 0) + (checks || 0);
});

// debounce handle
let searchTimeout = null;

// --- helpers ---
function formatDate(dateString) {
  if (!dateString) return "-";
  try {
    return new Date(dateString).toLocaleDateString();
  } catch {
    return dateString;
  }
}

// robust response parser
function parseResults(payload) {
  // payload could be:
  // - an array
  // - { data: [...] }
  // - { data: { data: [...] , meta: {...} } }   (Laravel wrapped)
  // - { items: [...] }
  if (!payload) return { results: [], meta: null };

  let results = [];
  let meta = null;

  if (Array.isArray(payload)) {
    results = payload;
  } else if (Array.isArray(payload.data)) {
    results = payload.data;
  } else if (Array.isArray(payload.data?.data)) {
    results = payload.data.data;
    meta = payload.data.meta || payload.meta || null;
  } else if (Array.isArray(payload.items)) {
    results = payload.items;
    meta = payload.pagination || payload.meta || null;
  } else if (Array.isArray(payload.results)) {
    results = payload.results;
    meta = payload.meta || null;
  } else {
    // fallback: empty
    results = [];
  }

  // if meta not set, try to detect
  if (!meta) {
    meta = payload.meta || payload.pagination || payload.paginator || null;
  }

  return { results, meta };
}

// --- fetch jobs (infinite scroll friendly) ---
async function fetchJobs(page = 1) {
  if (loading.value || lastPage.value) return;
  loading.value = true;
  error.value = "";

  try {
    // call service
    const raw = await JobService.getJobs({
      page,
      per_page: perPage.value,
      search: search.value || undefined,
    });

    // axios usually wraps data
    const payload = raw?.data !== undefined ? raw.data : raw;

    const { results, meta } = parseResults(payload);

    // determine last page using meta if available
    if (meta && meta.current_page !== undefined && meta.last_page !== undefined) {
      if (meta.current_page >= meta.last_page) lastPage.value = true;
    } else {
      // fallback: if returned results length < perPage, assume last page
      if (results.length < perPage.value) lastPage.value = true;
    }

    if (page === 1) {
      jobs.value = results;
    } else {
      // append unique items (prevent duplicates)
      const existingIds = new Set(jobs.value.map((j) => j.id));
      const newItems = results.filter((r) => !existingIds.has(r.id));
      jobs.value.push(...newItems);
    }

    currentPage.value = page;
  } catch (err) {
    // keep user-friendly message
    if (err?.response?.status === 403) {
      error.value = "Access denied. Upgrade your plan to access jobs.";
    } else {
      error.value = err?.message || "Error fetching jobs. Please try again later.";
    }
  } finally {
    loading.value = false;
  }
}

// --- infinite scroll handler ---
function handleScroll() {
  if (loading.value || lastPage.value) return;
  const { scrollTop, clientHeight, scrollHeight } = document.documentElement;
  // when the user is within 200px of bottom, load next page
  if (scrollTop + clientHeight >= scrollHeight - 200) {
    fetchJobs(currentPage.value + 1);
  }
}

// --- search debounce ---
function onSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    lastPage.value = false;
    fetchJobs(1);
  }, 400);
}

// --- reload helper ---
function reloadAll() {
  lastPage.value = false;
  currentPage.value = 0;
  jobs.value = [];
  fetchJobs(1);
  fetchLimits();
}

// --- fetch user limits (for overlay) ---
async function fetchLimits() {
  limitsLoading.value = true;
  try {
    const res = await subscriptionService.getLimits();
    // service may return the object directly or wrapped in data
    limits.value = res?.data ?? res ?? limits.value;
  } catch (err) {
    console.error("Error fetching limits:", err);
  } finally {
    limitsLoading.value = false;
  }
}

// --- modal & action handlers (generic) ---
async function handleAction({ job, serviceFn, modalRef, progressRef, resultRef }) {
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
    // service might return { data: {...} } or { template: '...' }
    const payload = result?.data ?? result;
    resultRef.value = {
      template: payload?.template ?? payload?.result ?? "",
      error: payload?.success === false ? payload?.message : null,
    };
  } catch (err) {
    if (err?.response?.status === 403) {
      resultRef.value = { template: "", error: "Access denied (403). Tokens limit exceeded." };
    } else {
      resultRef.value = { template: "", error: err?.message || "Action failed" };
    }
  } finally {
    clearInterval(interval);
    progressRef.value = 100;
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

// modal open/close helpers
function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}
function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
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

function goToPlans() {
  router.push("/my-plans");
}

// lifecycle
onMounted(async () => {
  await fetchLimits();
  await fetchJobs(1);
  window.addEventListener("scroll", handleScroll, { passive: true });
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<style scoped>
/* small helper to clamp long descriptions if your job.description exists */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
