<template>
  <div>
    <section>
      <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">Featured Jobs</h2>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
      </div>

      <!-- Jobs List -->
      <div v-else class="space-y-4">
        <div
          v-for="job in jobs.slice(0, 5)"
          :key="job.id"
          class="bg-card-light dark:bg-card-dark p-4 rounded-lg shadow-md border border-border-light dark:border-border-dark"
        >
          <div class="flex flex-col sm:flex-row justify-between items-start mb-3">
            <div>
              <h3 class="text-lg font-semibold text-primary mb-1">{{ job.title }} - {{ job.county }}, {{ job.country }}</h3>
              <p class="text-subtext-light dark:text-subtext-dark mb-1">{{ job.company }} - {{ job.type }}</p>
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

            <a class="mt-3 sm:mt-0 px-4 py-2 bg-primary text-white font-semibold rounded-full shadow-md hover:bg-indigo-700 transition-colors flex items-center whitespace-nowrap" href="#">
              View Details
              <span class="material-icons text-base ml-2">arrow_forward</span>
            </a>
          </div>

          <div class="border-t border-border-light dark:border-border-dark pt-3">
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2">
              <button @click="$emit('check-eligibility', job)" class="flex items-center justify-center py-2 px-3 border border-green-500 text-green-500 font-semibold rounded-lg hover:bg-green-50 dark:hover:bg-green-900 transition-colors text-center">
                <span class="material-icons text-lg mr-1">check_circle</span>
                Check Eligibility
              </button>
              <button @click="$emit('revamp-cv', job)" class="flex items-center justify-center py-2 px-3 border border-blue-500 text-blue-500 font-semibold rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors text-center">
                <span class="material-icons text-lg mr-1">description</span>
                CV Revamp
              </button>
              <button @click="$emit('generate-cover', job)" class="flex items-center justify-center py-2 px-3 border border-yellow-500 text-yellow-500 font-semibold rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900 transition-colors text-center">
                <span class="material-icons text-lg mr-1">mail</span>
                Cover Letter
              </button>
              <button @click="$emit('generate-email', job)" class="flex items-center justify-center py-2 px-3 border border-purple-500 text-purple-500 font-semibold rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900 transition-colors text-center">
                <span class="material-icons text-lg mr-1">drafts</span>
                Email Template
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import JobService from "@/services/jobService";

const jobs = ref([]);
const loading = ref(true);

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

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

onMounted(fetchJobs);
</script>
