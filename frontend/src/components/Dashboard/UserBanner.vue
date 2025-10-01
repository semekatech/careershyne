<template>
  <div
    class="w-full p-4 sm:p-6 md:p-8 rounded-lg mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 
           bg-primary/10 dark:bg-primary/20 transition-all duration-500"
  >
    <!-- Text -->
    <div class="flex-1 text-center sm:text-left">
      <h3 class="text-lg sm:text-2xl font-bold text-text-light dark:text-text-dark">
        {{ currentSlide.title }}
      </h3>
      <p class="text-muted-light dark:text-muted-dark mt-2 max-w-xl mx-auto sm:mx-0 text-sm sm:text-base">
        {{ currentSlide.description }}
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

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";

// Slides content
const slides = [
  {
    title: "Your AI-Powered Job Search Toolkit",
    description:
      "Transform your CV, write standout cover letters, and create polished email templates that help you land the opportunities you deserve.",
  },
  {
    title: "Optimize Your CV Effortlessly",
    description:
      "Get AI-driven suggestions to make your CV more appealing to recruiters in your industry.",
  },
  {
    title: "Craft Standout Cover Letters",
    description:
      "Generate personalized cover letters in seconds and increase your chances of getting noticed.",
  },
  {
    title: "Send Professional Emails",
    description:
      "Create polished and tailored email templates for networking and job applications.",
  },
];

const currentIndex = ref(0);
const currentSlide = computed(() => slides[currentIndex.value]);

// Auto-slide logic
let interval = null;
onMounted(() => {
  interval = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % slides.length;
  }, 5000); // change every 5 seconds
});

onUnmounted(() => {
  clearInterval(interval);
});

// Button action
const scrollToTabs = () => {
  const element = document.getElementById("tabs-section");
  if (element) element.scrollIntoView({ behavior: "smooth" });
};
</script>
