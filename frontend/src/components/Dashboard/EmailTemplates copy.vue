<template>
  <div class="p-4">
    <!-- Header -->
    <header
      class="w-full border border-border-light dark:border-border-dark rounded-lg p-8 text-center shadow-sm mb-8 bg-white dark:bg-background-dark"
    >
      <h1
        class="text-3xl font-bold mb-2 flex items-center justify-center text-text-light dark:text-text-dark"
      >
        AI-Powered Email Template Generator
        <span class="material-icons ml-2 text-primary">email</span>
      </h1>
      <p
        class="text-base text-subtext-light dark:text-subtext-dark max-w-2xl mx-auto"
      >
        Provide the context or job description, and our AI will craft a professional email tailored to your needs.
      </p>
    </header>

    <section class="bg-card-light dark:bg-card-dark p-8 rounded-lg">
      <!-- Stepper -->
      <div class="mb-8">
        <ol class="flex items-center w-full">
          <li
            :class="{
              'text-primary dark:text-primary-light after:border-primary dark:after:border-primary-light':
                currentStep >= 1,
              'after:border-border-light dark:after:border-border-dark':
                currentStep < 1,
            }"
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block"
          >
            <span
              :class="{
                'bg-primary text-white': currentStep >= 1,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark':
                  currentStep < 1,
              }"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
            >
              1
            </span>
          </li>
          <li
            :class="{
              'text-primary dark:text-primary-light after:border-primary dark:after:border-primary-light':
                currentStep >= 2,
              'after:border-border-light dark:after:border-border-dark':
                currentStep < 2,
            }"
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block"
          >
            <span
              :class="{
                'bg-primary text-white': currentStep >= 2,
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark':
                  currentStep < 2,
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
                'bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark':
                  currentStep < 3,
              }"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
            >
              3
            </span>
          </li>
        </ol>
        <div class="flex justify-between mt-2 text-sm font-medium text-subtext-light dark:text-subtext-dark">
          <span :class="{'text-primary dark:text-primary-light': currentStep >= 1}">Email Context</span>
          <span :class="{'text-primary dark:text-primary-light': currentStep >= 2}">Generated Email</span>
          <span :class="{'text-primary dark:text-primary-light': currentStep >= 3}">Download / Copy</span>
        </div>
      </div>

      <!-- Step 1: Provide Context -->
      <div v-if="currentStep === 1">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-2">
          Step 1: Provide Context or Job Description
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Paste the context or upload a file (PDF, DOC, DOCX) that explains the purpose of your email.
        </p>

        <div class="mb-6">
          <label class="sr-only" for="input-method">Input Method</label>
          <div class="relative">
            <select
              id="input-method"
              v-model="inputType"
              class="w-full appearance-none bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg py-3 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            >
              <option value="text">Paste Context</option>
              <option value="pdf">Upload File</option>
            </select>
            <div
              class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-subtext-light dark:text-subtext-dark"
            >
              <span class="material-icons">expand_more</span>
            </div>
          </div>
        </div>

        <!-- Text Input -->
        <div v-if="inputType === 'text'">
          <textarea
            v-model="emailContext"
            rows="10"
            class="w-full bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            placeholder="Paste your context or job description here..."
          ></textarea>
        </div>

        <!-- File Upload -->
        <div v-else class="mt-4">
          <div
            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center cursor-pointer"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="fileInput.click()"
          >
            <p class="font-semibold mb-2">Drag and drop your file here</p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">PDF, DOC, DOCX up to 2MB</p>
            <button
              type="button"
              class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
              @click.stop="fileInput.click()"
            >
              Or browse files
            </button>
            <p v-if="contextFileName" class="mt-4 text-sm text-text-light dark:text-text-dark">
              Selected file: {{ contextFileName }}
            </p>
            <input
              type="file"
              ref="fileInput"
              accept=".pdf,.doc,.docx"
              class="hidden"
              @change="handleFileUpload"
            />
          </div>
        </div>

        <div class="flex justify-end mt-8">
          <button
            @click="submitContext"
            :disabled="!isFormValid || loading"
            class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 disabled:opacity-50"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Generate Email</span>
          </button>
        </div>
      </div>

      <!-- Step 2: Edit Generated Email (Tiptap Integrated Directly) -->
      <div v-else-if="currentStep === 2 && generatedEmail">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4">
          Step 2: Edit Generated Email
        </h2>

        <!-- Tiptap Editor -->
        <EditorContent :editor="editor" class="border p-4 rounded bg-white dark:bg-background-dark max-h-[500px] overflow-y-auto" />

        <div class="flex justify-between mt-6">
          <button @click="goToStep(1)" class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-bold py-2 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
            Go Back & Edit Context
          </button>

          <button @click="goToStep(3)" class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
            Finalize & Copy / Download
            <span class="material-icons ml-2">arrow_forward</span>
          </button>
        </div>
      </div>

      <!-- Step 3: Download / Copy -->
      <div v-else-if="currentStep === 3">
        <h2 class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4">
          Step 3: Copy or Download Email
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Your professional email is ready! Copy it or download it as a Word document.
        </p>
        <div class="flex justify-between mt-6">
          <button @click="goToStep(2)" class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-bold py-2 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
            <span class="material-icons mr-2">arrow_back</span> Review Email
          </button>
          <button @click="downloadAsWord" class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
            <span class="material-icons mr-2">download</span> Download Email
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount, watch } from "vue";
import { Editor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import { generateJobEmail } from "@/services/emailService.js";

// --- Step & Form State ---
const currentStep = ref(1);
const loading = ref(false);

const inputType = ref("text");
const emailContext = ref("");
const contextFile = ref(null);
const contextFileName = ref("");
const fileInput = ref(null);

const generatedEmail = ref("");

// --- Form Validation ---
const isFormValid = computed(() => {
  if (inputType.value === "text" && emailContext.value.trim().length > 0) return true;
  if (inputType.value === "pdf" && contextFile.value) return true;
  return false;
});

// --- File Handlers ---
const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > MAX_FILE_SIZE) return alert("File is too large (2MB max).");

  contextFile.value = file;
  contextFileName.value = file.name;
}

