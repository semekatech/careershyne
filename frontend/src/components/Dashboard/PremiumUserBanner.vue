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
        @click="openModal"
        class="w-full sm:w-auto bg-primary text-white font-semibold py-3 px-6 rounded-lg 
               flex items-center justify-center shadow-lg 
               hover:bg-primary/90 transition-colors"
      >
        <span class="material-icons-sharp mr-2">help_outline</span>
        How It Works
      </button>
    </div>
  </div>

  <!-- Modal -->
  <transition name="fade">
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
    >
      <div
        class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-2xl shadow-2xl max-w-lg w-full mx-4 p-6 relative"
      >
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 dark:hover:text-gray-300"
        >
          <span class="material-icons">close</span>
        </button>

        <!-- Modal Header -->
        <h2 class="text-2xl font-bold mb-4 text-center text-primary">
          How Job Browsing Works
        </h2>

        <!-- Modal Body -->
        <div class="text-sm sm:text-base space-y-4 leading-relaxed">
          <p>
            🔍 <strong>1. Browse unlimited jobs in your field.</strong><br />
            Instantly explore hundreds of job listings carefully tailored to match your career field and skills.
          </p>

          <p>
            🎯 <strong>2. Use smart filters.</strong><br />
            Filter jobs by location, category, experience level, or posting date — so you find the most relevant opportunities faster.
          </p>

          <p>
            💼 <strong>3. Save or apply directly.</strong><br />
            Save jobs you like to your dashboard or apply instantly to the ones that fit your profile.
          </p>

          <p>
            📨 <strong>4. Stay updated.</strong><br />
            Receive alerts when new jobs matching your field are posted — never miss an opportunity again!
          </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-center">
          <button
            @click="closeModal"
            class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary/90 transition-all"
          >
            Got It
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";

// Slides content
const slides = [
  {
    title: "Explore Unlimited Jobs in Your Field",
    description:
      "Discover job listings tailored to your profession — browse, save, and apply directly with ease.",
  },
  {
    title: "Smart Job Matching & Easy Filtering",
    description:
      "Find the perfect job faster using filters for location, experience, and category.",
  },
  {
    title: "Stay Ahead with Job Alerts",
    description:
      "Be the first to know when new opportunities in your field are posted.",
  },
];

const currentIndex = ref(0);
const currentSlide = computed(() => slides[currentIndex.value]);

// Auto-slide logic
let interval = null;
onMounted(() => {
  interval = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % slides.length;
  }, 5000);
});
onUnmounted(() => clearInterval(interval));

// Modal logic
const showModal = ref(false);
const openModal = () => (showModal.value = true);
const closeModal = () => (showModal.value = false);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
