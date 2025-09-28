<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <section
      class="bg-gradient-to-r from-purple-600 to-indigo-700 text-white py-8 px-6 text-center"
    >
      <h1 class="text-3xl font-bold mb-2">AI-Powered CV Revamp ⚡</h1>
      <p class="max-w-3xl mx-auto text-base opacity-90">
        Provide the job description, and our AI will tailor your CV to highlight
        the most relevant skills and experience.
      </p>
    </section>

    <!-- Steps Section -->
    <section class="py-8 px-6">
      <div class="w-full bg-white shadow-lg rounded-lg p-10 space-y-8">
        <!-- Step Indicators -->
        <div class="flex justify-center space-x-4 mb-8">
          <div :class="stepClass(1)" class="font-bold text-lg">
            1. Job Details
          </div>
          <span class="text-gray-400">/</span>
          <div :class="stepClass(2)" class="font-bold text-lg">
            2. Revamped CV
          </div>
        </div>

        <!-- Step 1: Job Details -->
        <div v-if="currentStep === 1" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">
            Step 1: Provide Job Description
          </h2>
          <p class="text-gray-600">
            Paste the job description or upload it as a PDF/Image.
          </p>

          <select
            v-model="inputType"
            class="w-full border rounded-lg p-3 focus:ring focus:ring-indigo-300"
          >
            <option value="">-- Select Input Method --</option>
            <option value="text">Paste Job Description</option>
            <option value="pdf">Upload PDF/Image</option>
          </select>

          <!-- Paste Job Description -->
          <div v-if="inputType === 'text'">
            <textarea
              v-model="jobText"
              rows="8"
              class="w-full bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 focus:ring-primary focus:border-primary"
              placeholder="Paste job description here..."
            ></textarea>
          </div>

          <!-- Upload Job Description File -->
          <div v-if="inputType === 'pdf'">
            <input
              type="file"
              @change="handleJobFileUpload"
              accept=".pdf,.jpg,.jpeg,.png"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full 
                     file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 
                     hover:file:bg-purple-100"
            />
            <p v-if="jobFileName" class="mt-2 text-sm text-gray-600">
              📂 {{ jobFileName }}
            </p>
          </div>

          <div class="flex justify-end mt-6">
            <button
              @click="submitJobDetails"
              :disabled="!isFormValid"
              class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold 
                     hover:bg-indigo-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
            >
              Revamp My CV
            </button>
          </div>
        </div>

        <!-- Step 2: Revamped CV -->
        <div v-if="currentStep === 2" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">
            Step 2: Your Revamped CV
          </h2>
          <div v-if="loading" class="flex flex-col items-center justify-center p-10">
            <div
              class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-indigo-500"
            ></div>
            <p class="mt-4 text-gray-600">Revamping your CV...</p>
          </div>
          <div v-else>
            <div
              class="bg-gray-50 border border-gray-200 rounded-md p-6 max-h-[500px] overflow-y-auto"
            >
              <pre
                class="whitespace-pre-wrap font-serif text-gray-900 leading-relaxed"
              >
                {{ revampedCv }}
              </pre>
            </div>

            <div class="flex space-x-4 mt-4 justify-end">
              <button
                @click="copyToClipboard"
                class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition"
              >
                Copy
              </button>
              <button
                @click="downloadWord"
                class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition"
              >
                Download Word
              </button>
            </div>
          </div>
          <div class="flex justify-start mt-6">
            <button
              @click="resetForm"
              class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
            >
              Start Over
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { generateCvRevamp } from "@/services/cvRevampService.js";

const currentStep = ref(1);
const loading = ref(false);

const inputType = ref("");
const jobText = ref("");
const jobFile = ref(null);
const jobFileName = ref("");

const revampedCv = ref("");

const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.length > 0) return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

function stepClass(step) {
  return {
    "text-indigo-600": currentStep.value === step,
    "text-gray-400": currentStep.value !== step,
  };
}

function handleJobFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    jobFile.value = file;
    jobFileName.value = file.name;
  }
}

async function submitJobDetails() {
  loading.value = true;
  currentStep.value = 2;

  const formData = new FormData();
  // ✅ No CV upload, backend should use logged-in user CV
  if (inputType.value === "text") {
    formData.append("job_text", jobText.value);
  }
  if (inputType.value === "pdf" && jobFile.value) {
    formData.append("job_pdf", jobFile.value);
  }

  try {
    const data = await generateCvRevamp(formData);
    revampedCv.value = data.revamped_cv;
  } catch (err) {
    console.error("Error revamping CV:", err);
    revampedCv.value =
      "❌ An error occurred while revamping your CV. Please try again.";
  } finally {
    loading.value = false;
  }
}

function copyToClipboard() {
  navigator.clipboard.writeText(revampedCv.value).then(() => {
    alert("Revamped CV copied to clipboard!");
  });
}

function downloadWord() {
  if (!revampedCv.value) {
    alert("No CV to download!");
    return;
  }
  const blob = new Blob([revampedCv.value], {
    type: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
  });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "revamped_cv.docx";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

function resetForm() {
  currentStep.value = 1;
  loading.value = false;
  inputType.value = "";
  jobText.value = "";
  jobFile.value = null;
  jobFileName.value = "";
  revampedCv.value = "";
}
</script>
