<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Prepare for Interviews</h3>
    <p class="text-muted-light dark:text-muted-dark mb-6">
      Get AI-assisted tips and practice questions to ace your interviews.
    </p>

    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center">
      <span class="material-icons-sharp text-5xl text-primary mb-4">record_voice_over</span>
      <p class="font-semibold mb-2">Upload your job description or role info</p>
      <p class="text-muted-light dark:text-muted-dark text-sm mb-4">PDF, DOC, DOCX up to 5MB</p>
      <button class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
        Or browse files
      </button>
    </div>

    <textarea
      class="w-full mt-6 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 focus:ring-primary focus:border-primary"
      placeholder="Or paste role details or practice questions here..."
      rows="8"
    ></textarea>
  </div>
</template>
