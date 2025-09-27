<template>
  <div
    v-if="show"
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
        <h2 class="text-2xl font-bold text-gray-800">Cover Letter</h2>
        <div class="flex items-center gap-3">
          <!-- Download Word -->
          <button
            v-if="coverLetter?.content"
            @click="downloadWord"
            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition"
          >
            Download Word
          </button>

          <!-- Close -->
          <button
            @click="$emit('close')"
            class="text-gray-500 hover:text-gray-800 text-2xl font-bold"
          >
            ✕
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 p-6 overflow-y-auto">
        <!-- Loading -->
        <div v-if="!coverLetter || progress < 100" class="flex flex-col items-center justify-center h-full text-center">
          <p class="mb-4 text-gray-600">
            Generating cover letter for <strong>{{ job?.title }}</strong>...
          </p>

          <!-- Progress -->
          <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div
              class="bg-yellow-500 h-4 rounded-full transition-all duration-500"
              :style="{ width: progress + '%' }"
            ></div>
          </div>
          <p class="mt-3 text-gray-700 font-medium">{{ progress }}%</p>
        </div>

        <!-- Result -->
        <div v-else class="space-y-6">
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Generated Cover Letter</h3>
          <div
            v-if="coverLetter.content"
            class="prose max-w-full text-gray-700 bg-gray-50 p-4 rounded-lg shadow-sm"
            v-html="coverLetter.content"
          ></div>

          <!-- Recommendations -->
          <div
            v-if="coverLetter.recommendations?.length"
            class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm"
          >
            <h3 class="font-semibold text-blue-700 mb-3 text-lg">💡 Recommendations</h3>
            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
              <li v-for="(rec, idx) in coverLetter.recommendations" :key="idx">{{ rec }}</li>
            </ul>
          </div>

          <!-- Error -->
          <div
            v-else-if="coverLetter.error"
            class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg"
          >
            {{ coverLetter.error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import coverLetterService from "@/services/coverLetterService";

defineProps({
  show: Boolean,
  job: Object
});

const progress = ref(0);
const coverLetter = ref(null);

watch(
  () => show,
  async (val) => {
    if (val) {
      progress.value = 0;
      coverLetter.value = null;

      // Fake progress
      const interval = setInterval(() => {
        if (progress.value < 90) progress.value += 10;
      }, 400);

      try {
        const result = await coverLetterService.generate(job.id);
        clearInterval(interval);
        progress.value = 100;
        coverLetter.value = result;
      } catch (err) {
        clearInterval(interval);
        progress.value = 100;
        coverLetter.value = { error: "Failed to generate cover letter." };
      }
    }
  }
);

function downloadWord() {
  if (!coverLetter.value?.content) return;

  const content = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" 
          xmlns:w="urn:schemas-microsoft-com:office:word" 
          xmlns="http://www.w3.org/TR/REC-html40">
      <head><meta charset="utf-8"><title>Cover Letter</title></head>
      <body>${coverLetter.value.content}</body>
    </html>
  `;

  const blob = new Blob(["\ufeff", content], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "cover-letter.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>
