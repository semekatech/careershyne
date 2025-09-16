<template>
  <TheWelcome />
  <section class="bg-white dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="grid md:grid-cols-2 gap-12 items-start">
        <!-- LEFT: Upload CV Form -->
        <div class="bg-orange-50 dark:bg-gray-800 rounded-2xl shadow-lg p-10">
          <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
            Upload Your CV
          </h3>
          <p class="text-gray-700 dark:text-gray-300 mb-6">
            Drag & drop your CV here, or click to select a file.
          </p>

          <!-- Drag & Drop Zone -->
          <div
            class="border-2 border-dashed border-orange-400 rounded-xl p-10 flex flex-col items-center justify-center cursor-pointer hover:bg-orange-100 dark:hover:bg-gray-700 transition"
            @click="triggerFileInput"
          >
            <svg
              class="w-12 h-12 text-orange-500 mb-3"
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
            <p class="text-gray-800 dark:text-gray-200">
              Drop your file here or
              <span class="font-medium text-orange-500">browse</span>
            </p>
            <input
              type="file"
              ref="fileInput"
              accept="application/pdf"
              class="hidden"
              @change="handleFileUpload"
            />
          </div>

          <!-- File Info -->
          <div v-if="fileName" class="mt-4">
            <p class="text-sm text-gray-700 dark:text-gray-300">
              Selected File: <strong>{{ fileName }}</strong>
            </p>

            <!-- Progress Bar -->
            <div
              class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2"
            >
              <div
                class="bg-orange-500 h-2 rounded-full transition-all duration-500"
                :style="{ width: progress + '%' }"
              ></div>
            </div>
            <p class="text-xs mt-1 text-gray-500">{{ progress }}%</p>
          </div>

          <!-- CTA -->
          <div class="mt-6">
            <button
              class="px-6 py-3 rounded-xl bg-orange-500 text-white hover:bg-gray-900 hover:text-orange-400 transition font-medium shadow-md"
              @click="uploadFile"
              :disabled="!selectedFile || uploading"
            >
              {{ uploading ? "Uploading..." : "Submit CV" }}
            </button>
          </div>
        </div>

        <!-- RIGHT: Blank Space for Future Details -->
        <div
          class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-10 min-h-[350px] flex items-center justify-center"
        >
          <p class="text-gray-400 dark:text-gray-500 italic">
            CV details & feedback will appear here...
          </p>
        </div>
      </div>
    </div>
  </section>

  <FooterSection />
</template>
<script setup>
import { ref } from "vue";
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
import UploadService from "@/services/UploadService";
const fileInput = ref(null);
const selectedFile = ref(null);
const fileName = ref("");
const progress = ref(0);
const uploading = ref(false);

function triggerFileInput() {
  fileInput.value.click();
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  selectedFile.value = file;
  fileName.value = file.name;
  progress.value = 0;
}

async function uploadFile() {
  if (!selectedFile.value) return;

  uploading.value = true;
  progress.value = 0;

  try {
    const response = await UploadService.uploadFile(selectedFile.value, (e) => {
      if (e.lengthComputable) {
        progress.value = Math.round((e.loaded * 100) / e.total);
      }
    });

    console.log("Upload successful:", response.data);
    alert("Upload complete!");
  } catch (error) {
    console.error("Upload failed:", error);
    alert("Upload failed!");
  } finally {
    uploading.value = false;
  }
}
</script>
