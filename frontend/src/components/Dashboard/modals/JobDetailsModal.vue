<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
    @click="$emit('close')"
  >
    <div
      class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col"
      @click.stop
    >
      <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
        <h2 class="text-2xl font-semibold">{{ job?.title }}</h2>
        <button @click="$emit('close')" class="text-gray-700 hover:text-gray-900 text-2xl font-bold">✕</button>
      </div>

      <div class="p-4 md:p-6 overflow-x-hidden overflow-y-auto flex-1 w-full">
        <p class="text-gray-600 mb-2">{{ job?.company }} - {{ job?.type }}</p>
        <p class="text-gray-500 mb-2">Location: {{ job?.county }}, {{ job?.country }}</p>
        <p class="text-gray-500 mb-2">Deadline: {{ new Date(job?.deadline).toLocaleDateString() }}</p>
        <p class="text-gray-700 mb-2"><strong>Experience:</strong> {{ job?.experience }} years</p>
        <p class="text-gray-700 mb-2"><strong>Education:</strong> {{ job?.education }}</p>
        <p class="text-gray-700 mb-2"><strong>Salary:</strong> {{ job?.salary }}</p>
        <p class="text-gray-700 mb-2"><strong>Field:</strong> {{ job?.field }}</p>
        <p class="text-gray-700 mb-2"><strong>Description:</strong></p>
        <div v-html="job?.description" class="prose mb-4 max-w-full"></div>
        <p class="text-gray-700 mb-2"><strong>Application Instructions:</strong></p>
        <div v-html="job?.applicationInstructions" class="prose max-w-full"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  job: Object
});
</script>
