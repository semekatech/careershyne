<template>
  <div>
    <section>
      <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">
        Personalized Jobs
      </h2>

      <!-- Loader -->
      <!-- Loader -->
      <div v-if="loading" class="space-y-4 animate-pulse">
        <!-- Simulated job cards -->
        <div
          v-for="n in 5"
          :key="n"
          class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700"
        >
          <div
            class="flex flex-col sm:flex-row justify-between items-start mb-3"
          >
            <div class="w-full">
              <div
                class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-3"
              ></div>
              <div
                class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/2 mb-2"
              ></div>
              <div class="flex items-center space-x-3">
                <div
                  class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/4"
                ></div>
                <div
                  class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/3"
                ></div>
              </div>
            </div>
            <div
              class="h-9 w-32 bg-gray-300 dark:bg-gray-700 rounded-full mt-4 sm:mt-0"
            ></div>
          </div>
        </div>
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
              class="w-full sm:w-auto mt-3 sm:mt-0 px-4 py-2 bg-primary text-white font-semibold rounded-full shadow-md hover:bg-indigo-700 transition-colors flex items-center justify-center whitespace-nowrap"
              @click="openModal(job)"
            >
              View Details
              <span class="material-icons text-base ml-2">arrow_forward</span>
            </button>
          </div>

          <!-- Action Buttons -->
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
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";

import JobService from "@/services/jobService";

import JobModal from "@/components/Dashboard/modals/JobModal.vue";

const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const selectedJob = ref(null);

const showModal = ref(false);

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const data = await JobService.getPersonalizedJobs();
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

onMounted(fetchJobs);
</script>
