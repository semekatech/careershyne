<template>
  <TheWelcome />

  <section class="bg-gray-50 dark:bg-gray-900 py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <transition name="fade" mode="out-in">
        <div
          v-if="showForm"
          key="form"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-10 border border-gray-200 dark:border-gray-700 max-w-2xl mx-auto"
        >
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Upload Your CV 📄
          </h3>
          <p class="text-gray-600 dark:text-gray-300 mb-6">
            Drag & drop your CV here, or click to select a file. PDF only, max
            5MB.
          </p>

          <div
            class="border-2 border-dashed border-blue-400 rounded-xl p-10 flex flex-col items-center justify-center cursor-pointer transition-all duration-300 hover:border-blue-500 hover:shadow-md hover:bg-blue-50/50 dark:hover:bg-gray-700"
            @click="triggerFileInput"
          >
            <svg
              class="w-14 h-14 text-blue-500 mb-3"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 16v-8m-4 4h8M20 12c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8z"
              />
            </svg>
            <p class="text-gray-800 dark:text-gray-200 font-medium">
              Drop your file here or
              <span class="text-blue-600 dark:text-blue-400 font-semibold"
                >browse</span
              >
            </p>
            <input
              type="file"
              ref="fileInput"
              accept="application/pdf"
              class="hidden"
              @change="handleFileUpload"
            />
          </div>

          <div
            v-if="fileName"
            class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4"
          >
            <p class="text-sm text-gray-700 dark:text-gray-300">
              📂 Selected File: <strong>{{ fileName }}</strong>
            </p>
            <div
              class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-3 overflow-hidden"
            >
              <div
                class="bg-blue-500 h-2 rounded-full transition-all duration-700 ease-out"
                :style="{ width: attachmentProgress + '%' }"
              ></div>
            </div>
            <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">
              {{ attachmentProgress }}%
            </p>
          </div>

          <div class="mt-6 flex justify-center">
            <div id="hcaptcha-container"></div>
          </div>

          <div class="mt-8">
            <button
              class="w-full px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold transition transform hover:scale-[1.02] shadow-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-700"
              @click="submitForm"
              :disabled="
                attachmentProgress < 100 || submitting || !hcaptchaToken
              "
            >
              {{ submitting ? "Submitting..." : "🚀 Submit CV for Review" }}
            </button>

            <div
              v-if="submitting"
              class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-3 overflow-hidden"
            >
              <div
                class="bg-blue-500 h-2 rounded-full transition-all duration-700 ease-out"
                :style="{ width: submitProgress + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <div
          v-else
          key="review"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-10 border border-gray-200 dark:border-gray-700 max-w-4xl mx-auto"
        >
          <div v-if="review">
            <div
              class="flex flex-col md:flex-row items-center justify-between mb-8"
            >
              <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
                AI CV Review ✨
              </h3>
              <div class="score-circle mt-6 md:mt-0">
                <svg viewBox="0 0 36 36" class="circular-chart">
                  <path
                    class="circle-bg"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                  <path
                    class="circle"
                    stroke="#2563eb"
                    :stroke-dasharray="`${review.score}, 100`"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                  <text x="18" y="20.35" class="percentage">
                    {{ review.score }}%
                  </text>
                </svg>
                <div
                  class="mt-2 text-center text-sm font-semibold text-gray-600 dark:text-gray-300"
                >
                  Overall Score
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <div
                class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700"
              >
                <h4
                  class="text-xl font-bold text-green-700 dark:text-green-400 flex items-center mb-2"
                >
                  <span class="mr-2">💪</span> Strengths
                </h4>
                <ul
                  class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300"
                >
                  <li v-for="(s, i) in review.strengths" :key="'s' + i">
                    {{ s }}
                  </li>
                  <li
                    v-if="!review.strengths.length"
                    class="text-gray-500 italic"
                  >
                    No strengths detected.
                  </li>
                </ul>
              </div>

              <div
                class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700"
              >
                <h4
                  class="text-xl font-bold text-red-700 dark:text-red-400 flex items-center mb-2"
                >
                  <span class="mr-2">🚧</span> Weaknesses
                </h4>
                <ul
                  class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300"
                >
                  <li v-for="(w, i) in review.weaknesses" :key="'w' + i">
                    {{ w }}
                  </li>
                  <li
                    v-if="!review.weaknesses.length"
                    class="text-gray-500 italic"
                  >
                    No weaknesses detected.
                  </li>
                </ul>
              </div>

              <div
                class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700"
              >
                <h4
                  class="text-xl font-bold text-blue-700 dark:text-blue-400 flex items-center mb-2"
                >
                  <span class="mr-2">💡</span> Suggestions
                </h4>
                <ul
                  class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300"
                >
                  <li v-for="(t, i) in review.suggestions" :key="'t' + i">
                    {{ t }}
                  </li>
                  <li
                    v-if="!review.suggestions.length"
                    class="text-gray-500 italic"
                  >
                    No suggestions provided.
                  </li>
                </ul>
              </div>
            </div>

            <section class="mt-10 text-center">
              <div
                class="bg-gray-100 dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm"
              >
                <h4
                  class="text-xl font-bold text-gray-900 dark:text-white mb-3"
                >
                  Don’t Miss Out on Job Opportunities
                </h4>
                <p
                  class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6"
                >
                  Based on your score and review, your CV might be holding you
                  back.
                  <span class="font-semibold text-blue-600 dark:text-blue-400"
                    >CareerShyne</span
                  >
                  can help you Revamp your CV and stand out to employers.
                </p>
                <router-link
                  to="/order-cv?ref=ai"
                  class="px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold shadow-md transform transition hover:scale-[1.05] hover:bg-blue-700"
                >
                  ✨ Yes, Help Me Revamp My CV
                </router-link>
              </div>
            </section>
          </div>
          <div v-else>
            <p class="text-gray-600 dark:text-gray-300">
              No review available. Please upload a CV.
            </p>
          </div>
        </div>
      </transition>
    </div>
  </section>

  <FooterSection />
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import UploadService from "@/services/UploadService";
import { useFileValidation } from "@/composables/useFileValidation";
import { useHCaptcha } from "@/composables/useHCaptcha";
import { useApiError } from "@/composables/useApiError";

