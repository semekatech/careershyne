<template>
  <div>
    <section>
      <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">
        Applied Jobs
      </h2>

      <!-- Loader -->
      <div v-if="loading" class="space-y-4 animate-pulse">
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
      <div v-else>
        <!-- No Jobs -->
        <div
          v-if="!jobs.length"
          class="text-center py-10 bg-card-light dark:bg-card-dark border border-gray-200 dark:border-gray-700 rounded-2xl"
        >
          <p class="text-lg font-medium text-gray-600 dark:text-gray-300 mb-3">
            You haven’t applied for any jobs yet.
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Browse available jobs and start applying today!
          </p>
        </div>

        <!-- Job Cards -->
        <div v-else class="space-y-4">
          <div
            v-for="job in jobs.slice(0, 100)"
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
                    <span class="material-icons text-base mr-1">event</span>
                    <span>Applied On: {{ formatDate(job.applied_on) }}</span>
                  </div>
                </div>
              </div>

              <!-- Buttons -->
              <div class="flex flex-col sm:flex-row gap-2 mt-3 sm:mt-0">
                <button
                  class="px-4 py-2 bg-primary text-white font-semibold rounded-full shadow-md hover:bg-indigo-700 transition-colors flex items-center justify-center whitespace-nowrap"
                  @click="openModal(job)"
                >
                  View Details
                  <span class="material-icons text-base ml-2"
                    >arrow_forward</span
                  >
                </button>

                <button
                  disabled
                  class="px-4 py-2 font-semibold rounded-full shadow-md flex items-center justify-center whitespace-nowrap transition-colors bg-green-500 text-white"
                >
                  <span class="material-icons text-base mr-2">check_circle</span>
                  Applied
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Application Details Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm"
    >
      <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-6 relative"
      >
        <!-- Close button -->
        <button
          @click="closeModal"
          class="absolute top-3 right-3 text-gray-500 hover:text-red-500"
        >
          <span class="material-icons text-2xl">close</span>
        </button>

        <!-- Job Header -->
        <div class="mb-4">
          <h3 class="text-xl font-bold text-primary mb-1">
            {{ selectedJob.title }}
          </h3>
          <p class="text-gray-600 dark:text-gray-300">
            {{ selectedJob.company }} — {{ selectedJob.county }},
            {{ selectedJob.country }}
          </p>
          <p class="text-sm text-gray-500 mt-1">
            Applied on: {{ formatDate(selectedJob.applied_on) }}
          </p>
        </div>

        <hr class="my-4 border-gray-200 dark:border-gray-700" />

        <!-- Application Content -->
        <div class="space-y-3">
          <div v-if="selectedJob.subject">
            <h4 class="font-semibold text-gray-800 dark:text-gray-200">
              Subject:
            </h4>
            <p class="text-gray-700 dark:text-gray-300">
              {{ selectedJob.subject }}
            </p>
          </div>

          <div v-if="selectedJob.body">
            <h4 class="font-semibold text-gray-800 dark:text-gray-200">
              Application Body:
            </h4>
            <p
              class="text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed"
            >
              {{ selectedJob.body }}
            </p>
          </div>

          <div v-if="selectedJob.application_cv">
            <h4 class="font-semibold text-gray-800 dark:text-gray-200">
              Attached CV:
            </h4>
            <a
              :href="selectedJob.application_cv"
              target="_blank"
              class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              <span class="material-icons text-base mr-1">description</span>
              View CV
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import JobService from "@/services/jobService";

const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const selectedJob = ref(null);
const showModal = ref(false);

function formatDate(dateString) {
  if (!dateString) return "N/A";
  return new Date(dateString).toLocaleDateString();
}

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const data = await JobService.getAppliedJobs();
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

onMounted(fetchJobs);
</script>
