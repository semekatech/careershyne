<template>
  <div class="p-4">
    <!-- Header -->
    <header
      class="w-full border border-border-light dark:border-border-dark rounded-lg p-8 text-center shadow-sm mb-8 bg-white dark:bg-background-dark"
    >
      <h1
        class="text-3xl font-bold mb-2 flex items-center justify-center text-text-light dark:text-text-dark"
      >
        AI-Powered Cover Letter Generator
        <span class="material-icons ml-2 text-primary">flash_on</span>
      </h1>
      <p
        class="text-base text-subtext-light dark:text-subtext-dark max-w-2xl mx-auto"
      >
        Provide the job description, and our AI will craft a cover letter
        tailored to highlight your most relevant skills and experience.
      </p>
    </header>

    <!-- Steps -->
    <section class="bg-card-light dark:bg-card-dark p-8 rounded-lg">
      <div class="mb-8">
        <ol class="flex items-center w-full">
          <li
            :class="{
              'text-primary dark:text-primary-light after:border-primary dark:after:border-primary-light': currentStep >= 1,
              'after:border-border-light dark:after:border-border-dark': currentStep < 1
            }"
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block"
          >
            <span
              :class="{
                'bg-primary text-white': currentStep >= 1,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark': currentStep < 1
              }"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
            >
              1
            </span>
          </li>
          <li
            :class="{
              'text-primary dark:text-primary-light after:border-primary dark:after:border-primary-light': currentStep >= 2,
              'after:border-border-light dark:after:border-border-dark': currentStep < 2
            }"
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block"
          >
            <span
              :class="{
                'bg-primary text-white': currentStep >= 2,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark': currentStep < 2
              }"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
            >
              2
            </span>
          </li>
          <li class="flex items-center">
            <span
              :class="{
                'bg-primary text-white': currentStep >= 3,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark': currentStep < 3
              }"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
            >
              3
            </span>
          </li>
        </ol>

        <div class="flex justify-between mt-2 text-sm font-medium text-subtext-light dark:text-subtext-dark">
          <span :class="{ 'text-primary dark:text-primary-light': currentStep >= 1 }">Job Details</span>
          <span :class="{ 'text-primary dark:text-primary-light': currentStep >= 2 }">Cover Letter</span>
          <span :class="{ 'text-primary dark:text-primary-light': currentStep >= 3 }">Download</span>
        </div>
      </div>

      <!-- Step 1: Input -->
      <div v-if="currentStep === 1">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-2">
          Step 1: Provide Job Description
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Paste the job description or upload it as a PDF/DOC.
        </p>

        <!-- Input Method -->
        <div class="mb-6">
          <select
            v-model="inputType"
            class="w-full appearance-none bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg py-3 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          >
            <option value="text">Paste Job Description</option>
            <option value="pdf">Upload PDF/DOC</option>
          </select>
        </div>

        <!-- Text Input -->
        <div v-if="inputType === 'text'">
          <textarea
            v-model="jobText"
            rows="10"
            class="w-full bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Paste job description here..."
          ></textarea>
        </div>

        <!-- File Upload -->
        <div v-else-if="inputType === 'pdf'" class="mt-4">
          <div
            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center cursor-pointer"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="fileInput.click()"
          >
            <p class="font-semibold mb-2">Drag and drop your job description here</p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">PDF, DOC, DOCX up to 2MB</p>
            <button type="button"
              class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
              @click.stop="fileInput.click()"
            >
              Or browse files
            </button>
            <p v-if="jobFileName" class="mt-4 text-sm text-text-light dark:text-text-dark">
              Selected file: {{ jobFileName }}
            </p>
            <input type="file" ref="fileInput" accept=".pdf,.doc,.docx" class="hidden" @change="handleJobFileUpload" />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <button
            @click="submitJobDetails"
            :disabled="!isFormValid || loading"
            class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 disabled:opacity-50"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Generate Cover Letter</span>
          </button>
        </div>
      </div>

      <!-- Step 2: Display -->
      <div v-else-if="currentStep === 2 && coverLetterContent">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4">Step 2: Your Cover Letter</h2>

        <div class="prose dark:prose-invert max-w-full overflow-x-auto p-4 border rounded-lg bg-background-light dark:bg-background-dark">
          <div v-html="formattedCoverLetter"></div>
        </div>

        <div class="flex justify-between mt-6">
          <button @click="goToStep(1)" class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-bold py-2 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
            Go Back & Change Job Details
          </button>
          <button @click="goToStep(3)" class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
            Finalize & Download Cover Letter
            <span class="material-icons ml-2">arrow_forward</span>
          </button>
        </div>
      </div>

      <!-- Step 3: Download -->
      <div v-else-if="currentStep === 3">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4">Step 3: Download Your Cover Letter</h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">Your cover letter is ready! Download the file below.</p>

        <div class="flex justify-between mt-6">
          <button @click="goToStep(2)" class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-bold py-2 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
            <span class="material-icons mr-2">arrow_back</span> Review Cover Letter
          </button>
          <button @click="downloadAsWord" class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
            <span class="material-icons mr-2">download</span> Download Cover Letter as Word
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { generateCoverLetter } from "@/services/coverLetterService.js";

