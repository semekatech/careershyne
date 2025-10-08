<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold mb-2 md:mb-0">Manage Jobs</h2>
      <router-link
        to="/add-job"
        class="py-2 px-6 text-white rounded font-medium transition duration-500 hover:opacity-90"
        :style="{ background: '#fd624e' }"
      >
        <font-awesome-icon :icon="['fas', 'plus']" class="mr-2" /> Add Job
      </router-link>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        placeholder="Search jobs..."
        class="p-2 border rounded w-full md:w-1/2"
      />
    </div>

    <!-- Jobs List -->
    <div class="space-y-4">
      <div
        v-for="job in paginatedJobs"
        :key="job.id"
        class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700 dark:border-border-dark"
      >
        <div>
          <h3 class="text-lg font-semibold text-blue-600">{{ job.title }}</h3>
          <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
          <p class="text-gray-500 text-sm">
            Location: {{ job.county }}, {{ job.country }} | Deadline:
            {{ formatDate(job.deadline) }}
          </p>
           Status:  <span
            :class="[
              'px-3 py-1 rounded text-xs font-semibold',
              new Date(job.deadline) >= new Date()
                ? 'bg-green-100 text-green-800'
                : 'bg-red-100 text-red-800',
            ]"
          >
            {{ new Date(job.deadline) >= new Date() ? "Active" : "Expired" }}
          </span>
        </div>
        <div class="flex items-center space-x-2">
        
          <button
            @click="openModal(job)"
            class="ml-2 px-4 py-2 bg-white text-white rounded  transition "
          style="border: 1px solid #fd624e;color:black" >
            View Details
          </button>
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

    <!-- Full Screen Modal -->
    <!-- Full Screen Modal -->
   <!-- Full Screen Modal -->
<div
  v-if="showModal"
  class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
  @click="closeModal"  
>
  <div
    class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col"
    @click.stop  
  >
    <!-- Header (Sticky) -->
    <div
      class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10"
    >
      <h2 class="text-2xl font-semibold">{{ selectedJob.title }}</h2>
      <button
        @click="closeModal"
        class="text-gray-700 hover:text-gray-900 text-2xl font-bold"
      >
        ✕
      </button>
    </div>

    <!-- Content (Scrollable & full width) -->
    <div class="p-4 md:p-6 overflow-x-hidden overflow-y-auto flex-1 w-full">
      <p class="text-gray-600 mb-2">
        {{ selectedJob.company }} - {{ selectedJob.type }}
      </p>
      <p class="text-gray-500 mb-2">
        Location: {{ selectedJob.county }}, {{ selectedJob.country }}
      </p>
      <p class="text-gray-500 mb-2">
        Deadline: {{ formatDate(selectedJob.deadline) }}
      </p>
      <p class="text-gray-700 mb-2">
        <strong>Experience:</strong> {{ selectedJob.experience }} years
      </p>
      <p class="text-gray-700 mb-2">
        <strong>Education:</strong> {{ selectedJob.education }}
      </p>
      <p class="text-gray-700 mb-2">
        <strong>Salary:</strong> {{ selectedJob.salary }}
      </p>
      <p class="text-gray-700 mb-2">
        <strong>Field:</strong> {{ selectedJob.field_name }}
      </p>
      <p class="text-gray-700 mb-2"><strong>Description:</strong></p>
      <div
        v-html="selectedJob.description"
        class="prose mb-4 max-w-full"
      ></div>
      <p class="text-gray-700 mb-2">
        <strong>Application Instructions:</strong>
      </p>
      <div
        v-html="selectedJob.applicationInstructions"
        class="prose max-w-full"
      ></div>
    </div>
  </div>
</div>

  </div>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import JobService from "@/services/jobService";

const jobs = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

const showModal = ref(false);
const selectedJob = ref({});

// Fetch jobs
async function fetchJobs() {
  try {
    const data = await JobService.getJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
    currentPage.value = data.current_page || 1;
  } catch (err) {
    console.error("Error fetching jobs:", err);
    jobs.value = [];
  }
}

// Filtered & Paginated
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

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// Modal
function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}
function closeModal() {
  showModal.value = false;
}

// Date formatting
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

onMounted(fetchJobs);
</script>
