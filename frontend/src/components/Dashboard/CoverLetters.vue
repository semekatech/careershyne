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

    <!-- Steps Section -->
    <section class="bg-card-light dark:bg-card-dark p-8 rounded-lg">
      <!-- Step Progress -->
      <div class="mb-8">
        <ol class="flex items-center w-full">
          <li
            :class="stepClass(1)"
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block"
          >
            <span
              :class="circleClass(1)"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
              >1</span
            >
          </li>
          <li
            :class="stepClass(2)"
            class="flex w-full items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-4 after:inline-block"
          >
            <span
              :class="circleClass(2)"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
              >2</span
            >
          </li>
          <li class="flex items-center">
            <span
              :class="circleClass(3)"
              class="flex items-center justify-center w-10 h-10 rounded-full lg:h-12 lg:w-12 shrink-0"
              >3</span
            >
          </li>
        </ol>

        <div
          class="flex justify-between mt-2 text-sm font-medium text-subtext-light dark:text-subtext-dark"
        >
          <span
            :class="{
              'text-primary dark:text-primary-light': currentStep >= 1,
            }"
            >Job Details</span
          >
          <span
            :class="{
              'text-primary dark:text-primary-light': currentStep >= 2,
            }"
            >Cover Letter</span
          >
          <span
            :class="{
              'text-primary dark:text-primary-light': currentStep >= 3,
            }"
            >Download</span
          >
        </div>
      </div>

      <!-- Step 1: Job Input -->
      <div v-if="currentStep === 1">
        <h2
          class="text-2xl font-semibold text-text-light dark:text-text-dark mb-2"
        >
          Step 1: Provide Job Description
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Paste the job description or upload it as a PDF/DOC.
        </p>

        <!-- Input Type Selector -->
        <div class="mb-6">
          <label class="sr-only" for="input-method">Input Method</label>
          <div class="relative">
            <select
              id="input-method"
              v-model="inputType"
              class="w-full appearance-none bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-light dark:text-text-dark rounded-lg py-3 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
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

        <!-- Textarea Input -->
        <div v-if="inputType === 'text'">
          <label class="sr-only" for="job-description"
            >Paste job description here...</label
          >
          <textarea
            id="job-description"
            v-model="jobText"
            rows="10"
            :disabled="loading"
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
            @click="$refs.fileInput && $refs.fileInput.click()"
          >
            <p class="font-semibold mb-2">
              Drag and drop your job description here
            </p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">
              PDF, DOC, DOCX up to 2MB
            </p>
            <button
              type="button"
              :disabled="loading"
              class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
              @click.stop="$refs.fileInput && $refs.fileInput.click()"
            >
              Or browse files
            </button>
            <p
              v-if="jobFileName"
              class="mt-4 text-sm text-text-light dark:text-text-dark"
            >
              Selected file: {{ jobFileName }}
            </p>
            <input
              ref="fileInput"
              type="file"
              accept=".pdf,.doc,.docx"
              class="hidden"
              @change="handleJobFileUpload"
            />
          </div>
        </div>

        <div class="flex flex-col items-end mt-6">
          <div class="w-full sm:w-auto">
            <button
              @click="submitJobDetails"
              :disabled="!isFormValid || loading"
              class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 disabled:opacity-50 flex items-center"
            >
              <svg
                v-if="loading"
                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                ></path>
              </svg>
              <span v-if="loading"
                >Generating your tailored Cover Letter...</span
              >
              <span v-else>Tailor my Cover Letter</span>
            </button>
          </div>

          <!-- Progress message -->
          <p
            v-if="loading"
            class="mt-3 text-sm text-subtext-light dark:text-subtext-dark animate-pulse text-center w-full sm:w-auto"
          >
            {{ progressMessage }}
          </p>
        </div>
      </div>

      <!-- Step 2: CV Editor -->
      <div v-else-if="currentStep === 2 && revampedCv">
        <h2
          class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4"
        >
          Step 2: Edit & Customize Your Cover Letter
        </h2>

        <!-- Editor Toolbar -->
        <div class="flex flex-wrap gap-2 p-3 border-b bg-gray-50 items-center">
          <button @click="toggleBold" :class="toolbarBtnClass(isBold)">
            <span class="material-icons text-sm">format_bold</span>
          </button>
          <button @click="toggleItalic" :class="toolbarBtnClass(isItalic)">
            <span class="material-icons text-sm">format_italic</span>
          </button>
          <button
            @click="toggleUnderline"
            :class="toolbarBtnClass(isUnderline)"
          >
            <span class="material-icons text-sm">format_underlined</span>
          </button>
          <button @click="toggleStrike" :class="toolbarBtnClass(isStrike)">
            <span class="material-icons text-sm">strikethrough_s</span>
          </button>

          <button
            @click="toggleBulletList"
            :class="toolbarBtnClass(isBulletList)"
          >
            <span class="material-icons text-sm">format_list_bulleted</span>
          </button>
          <button
            @click="toggleOrderedList"
            :class="toolbarBtnClass(isOrderedList)"
          >
            <span class="material-icons text-sm">format_list_numbered</span>
          </button>
          <button
            @click="toggleCodeBlock"
            :class="toolbarBtnClass(isCodeBlock)"
          >
            <span class="material-icons text-sm">code</span>
          </button>

          <button @click="undo" class="p-2 rounded-md hover:bg-gray-200">
            <span class="material-icons text-sm">undo</span>
          </button>
          <button @click="redo" class="p-2 rounded-md hover:bg-gray-200">
            <span class="material-icons text-sm">redo</span>
          </button>

          <select
            v-model="fontSize"
            @change="setFontSize"
            class="border rounded p-1 text-sm"
          >
            <option v-for="size in fontSizes" :key="size" :value="size">
              {{ size }}
            </option>
          </select>

          <select
            v-model="fontFamily"
            @change="setFontFamily"
            class="border rounded p-1 text-sm"
          >
            <option
              v-for="family in fontFamilies"
              :key="family"
              :value="family"
            >
              {{ family }}
            </option>
          </select>

          <input
            type="color"
            v-model="textColor"
            @input="setTextColor"
            class="w-8 h-8 border-0 cursor-pointer"
            title="Text color"
          />

          <button
            @click="setTextAlign('left')"
            class="p-2 rounded-md hover:bg-gray-200"
          >
            <span class="material-icons text-sm">format_align_left</span>
          </button>
          <button
            @click="setTextAlign('center')"
            class="p-2 rounded-md hover:bg-gray-200"
          >
            <span class="material-icons text-sm">format_align_center</span>
          </button>
          <button
            @click="setTextAlign('right')"
            class="p-2 rounded-md hover:bg-gray-200"
          >
            <span class="material-icons text-sm">format_align_right</span>
          </button>
          <button
            @click="setTextAlign('justify')"
            class="p-2 rounded-md hover:bg-gray-200"
          >
            <span class="material-icons text-sm">format_align_justify</span>
          </button>

          <button
            @click="downloadWord"
            class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded-md text-sm font-medium"
          >
            <span class="material-icons text-base">description</span> Word
          </button>
          <button
            @click="downloadPDF"
            class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium"
          >
            <span class="material-icons text-base">picture_as_pdf</span> PDF
          </button>
        </div>

        <!-- Editor Content -->
        <div
          class="flex-1 p-4 overflow-y-auto bg-gray-50 relative min-h-[60vh]"
        >
          <EditorContent
            v-if="editor"
            :editor="editor"
            class="prose prose-base max-w-none w-full focus:outline-none leading-relaxed bg-white shadow rounded-lg p-6"
          />
          <div
            v-else
            class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg w-full"
          >
            {{ revampedCv || "No template available." }}
          </div>
        </div>
      </div>

      <!-- Step 3: Download -->
      <div v-else-if="currentStep === 3">
        <h2
          class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4"
        >
          Step 3: Download Your Cover Letter
        </h2>
        <p class="text-subtext-light dark:text-subtext-dark mb-6">
          Your CV is ready! Download the file below. It's tailored for the job
          you provided.
        </p>
        <div class="flex justify-between mt-6">
          <button
            @click="goToStep(2)"
            class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-bold py-2 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
          >
            <span class="material-icons mr-2">arrow_back</span> Review CV
          </button>
          <button
            @click="downloadWord"
            class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1"
          >
            <span class="material-icons mr-2">download</span> Download Cover
            Letter as Word
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, watch, onBeforeUnmount, nextTick } from "vue";
import { Editor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import { Underline } from "@tiptap/extension-underline";
import { Link } from "@tiptap/extension-link";
import { CodeBlock } from "@tiptap/extension-code-block";
import { BulletList } from "@tiptap/extension-bullet-list";
import { OrderedList } from "@tiptap/extension-ordered-list";
import { ListItem } from "@tiptap/extension-list-item";
import { Strike } from "@tiptap/extension-strike";
import { TextStyle } from "@tiptap/extension-text-style";
import { Color } from "@tiptap/extension-color";
import { TextAlign } from "@tiptap/extension-text-align";
import { generateCoverLetter } from "@/services/coverLetterService.js";
import html2pdf from "html2pdf.js";
const currentStep = ref(1);
const loading = ref(false);
const inputType = ref("text");
const jobText = ref("");
const jobFile = ref(null);
const jobFileName = ref("");
const revampedCv = ref("");
const editor = ref(null);
const progressMessage = ref("");
let progressInterval = null;

const fontSize = ref("14px");
const fontFamily = ref("Arial");
const textColor = ref("#000000");
const fontSizes = ["12px", "14px", "16px", "18px", "20px", "24px"];
const fontFamilies = [
  "Arial",
  "Verdana",
  "Georgia",
  "Times New Roman",
  "Courier New",
];

const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB

const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.trim().length > 0)
    return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

function handleJobFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > MAX_FILE_SIZE) {
    alert("File too large. Max 2MB.");
    return;
  }
  jobFile.value = file;
  jobFileName.value = file.name;
}

function handleDrop(event) {
  const file = event.dataTransfer.files[0];
  if (!file) return;
  if (file.size > MAX_FILE_SIZE) {
    alert("File too large. Max 2MB.");
    return;
  }
  jobFile.value = file;
  jobFileName.value = file.name;
}

// Progress messages sequence
const PROGRESS_MESSAGES = [
  "Analyzing your job description...",
  "Extracting key skills and qualifications...",
  "Aligning your CV with the job requirements...",
  "Polishing tone and formatting...",
  "Finalizing your revamped CV...",
];

async function submitJobDetails() {
  // reset state
  revampedCv.value = "";
  loading.value = true;
  progressMessage.value = PROGRESS_MESSAGES[0];

  // start simulated progress (will be cleared when request finishes)
  let i = 1;
  progressInterval = setInterval(() => {
    if (i < PROGRESS_MESSAGES.length) {
      progressMessage.value = PROGRESS_MESSAGES[i++];
    }
  }, 2500);

  // prepare formData
  const formData = new FormData();
  if (inputType.value === "text") formData.append("job_text", jobText.value);
  if (inputType.value === "pdf" && jobFile.value)
    formData.append("job_file", jobFile.value);

  try {
    const data = await generateCoverLetter(formData);
    console.log("API response:", data);
    clearProgressInterval();
    if (data && data.success && data.revamped_cv) {
      let cleaned = data.revamped_cv;
      if (
        cleaned.includes("❌ Error: Cover letter generated successfully") ||
        cleaned.toLowerCase().includes("error")
      ) {
        revampedCv.value =
          "❌ The cover letter API returned a placeholder message. Please check your input or try again.";
      } else {
        // Remove markdown fences and JSON wrapping
        cleaned = cleaned.replace(/```[\s\S]*?```/g, "");
        const jsonMatch = cleaned.match(
          /{\s*"revampedCv"\s*:\s*"([\s\S]*)"\s*}/
        );
        if (jsonMatch) cleaned = jsonMatch[1];

        cleaned = cleaned.replace(/\\"/g, '"').replace(/\\n/g, "\n").trim();
        cleaned = cleaned.replace(/\n{2,}/g, "\n");
        cleaned = cleaned.replace(/<p>(\s|&nbsp;)*<\/p>/g, "");

        revampedCv.value = cleaned;
        currentStep.value = 2;

        await nextTick();

        if (!editor.value) {
          editor.value = new Editor({
            extensions: [
              StarterKit,
              Underline,
              Link,
              CodeBlock,
              BulletList,
              OrderedList,
              ListItem,
              Strike,
              TextStyle,
              Color,
              TextAlign.configure({ types: ["heading", "paragraph"] }),
            ],
            content: revampedCv.value,
          });
        } else {
          editor.value.commands.setContent(revampedCv.value, false);
        }
      }
    } else {
      revampedCv.value =
        "❌ The cover letter API returned no content. Please try again.";
      currentStep.value = 2;
    }
  } catch (err) {
    clearProgressInterval();
    revampedCv.value = "❌ Error generating CV. Please try again.";
    currentStep.value = 2;
  } finally {
    clearProgressInterval();
    loading.value = false;
  }
}

function clearProgressInterval() {
  if (progressInterval) {
    clearInterval(progressInterval);
    progressInterval = null;
  }
}

// Navigation
function goToStep(step) {
  currentStep.value = step;
}

// Toolbar Actions
const toggleBold = () => editor.value.chain().focus().toggleBold().run();
const toggleItalic = () => editor.value.chain().focus().toggleItalic().run();
const toggleUnderline = () =>
  editor.value.chain().focus().toggleUnderline().run();
const toggleStrike = () => editor.value.chain().focus().toggleStrike().run();
const toggleBulletList = () =>
  editor.value.chain().focus().toggleBulletList().run();
const toggleOrderedList = () =>
  editor.value.chain().focus().toggleOrderedList().run();
const toggleCodeBlock = () =>
  editor.value.chain().focus().toggleCodeBlock().run();
const undo = () => editor.value.chain().focus().undo().run();
const redo = () => editor.value.chain().focus().redo().run();
const selectAll = () => editor.value.commands.focus().selectAll();
const setFontSize = () =>
  editor.value
    .chain()
    .focus()
    .setMark("textStyle", { fontSize: fontSize.value })
    .run();
const setFontFamily = () =>
  editor.value
    .chain()
    .focus()
    .setMark("textStyle", { fontFamily: fontFamily.value })
    .run();
const setTextColor = () =>
  editor.value
    .chain()
    .focus()
    .setMark("textStyle", { color: textColor.value })
    .run();
const setTextAlign = (align) =>
  editor.value.chain().focus().setNode("paragraph", { textAlign: align }).run();

const isBold = computed(() => editor.value?.isActive("bold"));
const isItalic = computed(() => editor.value?.isActive("italic"));
const isUnderline = computed(() => editor.value?.isActive("underline"));
const isStrike = computed(() => editor.value?.isActive("strike"));
const isBulletList = computed(() => editor.value?.isActive("bulletList"));
const isOrderedList = computed(() => editor.value?.isActive("orderedList"));
const isCodeBlock = computed(() => editor.value?.isActive("codeBlock"));

const toolbarBtnClass = (active) =>
  `p-2 rounded-md hover:bg-gray-200 ${active ? "bg-gray-300" : ""}`;

// Download / Copy
function downloadWord() {
  if (!editor.value) return;
  const html = `<html><head><meta charset="utf-8"><title>Revamped CV</title></head><body>${editor.value.getHTML()}</body></html>`;
  const blob = new Blob(["\ufeff", html], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "cover_letter.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}
function copyToClipboard() {
  if (!editor.value) return;
  navigator.clipboard
    .writeText(editor.value.getHTML())
    .then(() => alert("Copied!"));
}

// Step / Circle Classes
const stepClass = (step) => ({
  "text-primary dark:text-primary-light after:border-primary dark:after:border-primary-light":
    currentStep.value >= step,
  "after:border-border-light dark:after:border-border-dark":
    currentStep.value < step,
});
const circleClass = (step) => ({
  "bg-primary text-white": currentStep.value >= step,
  "bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark":
    currentStep.value < step,
});

async function downloadPDF() {
  if (!editor.value) return;

  // Select the editor's content
  const editorEl = document.querySelector(".ProseMirror");
  if (!editorEl) return;

  // Clone the content to preserve formatting
  const clone = editorEl.cloneNode(true);

  // Create a container for html2pdf
  const container = document.createElement("div");
  container.style.fontFamily = "'Times New Roman', serif";
  container.style.fontSize = "12pt";
  container.style.lineHeight = "1.6";
  container.style.whiteSpace = "normal"; // change from pre-wrap
  container.style.padding = "20px";

  // Maintain paragraph spacing
  clone.querySelectorAll("p").forEach((p) => {
    p.style.marginTop = "0.5em";
    p.style.marginBottom = "0.5em";
  });

  container.appendChild(clone);

  // Generate PDF
  await html2pdf()
    .set({
      margin: 10,
      filename: "cover_letter.pdf",
      html2canvas: { scale: 2, letterRendering: true },
      jsPDF: { unit: "pt", format: "a4", orientation: "portrait" },
    })
    .from(container)
    .save();
}

onBeforeUnmount(() => {
  if (editor.value) editor.value.destroy();
  clearProgressInterval();
});
</script>

<style scoped>
/* small pulse (keeps your existing animate-pulse feel but scoped) */
.animate-pulse {
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
  0%,
  100% {
    opacity: 0.8;
  }
  50% {
    opacity: 1;
  }
}
.prose p {
  margin-top: 0.25em; /* default is 1em */
  margin-bottom: 0.25em;
  line-height: 1.4;
}
</style>
