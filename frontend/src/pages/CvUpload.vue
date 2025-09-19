<template>
  <TheWelcome />

  <section class="bg-gradient-to-br from-green-50 via-white to-purple-100 py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="grid md:grid-cols-2 gap-12 items-start">
        <!-- LEFT: Upload CV Form -->
        <div
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-10 border border-orange-100 dark:border-gray-700"
        >
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Upload Your CV
          </h3>
          <p class="text-gray-600 dark:text-gray-300 mb-6">
            Drag & drop your CV here, or click to select a file. PDF only, max
            2MB.
          </p>

          <!-- Drag & Drop Zone -->
          <div
            class="border-2 border-dashed border-orange-400 rounded-xl p-10 flex flex-col items-center justify-center cursor-pointer transition hover:border-orange-500 hover:shadow-lg hover:bg-orange-50/50 dark:hover:bg-gray-700"
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

          <!-- Attachment Progress -->
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

          <!-- reCAPTCHA Checkbox -->
          <div class="mt-6 flex justify-center">
            <VueRecaptcha
              sitekey="6LeVRM4rAAAAACZLcBp_o6lByka2W0R9xPqBgc1f"  
              @verify="onVerify"
              @expired="onExpired"
            />
          </div>

          <!-- Submit Button -->
          <div class="mt-8">
            <button
              class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-pink-500 text-white font-semibold hover:opacity-90 transition transform hover:scale-[1.02] shadow-md"
              @click="submitForm"
              :disabled="attachmentProgress < 100 || submitting || !recaptchaToken"
            >
              {{ submitting ? "Submitting..." : "🚀 Submit CV for Review" }}
            </button>

            <!-- Optional submit progress bar -->
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

        <!-- RIGHT: CV Feedback -->
        <div
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-10 border border-gray-100 dark:border-gray-700 min-h-[380px] flex flex-col overflow-y-auto"
        >
          <div v-if="review" class="w-full">
            <h4
              class="text-lg font-semibold text-orange-600 dark:text-orange-400 mb-4 flex items-center gap-2"
            >
              <span>✨ AI Insights</span>
            </h4>
            <div
              class="bg-gray-50 dark:bg-gray-700/40 p-4 rounded-lg shadow-inner"
            >
              <pre
                class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700 dark:text-gray-200"
              >{{ review }}</pre>
            </div>
          </div>

          <div v-else class="w-full">
            <h4 class="font-semibold text-orange-500 mb-4">
              📊 What you’ll get:
            </h4>
            <ul
              class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300 text-sm"
            >
              <li>✅ Instant resume scoring (0-100)</li>
              <li>⚡ Tailored tips to improve impact</li>
              <li>🎯 ATS optimization check</li>
              <li>💡 Highlighted strengths & weaknesses</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
  <FooterSection />
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/AiFooter.vue";
import UploadService from "@/services/UploadService";
import { VueRecaptcha } from 'vue-recaptcha-v2'

const fileInput = ref(null);
const selectedFile = ref(null);
const fileName = ref("");
const attachmentProgress = ref(0);
const uploading = ref(false);
const review = ref(""); 
const submitting = ref(false);
const recaptchaToken = ref(null);

function triggerFileInput() {
  fileInput.value.click();
}

async function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  selectedFile.value = file;
  fileName.value = file.name;
  attachmentProgress.value = 0;
  uploading.value = true;

  try {
    await UploadService.uploadFile(file, null, (e) => {
      if (e.lengthComputable) {
        attachmentProgress.value = Math.round((e.loaded * 100) / e.total);
      }
    });

    uploading.value = false;
    attachmentProgress.value = 100;
  } catch (err) {
    console.error("Attachment failed:", err);
    uploading.value = false;
    alert("Failed to attach file!");
  }
}

function onVerify(token) {
  recaptchaToken.value = token;
  console.log("✅ reCAPTCHA verified:", token);
}

function onExpired() {
  recaptchaToken.value = null;
  console.log("⚠️ reCAPTCHA expired");
}

async function submitForm() {
  if (!selectedFile.value || !recaptchaToken.value) {
    alert("Please select a file and complete the reCAPTCHA.");
    return;
  }

  submitting.value = true;
  review.value = "";

  Swal.fire({
    title: "Hold on…",
    html: `<div style="display:flex; flex-direction:column; align-items:center; gap:10px;">
             <div class="loader"></div>
             <div style="font-weight:600; color:#f97316;">Getting your AI review ready...</div>
           </div>`,
    showConfirmButton: false,
    allowOutsideClick: false,
    background: "#fef3c7",
  });

  try {
    const formData = new FormData();
    formData.append("file", selectedFile.value);
    formData.append("recaptchaToken", recaptchaToken.value);

    const res = await axios.post(
      "https://careershyne.com/api/ai/upload",
      formData,
      { headers: { "Content-Type": "multipart/form-data" } }
    );

    review.value = res.data.review || "No review received.";

    Swal.fire({
      icon: "success",
      title: "AI Review Ready!",
      html: `<pre style="text-align:left; white-space:pre-wrap;">${review.value}</pre>`,
      confirmButtonText: "Close",
      width: 600,
      background: "#fef3c7",
    });
  } catch (err) {
    console.error("Submission failed:", err);
    Swal.fire({
      icon: "error",
      title: "Submission Failed",
      text: "Please try again!",
      background: "#fee2e2",
    });
  } finally {
    submitting.value = false;
  }
}
</script>
