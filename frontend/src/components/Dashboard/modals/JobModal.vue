<template>
  <div v-if="job" class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10">

    <div class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col" @click.stop>
      
      <!-- Header -->
      <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
        <h2 class="text-2xl font-semibold">{{ job.title }}</h2>
        <button @click="$emit('close')" class="text-gray-700 hover:text-gray-900 text-2xl font-bold">✕</button>
      </div>

      <!-- Content -->
      <div class="flex-1 p-4 md:p-6 overflow-y-auto">
        <!-- Basic Info -->
        <div class="space-y-2">
          <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
          <p class="text-gray-500">Location: {{ job.county }}, {{ job.country }}</p>
          <p class="text-gray-500">Deadline: {{ formatDate(job.deadline) }}</p>
          <p class="text-gray-700"><strong>Experience:</strong> {{ job.experience }} years</p>
          <p class="text-gray-700"><strong>Education:</strong> {{ job.education }}</p>
          <p class="text-gray-700"><strong>Salary:</strong> {{ job.salary }}</p>
          <p class="text-gray-700"><strong>Field:</strong> {{ job.field }}</p>
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
</template>

<script setup>
const props = defineProps({
  job: Object
});

const emit = defineEmits(['close', 'check-eligibility', 'cv-revamp', 'cover-letter', 'email-template']);

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}
</script>
