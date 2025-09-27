<template>
  <div>
    <h2 class="text-2xl font-semibold mb-6">Featured Jobs</h2>

    <!-- Loader -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
    </div>

    <!-- Jobs List -->
    <div v-else class="space-y-6">
      <div
        v-for="job in jobs.slice(0, 5)"
        :key="job.id"
        class="border rounded p-4 hover:shadow transition flex flex-col gap-4"
      >
        <!-- Job Details -->
        <div class="flex-1">
          <h3 class="text-lg font-semibold text-blue-600">{{ job.title }}</h3>
          <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
          <p class="text-gray-500 text-sm">
            Location: {{ job.county }}, {{ job.country }} | Deadline: {{ formatDate(job.deadline) }}
          </p>
        </div>

        <!-- Actions at bottom -->
        <div class="flex flex-wrap gap-2 mt-4">
          <button @click="$emit('check-eligibility', job)" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition text-sm flex-1">Check Eligibility</button>
          <button @click="$emit('revamp-cv', job)" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition text-sm flex-1">CV Revamp</button>
          <button @click="$emit('generate-cover', job)" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-sm flex-1">Cover Letter</button>
          <button @click="$emit('generate-email', job)" class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600 transition text-sm flex-1">Email Template</button>
          <button @click="$emit('view-details', job)" class="px-4 py-2 bg-white text-black rounded border border-[#fd624e] transition text-sm flex-1">View Details</button>
        </div>
      </div>
    </div>
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
