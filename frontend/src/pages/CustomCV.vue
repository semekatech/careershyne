<template>
  <main>
    <TheWelcome />

    <!-- Gray background only for the form section -->
    <section class="bg-gray-100 dark:bg-gray-900 py-12 min-h-screen">
      <CustomCvForm />
    </section>

    <!-- Footer stays white -->
    <FooterSection />
  </main>
</template>


<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import CustomCvForm from "@/components/CustomCvForm.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>

<style scoped>
.prose {
  max-width: 100%;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}




</style>
