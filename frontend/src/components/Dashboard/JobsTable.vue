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

    <!-- Tabs -->
    <div class="inline-flex items-center gap-3 mb-4">
      <button
        @click="activeTab = 'active'"
        :class="[
          'px-4 py-2 rounded-full text-sm font-semibold transition',
          activeTab === 'active'
            ? 'bg-indigo-600 text-white shadow-md'
            : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-indigo-600 hover:text-white',
        ]"
      >
        Active
      </button>
      <button
        @click="activeTab = 'expired'"
        :class="[
          'px-4 py-2 rounded-full text-sm font-semibold transition',
          activeTab === 'expired'
            ? 'bg-indigo-600 text-white shadow-md'
            : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-indigo-600 hover:text-white',
        ]"
      >
        Expired
      </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        placeholder="Search jobs..."
        class="p-2 border rounded w-full md:w-1/2"
      />
    </div>

    <!-- Loader -->
    <div v-if="loading" class="space-y-4">
      <div
        v-for="n in 5"
        :key="n"
        class="animate-pulse bg-gray-200 dark:bg-gray-700 rounded-2xl p-4"
      >
        <div class="h-6 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-2"></div>
        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2 mb-2"></div>
        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/3"></div>
      </div>
    </div>

    <!-- Jobs List -->
    <div v-else class="space-y-4">
      <div
        v-for="job in paginatedJobs"
        :key="job.id"
        class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col md:flex-row md:justify-between md:items-center"
      >
        <!-- Job Info -->
        <div>
          <h3 class="text-lg font-semibold text-blue-600">{{ job.title }}</h3>
          <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
          <p class="text-gray-500 text-sm">
            Location: {{ job.county }}, {{ job.country }} | Deadline:
            {{ formatDate(job.deadline) }}
          </p>
          Status:
          <span
            :class="[
              'px-3 py-1 rounded text-xs font-semibold',
              isActive(job.deadline)
                ? 'bg-green-100 text-green-800'
                : 'bg-red-100 text-red-800',
            ]"
          >
            {{ isActive(job.deadline) ? "Active" : "Expired" }}
          </span>
        </div>

        <!-- View Details Button -->
        <div class="mt-2 md:mt-0 flex-shrink-0">
          <button
            @click="openModal(job)"
            class="px-4 py-2 bg-white rounded transition border border-[#fd624e] text-black"
          >
            View Details
          </button>
        </div>
      </div>

      <p
        v-if="paginatedJobs.length === 0"
        class="text-center text-gray-500 mt-4"
      >
        No {{ activeTab }} jobs found.
      </p>
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
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
      @click="closeModal"
    >
      <div
        class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col"
        @click.stop
      >
        <!-- Header -->
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

        <!-- Content -->
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
const loading = ref(true);
const activeTab = ref("active"); // active or expired

const showModal = ref(false);
const selectedJob = ref({});

// Fetch jobs
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

const filteredJobs = computed(() => {
  const filtered = jobs.value.filter((job) => {
    const active = isActive(job.deadline);
    return activeTab.value === "active" ? active : !active;
  });

  if (!search.value) return filtered;

  return filtered.filter((job) =>
    [job.title, job.company, job.county, job.country].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

function isActive(deadline) {
  const jobDate = new Date(deadline);
  const today = new Date();

  // Set both to start of the day
  jobDate.setHours(0, 0, 0, 0);
  today.setHours(0, 0, 0, 0);

  return jobDate >= today;
}

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
