<template>
  <main
    class="bg-gradient-to-br from-rose-50 to-white dark:from-gray-900 dark:to-gray-800"
  >
    <TheWelcome />
    <PaymentForm />
    <FooterSection />
  </main>
</template>
<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import PaymentForm from "@/components/CvOrderConfirmation.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>