function handleDrop(event) {
  const file = event.dataTransfer.files[0];
  if (!file) return;
  if (file.size > MAX_FILE_SIZE) return alert("File is too large (2MB max).");

  contextFile.value = file;
  contextFileName.value = file.name;
}

// --- Navigation ---
function goToStep(step) {
  currentStep.value = step;
}

// --- Submit Context ---
async function submitContext() {
  generatedEmail.value = "";
  loading.value = true;

  const formData = new FormData();
  if (inputType.value === "text") formData.append("job_text", emailContext.value);
  if (inputType.value === "pdf" && contextFile.value) formData.append("job_pdf", contextFile.value);

  try {
    const data = await generateJobEmail(formData);

    if (data.success && data.email_template) {
      generatedEmail.value = data.email_template;
      editor.commands.setContent(generatedEmail.value);
      currentStep.value = 2;
    } else {
      generatedEmail.value = `❌ Error: ${data.message || "Email generation failed."}`;
      editor.commands.setContent(generatedEmail.value);
      currentStep.value = 2;
    }
  } catch (err) {
    let errorMessage = "❌ An unexpected error occurred.";
    if (err.response?.data) {
      const apiErrors = err.response.data.errors ? Object.values(err.response.data.errors).flat().join(" ") : null;
      errorMessage = `❌ ${err.response.data.message || apiErrors || "Server error."}`;
    }
    generatedEmail.value = errorMessage;
    editor.commands.setContent(errorMessage);
    currentStep.value = 2;
  } finally {
    loading.value = false;
  }
}

// --- Tiptap Editor ---
const editor = new Editor({
  extensions: [StarterKit],
  content: generatedEmail.value || "<p>Start editing...</p>",
  onUpdate: ({ editor }) => {
    generatedEmail.value = editor.getHTML();
  },
});

watch(() => generatedEmail.value, (newVal) => {
  if (newVal !== editor.getHTML()) editor.commands.setContent(newVal, false);
});

onBeforeUnmount(() => editor.destroy());

// --- Download as Word ---
function downloadAsWord() {
  if (!generatedEmail.value) return;

  const preHtml = `
    <html xmlns:o='urn:schemas-microsoft-com:office:office' 
          xmlns:w='urn:schemas-microsoft-com:office:word' 
          xmlns='http://www.w3.org/TR/REC-html40'>
    <head><meta charset='utf-8'><title>Generated Email</title></head><body>`;
  const postHtml = "</body></html>";

  const htmlContent = preHtml + generatedEmail.value + postHtml;

  const blob = new Blob(["\ufeff", htmlContent], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "generated_email.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}
</script>
