<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-4">Manage Jobs</h2>

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
        class="border rounded p-4 hover:shadow transition flex justify-between items-center"
      >
        <div>
          <h3 class="text-lg font-semibold text-blue-600">{{ job.title }}</h3>
          <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
          <p class="text-gray-500 text-sm">
            Location: {{ job.county }}, {{ job.country }} | Deadline: {{ formatDate(job.deadline) }}
          </p>
        </div>
        <div>
          <span
            :class="[
              'px-3 py-1 rounded text-xs font-semibold',
              job.status === 'active'
                ? 'bg-green-100 text-green-800'
                : 'bg-red-100 text-red-800',
            ]"
          >
            {{ job.status || 'active' }}
          </span>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import JobService from "@/services/jobService";

const jobs = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

// Fetch jobs
async function fetchJobs() {
  try {
    const data = await JobService.getJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
    totalPages.value = data.last_page || 1;
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
    [job.title, job.company, job.county, job.country]
      .some(f => f?.toLowerCase().includes(search.value.toLowerCase()))
  );
});

const paginatedJobs = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredJobs.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredJobs.value.length / perPage.value)
);

// Pagination methods
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// Date formatting
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

onMounted(fetchJobs);
</script>
