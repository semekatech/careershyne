<template>
  <TheWelcome />

  <section
    class="bg-gradient-to-br from-green-50 via-white to-purple-100 py-20"
  >
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <transition name="fade" mode="out-in">
        <div
          v-if="showForm"
          key="form"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-10 border border-orange-100 dark:border-gray-700 max-w-2xl mx-auto"
        >
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Upload Your CV 📄
          </h3>
          <p class="text-gray-600 dark:text-gray-300 mb-6">
            Drag & drop your CV here, or click to select a file. PDF only, max
            2MB.
          </p>

          <div
            class="border-2 border-dashed border-orange-400 rounded-xl p-10 flex flex-col items-center justify-center cursor-pointer transition-all duration-300 hover:border-orange-500 hover:shadow-lg hover:bg-orange-50/50 dark:hover:bg-gray-700"
            @click="triggerFileInput"
          >
            <svg
              class="w-14 h-14 text-orange-500 mb-3"
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
              <span class="text-orange-600 dark:text-orange-400 font-semibold"
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
                class="bg-gradient-to-r from-orange-400 to-pink-500 h-2 rounded-full transition-all duration-700 ease-out"
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
              class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-pink-500 text-white font-semibold transition transform hover:scale-[1.02] shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
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
                class="bg-gradient-to-r from-orange-400 to-pink-500 h-2 rounded-full transition-all duration-700 ease-out"
                :style="{ width: submitProgress + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <div
          v-else
          key="review"
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-10 border border-gray-100 dark:border-gray-700 max-w-4xl mx-auto"
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
                  <defs>
                    <linearGradient
                      id="score-green-gradient"
                      x1="0%"
                      y1="0%"
                      x2="100%"
                      y2="0%"
                    >
                      <stop offset="0%" stop-color="#22c55e" />
                      <stop offset="100%" stop-color="#16a34a" />
                    </linearGradient>
                  </defs>
                  <path
                    class="circle-bg"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                  <path
                    class="circle"
                    stroke="url(#score-green-gradient)"
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
                  class="text-xl font-bold text-green-600 dark:text-green-400 flex items-center mb-2"
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
                  class="text-xl font-bold text-red-600 dark:text-red-400 flex items-center mb-2"
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
                  class="text-xl font-bold text-blue-600 dark:text-blue-400 flex items-center mb-2"
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

            <section class="mt-8">
              <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                Overall Impression
              </h4>
              <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ review.impression }}
              </p>
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
import { ref, onMounted, watch } from "vue";
import Swal from "sweetalert2";
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/AiFooter.vue";
import UploadService from "@/services/UploadService";
import * as pdfjsLib from "pdfjs-dist";

// Tell pdfjs where to find the worker
pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
  "pdfjs-dist/build/pdf.worker.min.mjs",
  import.meta.url
).toString();

const fileInput = ref(null);
const selectedFile = ref(null);
const fileName = ref("");
const attachmentProgress = ref(0);
const submitting = ref(false);
const submitProgress = ref(0);
const review = ref("");
const hcaptchaToken = ref(null);
const showForm = ref(true);

let hcaptchaWidgetId = null;

function triggerFileInput() {
  fileInput.value.click();
}

async function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  // ✅ 1. Size check (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    Swal.fire({
      icon: "error",
      title: "File too large",
      text: "Your CV must be under 5 MB.",
    });
    return;
  }

  // ✅ 2. Page count check (max 5 pages)
  try {
    const arrayBuffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
    if (pdf.numPages > 5) {
      Swal.fire({
        icon: "error",
        title: "Too many pages",
        text: "Your CV must not exceed 5 pages.",
      });
      return;
    }
  } catch (err) {
    console.error("Error reading PDF:", err);
    Swal.fire({
      icon: "error",
      title: "Invalid file",
      text: "Could not read the PDF. Please upload a valid file.",
    });
    return;
  }

  // ✅ Passed validation
  selectedFile.value = file;
  fileName.value = file.name;
  attachmentProgress.value = 0;

  // fake progress for UX
  const reader = new FileReader();
  reader.onprogress = (e) => {
    if (e.lengthComputable) {
      attachmentProgress.value = Math.round((e.loaded * 100) / e.total);
    }
  };
  reader.onloadend = () => {
    attachmentProgress.value = 100;
  };
  reader.readAsArrayBuffer(file);
}

async function submitForm() {
  if (!selectedFile.value) return alert("Please select a file.");
  if (!hcaptchaToken.value)
    return alert("Please complete the hCaptcha verification.");

  submitting.value = true;
  review.value = "";

  Swal.fire({
    title: "Hold on…",
    html: `<div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
              <div class="loader"></div>
              <div style="font-weight:600; color:#f97316;">Uploading your CV and getting AI review...</div>
            </div>`,
    showConfirmButton: false,
    allowOutsideClick: false,
    background: "#fef3c7",
  });

  try {
    const res = await UploadService.uploadFile(
      selectedFile.value,
      hcaptchaToken.value
    );
    review.value = res.data.review || "No review received.";
    showForm.value = false;

    Swal.fire({
      icon: "success",
      title: "AI Review Ready!",
      confirmButtonText: "Close",
      width: 600,
      background: "#fef3c7",
    });
  } catch (err) {
    console.error(err);

    if (err.response) {
      const status = err.response.status;
      const data = err.response.data;

      if (status === 422) {
        Swal.fire({
          icon: "warning",
          title: "Invalid CV",
          text: data.message || "The uploaded file is not a valid CV.",
          background: "#fef9c3",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Submission Failed",
          text: data.message || "Please try again!",
          background: "#fee2e2",
        });
      }
    } else {
      // network / unexpected error
      Swal.fire({
        icon: "error",
        title: "Network Error",
        text: "Could not reach the server. Please check your connection.",
        background: "#fee2e2",
      });
    }
  } finally {
    submitting.value = false;
    attachmentProgress.value = 0;
    selectedFile.value = null;
    fileName.value = "";
  }
}

watch(showForm, (newVal) => {
  if (newVal) {
    setTimeout(() => renderHCaptcha(), 100);
  }
});

onMounted(() => {
  if (!window.hcaptcha) {
    const script = document.createElement("script");
    script.src = "https://js.hcaptcha.com/1/api.js";
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
    script.onload = renderHCaptcha;
  } else {
    renderHCaptcha();
  }
});

function renderHCaptcha() {
  if (!window.hcaptcha) return;

  const container = document.getElementById("hcaptcha-container");
  if (container) container.innerHTML = "";

  hcaptchaWidgetId = window.hcaptcha.render("hcaptcha-container", {
    sitekey: "4eaee940-28ca-4440-855a-b9eaa88ad3be",
    callback: (token) => (hcaptchaToken.value = token),
    "expired-callback": () => (hcaptchaToken.value = null),
  });
}
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
  stroke: #eee;
  stroke-width: 3.8;
}

.circle {
  fill: none;
  /* Now uses the green gradient defined in SVG defs */
  stroke-width: 2.8;
  stroke-linecap: round;
  transform: rotate(-90deg);
  transform-origin: 50% 50%;
  transition: stroke-dasharray 0.8s ease-out;
}

.percentage {
  fill: #16a34a; /* Changed to green to match the score bar */
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  font-size: 0.5em;
  text-anchor: middle;
  dominant-baseline: central;
  font-weight: 700;
}
</style>