<template>
  <section class="bg-white py-16" id="ai-services">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
      
      <!-- HEADER -->
      <div class="text-center mb-12">
        <p class="text-sm font-semibold text-orange-500 uppercase tracking-wide">
          Your AI Career Tool Kit
        </p>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
          CareerShyne AI, Simplified
        </h2>
      </div>

      <!-- TOOLS GRID -->
      <div class="flex flex-wrap justify-center gap-4 mb-12">
        <div
          v-for="(service, index) in services"
          :key="index"
          @click="selectService(service)"
          :class="[
            'px-6 py-3 rounded-xl border shadow-sm font-semibold cursor-pointer transition-transform transition-shadow transform',
            selectedService?.name === service.name 
              ? 'text-orange-600 scale-105 shadow-lg' 
              : 'bg-white text-gray-700 hover:text-orange-600 hover:scale-105 hover:shadow-lg'
          ]"
        >
          {{ service.name }}
        </div>
      </div>

      <!-- BANNER IMAGE -->
      <div v-if="selectedService" class="flex justify-center">
        <img 
          :src="selectedService.banner" 
          :alt="selectedService.name" 
          class="max-w-3xl w-full h-[300px] md:h-[450px] lg:h-[550px] object-cover shadow-md transition-all duration-500"
        />
      </div>
      
    </div>
  </section>
</template>

<script setup>
import { ref } from "vue";

// Service list with banners
const services = [
  { name: "CV Revamp", banner: "/img/cv-banner.png" },
  { name: "Cover Letter Builder", banner: "/img/coverletter-banner.png" },
  { name: "Email Drafts", banner: "/img/email-banner.png" },
  { name: "Job Eligibility Checker", banner: "/img/jobchecker-banner.png" },
];

// Default: CV Revamp selected
const selectedService = ref(services[0]);

function selectService(service) {
  selectedService.value = service;
}
</script>
