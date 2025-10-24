<template>
  <TheWelcome />

  <section class="relative py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <!-- HEADER -->
      <div class="text-center mb-16">
        <p
          class="text-sm font-semibold text-orange-600 uppercase tracking-wide mb-2"
        >
          Unlock Your Career Potential
        </p>
        <h2
          class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight"
        >
          What We Offer
        </h2>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          CareerShyne combines automation, optimization, and expert insight to help you stand out and get hired faster.
        </p>
      </div>

      <!-- SERVICES GRID -->
      <div class="grid md:grid-cols-3 gap-10 relative">
        <!-- CONNECTING LINE (desktop only) -->
        <div
          class="hidden md:block absolute top-1/2 left-0 right-0 h-0.5 bg-orange-100 transform -translate-y-1/2 z-0"
        ></div>

        <!-- SERVICE CARD -->
        <div
          v-for="(service, index) in services"
          :key="index"
          class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-md p-8 text-center transition-all transform hover:-translate-y-2 hover:shadow-2xl z-10"
        >
          <div
            class="flex items-center justify-center mx-auto w-14 h-14 rounded-full bg-orange-500 text-white text-2xl font-bold mb-4 shadow-lg"
          >
            {{ index + 1 }}
          </div>
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
            {{ service.title }}
          </h3>
          <p class="text-gray-600 dark:text-gray-300 mb-4">
            {{ service.description }}
          </p>
          <p class="font-medium text-gray-800 dark:text-gray-200">
            {{ service.outcome }}
          </p>
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center mt-20">
        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
          Ready to Elevate Your Career?
        </h3>
        <router-link
          to="/login"
          class="px-8 py-4 rounded-2xl bg-orange-500 text-white hover:bg-gray-900 hover:text-orange-400 transition font-medium shadow-md"
        >
          Get Started Today
        </router-link>
      </div>
    </div>
  </section>

  <FooterSection />
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";

const services = [
  {
    title: "CV Review & Optimization",
    description:
      "We refine your CV to grab recruiter attention, beat ATS filters, and highlight measurable achievements.",
    outcome:
      "Result: From invisible to irresistible — more callbacks, interviews, and opportunities.",
  },
  {
    title: "Cover Letters That Impress",
    description:
      "We craft personalized, authentic cover letters that show your motivation and fit for each role.",
    outcome:
      "Result: Letters that connect emotionally and get read by real decision-makers.",
  },
  {
    title: "LinkedIn Optimization",
    description:
      "We transform your LinkedIn into a recruiter magnet with keyword-rich content and powerful storytelling.",
    outcome:
      "Result: Build credibility, expand your network, and attract the right opportunities.",
  },
];
</script>

<style scoped>
div:hover .hover\:float {
  transform: translateY(-6px);
  transition: all 0.3s ease;
}
</style>
