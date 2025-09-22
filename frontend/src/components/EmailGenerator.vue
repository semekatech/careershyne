<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <section
      class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8 px-6 text-center"
    >
      <h1 class="text-3xl font-bold mb-2">
        AI-Powered Job Application Email Generator 📧
      </h1>
      <p class="max-w-3xl mx-auto text-base opacity-90">
        Instantly create professional job application emails tailored to the role. 
        Just provide the job details and let our AI draft the email for you.
      </p>
    </section>

    <!-- Steps Section -->
    <section class="py-8 px-6">
      <div class="w-full bg-white shadow-lg rounded-lg p-10 space-y-8">
        <!-- Step Indicators -->
        <div class="flex justify-center space-x-4 mb-8">
          <div
            :class="{
              'text-blue-600': currentStep === 1,
              'text-gray-400': currentStep !== 1,
            }"
            class="font-bold text-lg"
          >
            1. Job Details
          </div>
          <span class="text-gray-400">/</span>
          <div
            :class="{
              'text-blue-600': currentStep === 2,
              'text-gray-400': currentStep !== 2,
            }"
            class="font-bold text-lg"
          >
            2. Generate Email
          </div>
        </div>

        <!-- Step 1: Job Details -->
        <div v-if="currentStep === 1" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">Step 1: Provide Job Details</h2>
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
              <!-- <option value="url">Job Link</option> -->
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
            <input
              type="file"
              @change="handleJobFileUpload"
              accept=".pdf"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full 
                     file:border-0 file:text-sm file:font-semibold file:bg-blue-50 
                     file:text-blue-700 hover:file:bg-blue-100"
            />
            <p v-if="jobFileName" class="mt-2 text-sm text-gray-600">
              📂 {{ jobFileName }}
            </p>
          </div>

          <div class="flex justify-end mt-6">
            <button
              @click="submitJobDetails"
              :disabled="!isFormValid"
              :class="{ 'bg-gray-400 cursor-not-allowed': !isFormValid }"
              class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
            >
              Generate Email
            </button>
          </div>
        </div>

        <!-- Step 2: Generated Email -->
        <div v-if="currentStep === 2" class="space-y-6">
          <h2 class="text-xl font-bold text-gray-800">
            Step 2: Your Job Application Email is Ready!
          </h2>

          <div v-if="loading" class="flex flex-col items-center justify-center p-10">
            <div
              class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500"
            ></div>
            <p class="mt-4 text-gray-600">Generating your email...</p>
          </div>

          <div v-else>
            <div class="bg-white p-8 rounded-lg shadow-md border border-gray-200">
              <div
                class="bg-gray-50 border border-gray-200 rounded-md p-6 max-h-[500px] overflow-y-auto"
              >
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                  Generated Email
                </h3>
                <p class="whitespace-pre-wrap font-serif text-gray-900 leading-relaxed">
                  {{ generatedEmail }}
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4 mt-4 justify-end">
              <button
                @click="copyToClipboard"
                class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition"
              >
                Copy to Clipboard
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
import { generateJobEmail } from "@/services/jobEmailService.js";

const currentStep = ref(1);
const loading = ref(false);

const inputType = ref("");
const jobText = ref("");
const jobUrl = ref("");
const jobFile = ref(null);
const jobFileName = ref("");

const generatedEmail = ref("");

const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.length > 0) return true;
  if (inputType.value === "url" && jobUrl.value.length > 0) return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

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
    const data = await generateJobEmail(formData);
    generatedEmail.value = data.email_template;
  } catch (err) {
    console.error("Error generating email:", err);
    generatedEmail.value =
      "An error occurred while generating your job application email. Please try again.";
  } finally {
    loading.value = false;
  }
}

function copyToClipboard() {
  navigator.clipboard
    .writeText(generatedEmail.value)
    .then(() => {
      alert("Email copied to clipboard!");
    })
    .catch((err) => {
      console.error("Failed to copy:", err);
    });
}

function downloadWord() {
  if (!generatedEmail.value) {
    alert("No email to download!");
    return;
  }

  const blob = new Blob([`Job Application Email\n\n${generatedEmail.value}`], {
    type: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
  });

  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "job_application_email.docx";
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
  jobUrl.value = "";
  jobFile.value = null;
  jobFileName.value = "";
  generatedEmail.value = "";
}
</script>
