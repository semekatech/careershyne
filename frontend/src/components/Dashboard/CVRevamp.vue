<template>
  <div>
    <!-- Header -->
    <header
      class="w-full border border-border-light dark:border-border-dark rounded-lg p-8 text-center shadow-sm mb-8 bg-white dark:bg-background-dark"
    >
      <h1
        class="text-3xl font-bold mb-2 flex items-center justify-center text-text-light dark:text-text-dark"
      >
        AI-Powered CV Revamp
        <span class="material-icons ml-2 text-primary-DEFAULT">flash_on</span>
      </h1>
      <p
        class="text-base text-subtext-light dark:text-subtext-dark max-w-2xl mx-auto"
      >
        Provide the job description, and our AI will tailor your CV to highlight
        the most relevant skills and experience.
      </p>
    </header>

    <!-- Steps Wrapper -->
    <section class="bg-card-light dark:bg-card-dark p-8 ">
      <!-- Step indicators -->
      <div class="mb-8">
        <ol class="flex items-center w-full">
          <li
            class="flex w-full items-center text-primary-DEFAULT dark:text-primary-light after:content-[''] after:w-full after:h-1 after:border-b after:border-primary-DEFAULT dark:after:border-primary-light after:border-4 after:inline-block"
          >
            <span
              class="flex items-center justify-center w-10 h-10 bg-primary-DEFAULT rounded-full lg:h-12 lg:w-12 text-white shrink-0"
            >
              1
            </span>
          </li>
          <li
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-border-light dark:after:border-border-dark after:border-4 after:inline-block"
          >
            <span
              class="flex items-center justify-center w-10 h-10 bg-border-light dark:bg-border-dark rounded-full lg:h-12 lg:w-12 text-subtext-light dark:text-subtext-dark shrink-0"
            >
              2
            </span>
          </li>
          <li class="flex items-center">
            <span
              class="flex items-center justify-center w-10 h-10 bg-border-light dark:bg-border-dark rounded-full lg:h-12 lg:w-12 text-subtext-light dark:text-subtext-dark shrink-0"
            >
              3
            </span>
          </li>
        </ol>
        <div
          class="flex justify-between mt-2 text-sm font-medium text-subtext-light dark:text-subtext-dark"
        >
          <span class="text-primary-DEFAULT dark:text-primary-light"
            >Job Details</span
          >
          <span>Revamped CV</span>
          <span>Download</span>
        </div>
      </div>

      <!-- Job Description Step -->
      <div>
        <h2
          class="text-2xl font-semibold text-text-light dark:text-text-dark mb-2"
        >
          Step 1: Provide Job Description
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Paste the job description or upload it as a PDF/DOC.
        </p>

        <!-- Input method -->
        <div class="mb-6">
          <label class="sr-only" for="input-method">Input Method</label>
          <div class="relative">
            <select
              id="input-method"
              v-model="inputType"
              class="w-full appearance-none bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg py-3 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent"
            >
              <option value="text">Paste Job Description</option>
              <option value="pdf">Upload PDF/DOC</option>
            </select>
            <div
              class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-subtext-light dark:text-subtext-dark"
            >
              <span class="material-icons">expand_more</span>
            </div>
          </div>
        </div>

        <!-- Job description text (default) -->
        <div v-if="inputType === 'text'">
          <label class="sr-only" for="job-description"
            >Paste job description here...</label
          >
          <textarea
            id="job-description"
            v-model="jobText"
            rows="10"
            class="w-full bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent"
            placeholder="Paste job description here..."
          ></textarea>
        </div>

        <!-- Upload field -->
        <div v-else-if="inputType === 'pdf'" class="mt-4">
          <div
            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center cursor-pointer"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="fileInput.click()"
          >
            <!-- <span class="material-icons-sharp text-5xl text-primary mb-4">edit_document</span> -->
            <p class="font-semibold mb-2">Drag and drop your CV or job description here</p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">
              PDF, DOC, DOCX up to 5MB
            </p>
            <button
              type="button"
              class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
              @click.stop="fileInput.click()"
            >
              Or browse files
            </button>
            <p v-if="jobFileName" class="mt-4 text-sm text-text-light dark:text-text-dark">
              Selected file: {{ jobFileName }}
            </p>
            <!-- Hidden file input -->
            <input
              type="file"
              ref="fileInput"
              accept=".pdf,.doc,.docx"
              class="hidden"
              @change="handleJobFileUpload"
            />
          </div>
        </div>

        <!-- Submit button -->
        <div class="flex justify-end mt-8">
          <button
            @click="submitJobDetails"
            :disabled="!isFormValid || loading"
            class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 disabled:opacity-50"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Revamp My CV</span>
          </button>
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

// ✅ Default to text input
const inputType = ref("text");
const jobText = ref("");
const jobFile = ref(null);
const jobFileName = ref("");
const fileInput = ref(null);

const revampedCv = ref("");

// Validation rules
const allowedTypes = ["application/pdf", 
  "application/msword", 
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
];
const maxSize = 5 * 1024 * 1024; // 5MB

const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.length > 0) return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

function handleJobFileUpload(event) {
  const file = event.target.files[0];
  validateFile(file);
}

function handleDrop(event) {
  const file = event.dataTransfer.files[0];
  validateFile(file);
}

function validateFile(file) {
  if (!file) return;
  if (!allowedTypes.includes(file.type)) {
    alert("❌ Invalid file type. Please upload PDF, DOC, or DOCX.");
    return;
  }
  if (file.size > maxSize) {
    alert("❌ File too large. Maximum allowed size is 5MB.");
    return;
  }
  jobFile.value = file;
  jobFileName.value = file.name;
}

async function submitJobDetails() {
  loading.value = true;
  currentStep.value = 2;

  const formData = new FormData();
  if (inputType.value === "text") {
    formData.append("job_text", jobText.value);
  }
  if (inputType.value === "pdf" && jobFile.value) {
    formData.append("job_file", jobFile.value);
  }

  try {
    const data = await generateCvRevamp(formData);

    if (!data.success) {
      // Backend returned failure
      revampedCv.value = `❌ ${data.message}`;
      return;
    }

    revampedCv.value = data.revamped_cv;
  } catch (err) {
    console.error("Error revamping CV:", err);

    if (err.response && err.response.data) {
      // Laravel validation errors
      if (err.response.data.errors) {
        revampedCv.value =
          "❌ " + Object.values(err.response.data.errors).flat().join(" ");
      } else if (err.response.data.message) {
        revampedCv.value = "❌ " + err.response.data.message;
      }
    } else {
      revampedCv.value =
        "❌ An unexpected error occurred. Please try again later.";
    }
  } finally {
    loading.value = false;
  }
}


</script>