// --- Step State ---
const currentStep = ref(1);
const loading = ref(false);

// --- Input ---
const inputType = ref("text");
const jobText = ref("");
const jobFile = ref(null);
const jobFileName = ref("");
const fileInput = ref(null);

// --- Result ---
const coverLetterContent = ref("");

// --- Form Validation ---
const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.trim()) return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

// --- File Upload Limit ---
const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

function handleJobFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > MAX_FILE_SIZE) {
    alert("File is too large. Maximum allowed size is 2 MB.");
    return;
  }

  jobFile.value = file;
  jobFileName.value = file.name;
}

function handleDrop(event) {
  const file = event.dataTransfer.files[0];
  if (!file) return;

  if (file.size > MAX_FILE_SIZE) {
    alert("File is too large. Maximum allowed size is 2 MB.");
    return;
  }

  jobFile.value = file;
  jobFileName.value = file.name;
}

// --- Navigation ---
function goToStep(step) {
  currentStep.value = step;
}

// --- Submit ---
async function submitJobDetails() {
  coverLetterContent.value = "";
  loading.value = true;

  const formData = new FormData();
  if (inputType.value === "text") formData.append("job_text", jobText.value);
  if (inputType.value === "pdf" && jobFile.value) formData.append("job_file", jobFile.value);

  try {
    const data = await generateCoverLetter(formData);

    if (data.success && data.cover_letter) {
      coverLetterContent.value = data.cover_letter;
      currentStep.value = 2;
    } else {
      coverLetterContent.value = `❌ Error: ${data.message || "Cover letter generation failed."}`;
      currentStep.value = 2;
    }
  } catch (err) {
    console.error("Error generating cover letter:", err);
    let errorMessage = "❌ An unexpected error occurred. Please try again later.";
    if (err.response && err.response.data) {
      const apiErrors = err.response.data.errors
        ? Object.values(err.response.data.errors).flat().join(" ")
        : null;
      errorMessage = `❌ ${err.response.data.message || apiErrors || "Server error occurred."}`;
    }
    coverLetterContent.value = `Error: ${errorMessage}`;
    currentStep.value = 2;
  } finally {
    loading.value = false;
  }
}

// --- Format ---
const formattedCoverLetter = computed(() => {
  if (!coverLetterContent.value) return "";

  let html = coverLetterContent.value;

  // Clean and convert simple markdown/bullet points
  html = html.replace(/^\s*---\s*$/gm, "");
  html = html.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
  html = html.replace(/^\s*<strong>([^<]+)<\/strong>\s*$/gm, "<h2>$1</h2>");
  html = html.replace(/^\s*-\s*(\w+):\s*(.*)$/gm, "<li><strong>$1:</strong> $2</li>");
  html = html.replace(/^\s*-\s*(.+)$/gm, "<li>$1</li>");
  html = html.replace(/(<li>.+?<\/li>(\s*<li>.+?<\/li>)*)/gs, "<ul>$1</ul>");
  html = html.replace(/\n\s*\n/g, "</p><p>");
  html = html.replace(/\n/g, "<br>");
  html = html.split("<br>").map(line => line.trim()).map(line => {
    if (line && !line.startsWith("<h") && !line.startsWith("<ul") && !line.startsWith("<p") && !line.startsWith("<strong")) return `<p>${line}</p>`;
    return line;
  }).join("");
  html = html.replace(/<p>\s*<\/p>/g, "");
  if (html.length && !html.startsWith("<p") && !html.startsWith("<h") && !html.startsWith("<ul")) html = `<p>${html}`;
  if (html.length && !html.endsWith("</p>") && !html.endsWith("</ul") && !html.endsWith("</h1>") && !html.endsWith("</h2>") && !html.endsWith("</h3>")) html = `${html}</p>`;

  return html.trim();
});

// --- Download Word ---
function downloadAsWord() {
  if (!formattedCoverLetter.value) return;

  const preHtml = `
    <html xmlns:o='urn:schemas-microsoft-com:office:office' 
          xmlns:w='urn:schemas-microsoft-com:office:word' 
          xmlns='http://www.w3.org/TR/REC-html40'>
    <head><meta charset='utf-8'><title>Cover Letter</title></head><body>`;
  const postHtml = "</body></html>";

  const blob = new Blob(["\ufeff", preHtml + formattedCoverLetter.value + postHtml], { type: "application/msword" });

  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "cover_letter.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}
</script>
