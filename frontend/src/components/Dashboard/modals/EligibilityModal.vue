<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
  >
    <div
      class="bg-white w-full md:max-w-4xl h-[80vh] relative mx-2 md:mx-0 rounded-2xl shadow-xl flex flex-col"
      @click.stop
    >
      <!-- Header -->
      <div
        class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl"
      >
        <h2 class="text-2xl font-bold text-gray-800">Eligibility Check</h2>
        <button
          @click="$emit('close')"
          class="text-gray-500 hover:text-gray-800 text-2xl font-bold"
        >
          ✕
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 p-6 overflow-y-auto">
        <!-- Error state -->
        <div
          v-if="result?.error"
          class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg text-center"
        >
          {{ result.error }}
        </div>

        <!-- Loading / Progress -->
        <div
          v-else-if="!result || progress < 100"
          class="flex flex-col items-center justify-center h-full text-center space-y-4"
        >
          <p class="text-gray-600 text-lg font-medium">
            Comparing your CV with <strong>{{ job?.title }}</strong
            ><span class="inline-block animate-pulse">...</span>
          </p>
          <p class="text-gray-500 text-sm italic">{{ loadingMessage }}</p>
          <div
            class="w-3/4 h-4 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700"
          >
            <div
              class="h-4 rounded-full animate-slide"
              :style="{ width: progress + '%', background: currentGradient }"
            ></div>
          </div>
          <p class="text-gray-700 font-medium">{{ progress }}%</p>
        </div>

        <!-- Result state -->
        <div v-else class="space-y-8">
          <div class="flex flex-col items-center">
            <div
              class="relative flex items-center justify-center w-32 h-32 rounded-full border-8 shadow-md"
              :class="
                result.matchPercentage >= 70
                  ? 'border-green-500'
                  : result.matchPercentage >= 40
                  ? 'border-yellow-500'
                  : 'border-red-500'
              "
            >
              <span class="text-3xl font-bold text-gray-800"
                >{{ result.matchPercentage }}%</span
              >
            </div>
            <p class="mt-2 text-gray-600 font-medium">Match Score</p>
          </div>

          <div
            v-if="result.matchedSkills?.length"
            class="bg-green-50 border border-green-200 rounded-lg p-5 shadow-sm"
          >
            <h3 class="font-semibold text-green-700 mb-3 text-lg">
              ✅ Matched Skills
            </h3>
            <ul
              class="list-disc list-inside text-sm text-gray-700 grid grid-cols-1 sm:grid-cols-2 gap-x-6"
            >
              <li v-for="(skill, index) in result.matchedSkills" :key="index">
                {{ skill }}
              </li>
            </ul>
          </div>

          <div
            v-if="result.missingSkills?.length"
            class="bg-red-50 border border-red-200 rounded-lg p-5 shadow-sm"
          >
            <h3 class="font-semibold text-red-700 mb-3 text-lg">
              ❌ Missing Skills
            </h3>
            <ul
              class="list-disc list-inside text-sm text-red-600 grid grid-cols-1 sm:grid-cols-2 gap-x-6"
            >
              <li v-for="(skill, index) in result.missingSkills" :key="index">
                {{ skill }}
              </li>
            </ul>
          </div>

          <div
            v-if="result.recommendations"
            class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm"
          >
            <h3 class="font-semibold text-blue-700 mb-3 text-lg">
              💡 Recommendations
            </h3>
            <p class="text-sm text-gray-700 leading-relaxed">
              {{ result.recommendations }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  job: Object,
  progress: Number,
  result: Object,
});

// Animated loading messages & gradient colors
const messages = [
  {
    text: "Analyzing experience...",
    color: "linear-gradient(to right, #38bdf8, #60a5fa)",
  },
  {
    text: "Checking skills...",
    color: "linear-gradient(to right, #a78bfa, #f472b6)",
  },
  {
    text: "Comparing job requirements...",
    color: "linear-gradient(to right, #facc15, #f97316)",
  },
  {
    text: "Finalizing match score...",
    color: "linear-gradient(to right, #34d399, #10b981)",
  },
];

const loadingMessage = computed(() => {
  if (!props.progress) return messages[0].text;
  const index = Math.floor((props.progress / 100) * messages.length);
  return messages[Math.min(index, messages.length - 1)].text;
});

const currentGradient = computed(() => {
  if (!props.progress) return messages[0].color;
  const index = Math.floor((props.progress / 100) * messages.length);
  return messages[Math.min(index, messages.length - 1)].color;
});
</script>

<style>
@keyframes slide {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
.animate-slide {
  background-size: 200% 100%;
  animation: slide 1.5s linear infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
}
.animate-pulse {
  animation: pulse 1s infinite;
}
</style>