const { validatePDF } = useFileValidation();
const { hcaptchaToken, render, reset } = useHCaptcha("your-site-key");
const { handleError } = useApiError();

const fileName = ref("");
const selectedFile = ref(null);

async function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  try {
    await validatePDF(file);
    selectedFile.value = file;
    fileName.value = file.name;
  } catch (e) {
    Swal.fire({ icon: "error", title: "Invalid File", text: e.message });
  }
}

async function submitForm() {
  if (!selectedFile.value || !hcaptchaToken.value) return;

  try {
    const res = await UploadService.uploadFile(
      selectedFile.value,
      hcaptchaToken.value
    );
  } catch (err) {
    handleError(err);
  } finally {
    reset();
  }
}

onMounted(() => {
  if (!window.hcaptcha) {
    const script = document.createElement("script");
    script.src = "https://js.hcaptcha.com/1/api.js";
    script.async = true;
    script.defer = true;
    script.onload = render;
    document.head.appendChild(script);
  } else {
    render();
  }
});
</script>


<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.4s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Styles for the circular progress bar */
.score-circle {
  width: 120px;
  height: 120px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.circular-chart {
  display: block;
  margin: 10px auto;
}

.circle-bg {
  fill: none;
  stroke: #e5e7eb; /* Changed to a neutral gray */
  stroke-width: 3.8;
}

.circle {
  fill: none;
  stroke-width: 2.8;
  stroke-linecap: round;
  transform: rotate(-90deg);
  transform-origin: 50% 50%;
  transition: stroke-dasharray 0.8s ease-out;
}

.percentage {
  fill: #2563eb; /* Changed to blue to match the new palette */
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  font-size: 0.5em;
  text-anchor: middle;
  dominant-baseline: central;
  font-weight: 700;
}
</style>