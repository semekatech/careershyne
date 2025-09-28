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
    <section class="bg-card-light dark:bg-card-dark p-8">
      <!-- Step Indicators -->
      <div class="mb-8">
        <ol class="flex items-center w-full">
          <!-- Step 1 -->
          <li
            class="flex w-full items-center text-primary-DEFAULT dark:text-primary-light after:content-[''] after:w-full after:h-1 after:border-b after:border-primary-DEFAULT dark:after:border-primary-light after:border-4 after:inline-block"
          >
            <span
              class="flex items-center justify-center w-10 h-10 bg-primary-DEFAULT rounded-full lg:h-12 lg:w-12 text-white shrink-0"
            >
              1
            </span>
          </li>

          <!-- Step 2 -->
          <li
            class="flex w-full items-center"
            :class="[currentStep >= 2
              ? 'text-primary-DEFAULT dark:text-primary-light after:border-primary-DEFAULT dark:after:border-primary-light'
              : 'after:border-border-light dark:after:border-border-dark']"
          >
            <span
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
              :class="{
                'bg-primary-DEFAULT text-white': currentStep >= 2,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark': currentStep < 2,
              }"
            >
              2
            </span>
          </li>

          <!-- Step 3 -->
          <li class="flex items-center">
            <span
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
              :class="{
                'bg-primary-DEFAULT text-white': currentStep === 3,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark': currentStep < 3,
              }"
            >
              3
            </span>
          </li>
        </ol>

        <!-- Step labels -->
        <div class="flex justify-between mt-2 text-sm font-medium text-subtext-light dark:text-subtext-dark">
          <span :class="{ 'text-primary-DEFAULT dark:text-primary-light': currentStep >= 1 }">Job Details</span>
          <span :class="{ 'text-primary-DEFAULT dark:text-primary-light': currentStep >= 2 }">Revamped CV</span>
          <span :class="{ 'text-primary-DEFAULT dark:text-primary-light': currentStep >= 3 }">Download</span>
        </div>
      </div>

      <!-- Step 1: Job Input -->
      <div v-if="currentStep === 1">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-2">
          Step 1: Provide Job Description
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Paste the job description or upload it as a PDF/DOC.
        </p>

        <!-- Input Method -->
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
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-subtext-light dark:text-subtext-dark">
              <span class="material-icons">expand_more</span>
            </div>
          </div>
        </div>

        <!-- Text Input -->
        <textarea
          v-if="inputType === 'text'"
          v-model="jobText"
          rows="10"
          class="w-full bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent"
          placeholder="Paste job description here..."
        ></textarea>

        <!-- File Upload -->
        <div v-else class="mt-4">
          <div
            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center cursor-pointer"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="fileInput.click()"
          >
            <p class="font-semibold mb-2">Drag and drop your CV or job description here</p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">PDF, DOC, DOCX up to 5MB</p>
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
            <input
              type="file"
              ref="fileInput"
              accept=".pdf,.doc,.docx"
              class="hidden"
              @change="handleJobFileUpload"
            />
          </div>
        </div>

        <!-- Submit -->
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

      <!-- Step 2: Editable CV -->
      <div v-if="currentStep === 2" class="mt-6">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4">
          Step 2: Your Revamped CV
        </h2>

        <!-- Loader -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <span class="animate-pulse text-lg text-primary">Generating CV...</span>
        </div>

        <!-- Editable Content -->
        <div v-else>
          <QuillEditor
            v-model:content="revampedCv"
            contentType="html"
            theme="snow"
            class="bg-white dark:bg-background-dark border border-border-light dark:border-border-dark rounded-lg"
            style="min-height: 400px"
          />
        </div>

        <div class="flex justify-end mt-6" v-if="!loading">
          <button
            class="bg-primary hover:bg-primary-light text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-300"
            @click="currentStep = 3"
          >
            Continue to Download
          </button>
        </div>
      </div>

      <!-- Step 3: Download -->
      <div v-if="currentStep === 3" class="mt-6">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4">
          Step 3: Download Your Revamped CV
        </h2>

        <div class="flex gap-4">
          <button
            @click="downloadPDF"
            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition"
          >
            Download as PDF
          </button>
          <button
            @click="downloadDOCX"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition"
          >
            Download as DOCX
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { generateCvRevamp } from "@/services/cvRevampService.js";
import html2pdf from "html2pdf.js";
import { Document, Packer, Paragraph, TextRun } from "docx";
import { saveAs } from "file-saver";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

const currentStep = ref(1);
const loading = ref(false);

// Input handling
const inputType = ref("text");
const jobText = ref("");
const jobFile = ref(null);
const jobFileName = ref("");
const fileInput = ref(null);

const revampedCv = ref("");

// Validation
const allowedTypes = [
  "application/pdf",
  "application/msword",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
];
const maxSize = 5 * 1024 * 1024;

const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.length > 0) return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

// File handling
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

// Submit
async function submitJobDetails() {
  loading.value = true;
  revampedCv.value = "";
  currentStep.value = 2;

  const formData = new FormData();
  if (inputType.value === "text") formData.append("job_text", jobText.value);
  if (inputType.value === "pdf" && jobFile.value)
    formData.append("job_file", jobFile.value);

  try {
    const data = await generateCvRevamp(formData);
    if (!data.success) {
      revampedCv.value = `❌ ${data.message}`;
      return;
    }
    revampedCv.value = data.revamped_cv;
  } catch (err) {
    console.error("Error revamping CV:", err);
    revampedCv.value =
      err.response?.data?.message ||
      (err.response?.data?.errors &&
        Object.values(err.response.data.errors).flat().join(" ")) ||
      "❌ An unexpected error occurred. Please try again later.";
  } finally {
    loading.value = false;
  }
}

// Download PDF
function downloadPDF() {
  const container = document.createElement("div");
  container.innerHTML = revampedCv.value;
  html2pdf().from(container).save("revamped_cv.pdf");
}

// Download DOCX
async function downloadDOCX() {
  const plainText = revampedCv.value
    .replace(/<[^>]+>/g, "")
    .split("\n")
    .filter((line) => line.trim() !== "");

  const paragraphs = plainText.map(
    (line) =>
      new Paragraph({
        children: [new TextRun(line)],
      })
  );

  const doc = new Document({
    sections: [{ properties: {}, children: paragraphs }],
  });

  const blob = await Packer.toBlob(doc);
  saveAs(blob, "revamped_cv.docx");
}
</script>
