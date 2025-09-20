<template>
  <div class="min-h-screen bg-gray-50">
    <section class="bg-gradient-to-r from-orange-600 to-yellow-700 text-white py-8 px-6 text-center">
      <h1 class="text-3xl font-bold mb-2">AI-Powered Cover Letter Generator ✍️</h1>
      <p class="max-w-3xl mx-auto text-base opacity-90">
        Create tailored cover letters instantly. Follow the steps below to craft a professional cover letter just for you.
      </p>
    </section>

    <section class="py-8 px-6">
      <div class="w-full bg-white shadow-lg rounded-lg p-10 space-y-8">
        <div class="flex justify-center space-x-4 mb-8">
          <div :class="{'text-blue-600': currentStep === 1, 'text-gray-400': currentStep !== 1}" class="font-bold text-lg">1. Upload CV</div>
          <span class="text-gray-400">/</span>
          <div :class="{'text-blue-600': currentStep === 2, 'text-gray-400': currentStep !== 2}" class="font-bold text-lg">2. Job Details</div>
          <span class="text-gray-400">/</span>
          <div :class="{'text-blue-600': currentStep === 3, 'text-gray-400': currentStep !== 3}" class="font-bold text-lg">3. Generate</div>
        </div>

        <div v-if="currentStep === 1" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">Step 1: Upload Your CV</h2>
          <p class="text-gray-600">
            Upload your CV or resume. Our AI will use it to highlight your relevant skills and experience in the cover letter.
          </p>
          <div>
            <label class="block text-gray-700 font-medium mb-2">Upload CV (PDF, DOCX)</label>
            <input type="file" @change="handleCvUpload" accept=".pdf,.doc,.docx" class="block w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4
              file:rounded-full file:border-0
              file:text-sm file:font-semibold
              file:bg-orange-50 file:text-orange-700
              hover:file:bg-orange-100"
            />
            <p v-if="cvFileName" class="mt-2 text-sm text-gray-600">
              📂 {{ cvFileName }}
            </p>
          </div>
          <div class="flex justify-end">
            <button
              @click="nextStep"
              :disabled="!cvFile"
              :class="{'bg-gray-400 cursor-not-allowed': !cvFile}"
              class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
            >
              Next
            </button>
          </div>
        </div>

        <div v-if="currentStep === 2" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">Step 2: Provide Job Details</h2>
          <p class="text-gray-600">
            Choose one of the available input methods below to provide the job description.
          </p>

          <div>
            <label class="block text-gray-700 font-medium mb-2">Input Type</label>
            <select
              v-model="inputType"
              class="w-full border rounded-lg p-3 focus:ring focus:ring-blue-300"
            >
              <option value="">-- Select Input Method --</option>
              <option value="text">Paste Job Description</option>
              <option value="url">Job Link</option>
              <option value="pdf">Upload PDF</option>
            </select>
          </div>

          <div v-if="inputType === 'text'">
            <label class="block text-gray-700 font-medium mb-2">Job Description</label>
            <textarea
              v-model="jobText"
              rows="8"
              class="w-full border rounded-lg p-3 focus:ring focus:ring-blue-300"
              placeholder="Paste job description here..."
            ></textarea>
          </div>

          <div v-if="inputType === 'url'">
            <label class="block text-gray-700 font-medium mb-2">Job Link</label>
            <input
              v-model="jobUrl"
              type="url"
              class="w-full border rounded-lg p-3 focus:ring focus:ring-blue-300"
              placeholder="https://example.com/job-details"
            />
          </div>

          <div v-if="inputType === 'pdf'">
            <label class="block text-gray-700 font-medium mb-2">Upload Job PDF</label>
            <input type="file" @change="handleJobFileUpload" accept="application/pdf" class="block w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4
              file:rounded-full file:border-0
              file:text-sm file:font-semibold
              file:bg-orange-50 file:text-orange-700
              hover:file:bg-orange-100"
            />
            <p v-if="jobFileName" class="mt-2 text-sm text-gray-600">
              📂 {{ jobFileName }}
            </p>
          </div>

          <div class="flex justify-between mt-6">
            <button @click="prevStep" class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
              Back
            </button>
            <button
              @click="submitJobDetails"
              :disabled="!isFormValid"
              :class="{'bg-gray-400 cursor-not-allowed': !isFormValid}"
              class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
            >
              Generate Cover Letter
            </button>
          </div>
        </div>

        <div v-if="currentStep === 3" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">Step 3: Your Cover Letter is Ready!</h2>
          <div v-if="loading" class="flex flex-col items-center justify-center p-10">
            <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500"></div>
            <p class="mt-4 text-gray-600">Generating your professional cover letter...</p>
          </div>
          <div v-else>
            <textarea
              v-model="generatedCoverLetter"
              rows="20"
              class="w-full border rounded-lg p-4 font-mono text-sm resize-none"
              readonly
            ></textarea>
            <div class="flex justify-end mt-4">
              <button
                @click="copyToClipboard"
                class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition"
              >
                Copy to Clipboard
              </button>
            </div>
          </div>
          <div class="flex justify-start mt-6">
            <button @click="resetForm" class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
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
import { generateCoverLetter } from "./services/coverletter.js";

const currentStep = ref(1);
const loading = ref(false);

const cvFile = ref(null);
const cvFileName = ref("");

const inputType = ref("");
const jobText = ref("");
const jobUrl = ref("");
const jobFile = ref(null);
const jobFileName = ref("");

const generatedCoverLetter = ref("");

const isFormValid = computed(() => {
  if (inputType.value === 'text' && jobText.value.length > 0) return true;
  if (inputType.value === 'url' && jobUrl.value.length > 0) return true;
  if (inputType.value === 'pdf' && jobFile.value) return true;
  return false;
});

function handleCvUpload(event) {
  const file = event.target.files[0];
  if (file) {
    cvFile.value = file;
    cvFileName.value = file.name;
  }
}

function handleJobFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    jobFile.value = file;
    jobFileName.value = file.name;
  }
}

function nextStep() {
  currentStep.value++;
}

function prevStep() {
  currentStep.value--;
  if (currentStep.value < 1) currentStep.value = 1;
}

async function submitJobDetails() {
  loading.value = true;
  currentStep.value = 3;

  const formData = new FormData();
  formData.append("cv_file", cvFile.value);

  if (inputType.value === "text" && jobText.value) {
    formData.append("job_text", jobText.value);
  }
  if (inputType.value === "url" && jobUrl.value) {
    formData.append("job_url", jobUrl.value);
  }
  if (inputType.value === "pdf" && jobFile.value) {
    formData.append("job_pdf", jobFile.value);
  }
  try {
    const data = await generateCoverLetter(formData);
    generatedCoverLetter.value = data.cover_letter;
  } catch (err) {
    console.error("Error generating cover letter:", err);
    generatedCoverLetter.value = "An error occurred while generating your cover letter. Please try again.";
  } finally {
    loading.value = false;
  }
}

function copyToClipboard() {
  navigator.clipboard.writeText(generatedCoverLetter.value)
    .then(() => {
      alert("Cover letter copied to clipboard!");
    })
    .catch(err => {
      console.error("Failed to copy:", err);
    });
}

function resetForm() {
  currentStep.value = 1;
  loading.value = false;
  cvFile.value = null;
  cvFileName.value = "";
  inputType.value = "";
  jobText.value = "";
  jobUrl.value = "";
  jobFile.value = null;
  jobFileName.value = "";
  generatedCoverLetter.value = "";
}
</script>