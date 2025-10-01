<template>
  <div
    class="w-full bg-primary/10 dark:bg-primary/20 
           p-4 sm:p-6 md:p-8 
           rounded-lg mb-8 
           flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
  >
    <!-- Text -->
    <div class="flex-1 text-center sm:text-left">
      <h3 class="text-lg sm:text-2xl font-bold text-text-light dark:text-text-dark">
        Your AI-Powered Job Search Toolkit
      </h3>
      <p class="text-muted-light dark:text-muted-dark mt-2 max-w-xl mx-auto sm:mx-0 text-sm sm:text-base">
        Transform your CV, write standout cover letters, and create polished
        email templates that help you land the opportunities you deserve.
      </p>
    </div>

    <!-- Button -->
    <div class="w-full sm:w-auto flex justify-center sm:justify-end">
      <button
        @click="scrollToTabs"
        class="w-full sm:w-auto bg-primary text-white font-semibold py-3 px-6 rounded-lg 
               flex items-center justify-center shadow-lg 
               hover:bg-primary/90 transition-colors"
      >
        <span class="material-icons-sharp mr-2">try</span>
        Try Now
      </button>
    </div>
  </div>
</template>
