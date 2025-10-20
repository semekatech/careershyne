<template>
  <div>
    <section>
      <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">
        Saved Jobs
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

      <!-- Jobs Section -->
      <div v-else>
        <!-- No Saved Jobs -->
        <div
          v-if="!jobs.length"
          class="text-center py-10 bg-card-light dark:bg-card-dark border border-gray-200 dark:border-gray-700 rounded-2xl"
        >
          <p class="text-lg font-medium text-gray-600 dark:text-gray-300 mb-3">
            You haven’t saved any jobs yet.
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Browse available jobs and mark the ones you’re interested in!
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
                    <span class="material-icons text-base mr-1">location_on</span>
                    <span>{{ job.county }}, {{ job.country }}</span>
                  </div>
                  <div class="flex items-center">
                    <span class="material-icons text-base mr-1">event</span>
                    <span>Deadline: {{ formatDate(job.deadline) }}</span>
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
                  <span class="material-icons text-base ml-2">arrow_forward</span>
                </button>

                <button
                  :disabled="job.save_status === 'saved'"
                  :class="[
                    'px-4 py-2 font-semibold rounded-full shadow-md flex items-center justify-center whitespace-nowrap transition-colors',
                    job.save_status === 'saved'
                      ? 'bg-gray-400 text-white cursor-not-allowed'
                      : 'bg-green-500 text-white hover:bg-green-600',
                  ]"
                  @click="markInterested(job)"
                >
                  <span class="material-icons text-base mr-2">
                    {{ job.save_status === 'saved' ? 'star' : 'star_border' }}
                  </span>
                  {{ job.save_status === 'saved' ? 'Saved' : 'Interested' }}
                </button>
              </div>
            </div>
          </div>
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
    const data = await JobService.getSavedJobs();
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

async function markInterested(job) {
  const confirm = await Swal.fire({
    title: "Mark as Interested?",
    text: `Do you want to save "${job.title}" to your interested jobs?`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, save it",
    cancelButtonText: "Cancel",
    confirmButtonColor: "#16a34a",
    cancelButtonColor: "#6b7280",
  });

  if (!confirm.isConfirmed) return;

  try {
    const res = await JobService.markInterested(job.id);
    job.save_status = "saved";
    Swal.fire({
      icon: "success",
      title: "Marked as Interested!",
      text:
        res.data?.message ||
        `${job.title} has been saved to your interested jobs.`,
      timer: 2000,
      showConfirmButton: false,
    });
  } catch (err) {
    if (err.response?.status === 403) {
      Swal.fire({
        icon: "warning",
        title: "Job Already Marked",
        text: "You have already marked this job.",
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Something went wrong",
        text: "Unable to mark this job as interested. Please try again.",
      });
    }
  }
}

onMounted(fetchJobs);
</script>
