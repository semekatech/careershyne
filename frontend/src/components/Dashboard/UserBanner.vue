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
          How the Job Tools Work
        </h2>

        <!-- Modal Body -->
        <div class="text-sm sm:text-base space-y-4 leading-relaxed">
          <p>
            🧠 <strong>1. Your uploaded CV is automatically used.</strong><br />
            The system relies on your latest uploaded CV to assess eligibility and generate cover letters.
            If you want to change your CV, please visit your
            <a href="/profile/update" class="text-primary hover:underline font-medium">
              Profile Update
            </a> page.
          </p>

          <p>
            ✅ <strong>2. Click <span class="text-primary">Check Eligibility</span>.</strong><br />
            Instantly see if you meet the job’s requirements based on your current CV data.
          </p>

          <p>
            ✍️ <strong>3. Click <span class="text-primary">Generate Cover Letter</span>.</strong><br />
            Our AI writes a personalized cover letter for that specific job — ready in seconds.
          </p>

          <p>
            📩 <strong>4. Click <span class="text-primary">Send / Download</span>.</strong><br />
            You can download your generated documents or send them directly to the employer from your dashboard.
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
    title: "Your AI-Powered Job Search Toolkit",
    description:
      "Use your uploaded CV to check job eligibility, generate cover letters, and apply faster — all powered by AI.",
  },
  {
    title: "Smart Cover Letters, Real-Time Insights",
    description:
      "Get personalized cover letters and eligibility feedback instantly using your current CV.",
  },
  {
    title: "Work Smarter, Apply Better",
    description:
      "Simplify your job search process — check, write, and send with one click.",
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
