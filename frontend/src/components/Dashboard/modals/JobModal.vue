<template>
  <div v-if="job" class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10">
    <div class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col" @click.stop>
      
      <!-- Header -->
      <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
        <h2 class="text-2xl font-semibold">
          {{ job.isApplied ? "Application Details" : job.title }}
        </h2>
        <button @click="$emit('close')" class="text-gray-700 hover:text-gray-900 text-2xl font-bold">✕</button>
      </div>

      <!-- Content -->
      <div class="flex-1 p-4 md:p-6 overflow-y-auto">

        <!-- Applied Job -->
        <div v-if="job.isApplied" class="space-y-4">
          <p><strong>Subject:</strong> {{ job.subject }}</p>
          <div>
            <strong>Body:</strong>
            <div v-html="job.body" class="prose max-w-full"></div>
          </div>

          <p>
            <strong>CV:</strong>
            <a v-if="job.application_cv" :href="getFullCVUrl(job.application_cv)" target="_blank">
              {{ getFileNameFromPath(job.application_cv) }}
            </a>
            <span v-else>Not attached</span>
          </p>

          <p>
            <strong>Cover Letter:</strong>
            <a v-if="job.application_cover_letter" :href="getFullCVUrl(job.application_cover_letter)" target="_blank">
              {{ getFileNameFromPath(job.application_cover_letter) }}
            </a>
            <span v-else>Not attached</span>
          </p>

          <p><strong>Applied On:</strong> {{ formatDate(job.applied_on) }}</p>
        </div>

        <!-- Pending Job -->
        <div v-else>
          <!-- Basic Info -->
          <div class="space-y-2">
            <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
            <p class="text-gray-500">Location: {{ job.county }}, {{ job.country }}</p>
            <p class="text-gray-500">Deadline: {{ formatDate(job.deadline) }}</p>
            <p class="text-gray-700"><strong>Experience:</strong> {{ job.experience }} years</p>
            <p class="text-gray-700"><strong>Education:</strong> {{ job.education }}</p>
            <p class="text-gray-700"><strong>Salary:</strong> {{ job.salary }}</p>
            <p class="text-gray-700"><strong>Field:</strong> {{ job.field_name }}</p>
          </div>

          <!-- Job Description -->
          <div class="mt-4">
            <p class="text-gray-700 font-semibold mb-1">Job Description:</p>
            <div v-html="job.description" class="prose max-w-full"></div>
          </div>

          <!-- Application Instructions -->
          <div class="mt-4">
            <p class="text-gray-700 font-semibold mb-1">Application Instructions:</p>
            <div v-html="job.applicationInstructions" class="prose max-w-full"></div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  job: Object
});

const emit = defineEmits(['close']);

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

function getFileNameFromPath(path) {
  return path?.split("/").pop() || "";
}

function getFullCVUrl(path) {
  return path?.startsWith("http")
    ? path
    : `https://careershyne.com/storage/${path}`;
}
</script>
