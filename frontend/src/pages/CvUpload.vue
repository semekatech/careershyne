<template>
  <TheWelcome />

  <section
    class="bg-gradient-to-br from-green-50 via-white to-purple-100 py-20"
  >
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

          <!-- hCaptcha -->
          <div class="mt-6 flex justify-center">
            <div id="hcaptcha-container" class="mt-6 flex justify-center"></div>
          </div>

          <!-- Submit Button -->
          <div class="mt-8">
            <button
              class="w-full px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-pink-500 text-white font-semibold hover:opacity-90 transition transform hover:scale-[1.02] shadow-md"
              @click="submitForm"
              :disabled="
                attachmentProgress < 100 || submitting || !hcaptchaToken
              "
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
          <div v-if="review">
            <h3>
              AI CV Review <span class="text-sm">({{ review.score }}/100)</span>
            </h3>


            
            <section>
              <h4 style="color:dodgerblue;font-weight:bold">Strengths</h4>
              <ul>
                <li v-for="(s, i) in review.strengths" :key="'s' + i">
                 - {{ s }}
                </li>
                <li v-if="!review.strengths.length" class="text-muted">
                  No strengths detected.
                </li>
              </ul>

            </section>

            <section>
              <h4 style="color:dodgerblue;font-weight:bold">Weaknesses</h4>
              <ul>
                <li v-for="(w, i) in review.weaknesses" :key="'w' + i">
                  - {{ w }}
                </li>
                <li v-if="!review.weaknesses.length" class="text-muted">
                  No weaknesses detected.
                </li>
              </ul>
            </section>

            <section>
              <h4 style="color:dodgerblue;font-weight:bold">Suggestions</h4>
              <ul>
                <li v-for="(t, i) in review.suggestions" :key="'t' + i">
                 - {{ t }}
                </li>
                <li v-if="!review.suggestions.length" class="text-muted">
                  No suggestions provided.
                </li>
              </ul>
            </section>

            <section>
              <h4 style="color:dodgerblue;font-weight:bold">Overall Impression</h4>
              <p>{{ review.impression }}</p>
            </section>
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
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/AiFooter.vue";
import UploadService from "@/services/UploadService";

const fileInput = ref(null);
const selectedFile = ref(null);
const fileName = ref("");
const attachmentProgress = ref(0);
const submitting = ref(false);
const review = ref("");
const hcaptchaToken = ref(null);

function triggerFileInput() {
  fileInput.value.click();
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  selectedFile.value = file;
  fileName.value = file.name;
  attachmentProgress.value = 0; // reset progress

  const reader = new FileReader();

  reader.onprogress = (e) => {
    if (e.lengthComputable) {
      attachmentProgress.value = Math.round((e.loaded * 100) / e.total);
    }
  };

  reader.onloadend = () => {
    attachmentProgress.value = 100; // finished reading
    // File is ready to be uploaded
  };

  reader.readAsArrayBuffer(file); // start reading the file
}

// hCaptcha callbacks
window.onHCaptchaSuccess = function (token) {
  hcaptchaToken.value = token;
};

window.onHCaptchaExpired = function () {
  hcaptchaToken.value = null;
};

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
    // Upload the file
    const res = await UploadService.uploadFile(
      selectedFile.value,
      hcaptchaToken.value,
      (e) => {
        // ✅ This is the fix: Uncomment the code below to update the progress bar.
        // if (e.lengthComputable) {
        //   attachmentProgress.value = Math.round((e.loaded * 100) / e.total);
        // }
      }
    );

    // Get AI review from response
    review.value = res.data.review || "No review received.";

    Swal.fire({
      icon: "success",
      title: "AI Review Ready!",
      confirmButtonText: "Close",
      width: 600,
      background: "#fef3c7",
    });
  } catch (err) {
    console.error(err);
    Swal.fire({
      icon: "error",
      title: "Submission Failed",
      text: "Please try again!",
      background: "#fee2e2",
    });
  } finally {
    submitting.value = false;
    attachmentProgress.value = 0; // It's a good practice to reset the progress bar after completion.
    selectedFile.value = null; // Also reset the selected file.
    fileName.value = ""; // And the file name.
  }
}

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

  window.hcaptcha.render("hcaptcha-container", {
    sitekey: "4eaee940-28ca-4440-855a-b9eaa88ad3be",
    callback: (token) => (hcaptchaToken.value = token),
    "expired-callback": () => (hcaptchaToken.value = null),
  });
}
</script>
