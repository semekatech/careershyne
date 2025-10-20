<template>
<div
  class="bg-white dark:bg-card-dark p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4"
>

    <!-- Short Explanation -->
    <p class="text-gray-700 dark:text-gray-300 mb-4 sm:mb-0 leading-relaxed flex-1">
      Connect your Gmail account to allow
      <strong>automatic job application submissions</strong>
      and receive notifications on your behalf. Only minimal permissions are
      requested to read your mailbox and send emails securely.
      <strong>No passwords are stored on our servers.</strong>
    </p>

    <!-- Learn More Button -->
   <router-link
  :to="{ path: '/profile', query: { tab: 'gmail' } }"
  class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition-transform transform hover:scale-105"
>
  Connect Gmail
</router-link>

  </div>
</template>

<script setup>
const learnMore = () => {
  // Redirect to the page with more details
  window.location.href = "/gmail-authentication"; 
};
</script>
