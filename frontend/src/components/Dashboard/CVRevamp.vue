<template>
  <div class="p-4">
    <!-- Header -->
    <header
      class="w-full border border-border-light dark:border-border-dark rounded-lg p-8 text-center shadow-sm mb-8 bg-white dark:bg-background-dark"
    >
      <h1
        class="text-3xl font-bold mb-2 flex items-center justify-center text-text-light dark:text-text-dark"
      >
        AI-Powered CV Revamp
        <span class="material-icons ml-2 text-primary">flash_on</span>
      </h1>
      <p
        class="text-base text-subtext-light dark:text-subtext-dark max-w-2xl mx-auto"
      >
        Provide the job description, and our AI will tailor your CV to highlight
        the most relevant skills and experience.
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
            >Revamped CV</span
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
            <p class="font-semibold mb-2">
              Drag and drop your job description here
            </p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">
              PDF, DOC, DOCX up to 2MB
            </p>
            <button
              type="button"
              class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
              @click.stop="fileInput.click()"
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
              type="file"
              ref="fileInput"
              accept=".pdf,.doc,.docx"
              class="hidden"
              @change="handleJobFileUpload"
            />
          </div>
        </div>

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

      <!-- Step 2: CV Editor -->
      <div v-else-if="currentStep === 2 && revampedCv">
        <h2
          class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4"
        >
          Step 2: Edit & Customize Your Revamped CV
        </h2>

        <!-- Toolbar -->
        <div
          class="flex flex-wrap gap-2 p-3 border-b bg-gray-50 items-center mb-4"
        >
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
            @click="selectAll"
            class="p-2 rounded-md hover:bg-gray-200"
            title="Select All"
          >
            <span class="material-icons text-sm">select_all</span>
          </button>
          <button
            @click="downloadWord"
            class="p-2 rounded-md hover:bg-gray-200 text-green-600"
          >
            <span class="material-icons text-sm">download</span>
          </button>
          <button
            @click="copyToClipboard"
            class="p-2 rounded-md hover:bg-gray-200 text-blue-600"
          >
            <span class="material-icons text-sm">content_copy</span>
          </button>
        </div>

        <!-- Editor -->
        <EditorContent
          v-if="editor"
          :editor="editor"
          class="min-h-[60vh] prose dark:prose-invert max-w-full focus:outline-none w-full border rounded-lg p-6 bg-white shadow"
        />

        <!-- Navigation -->
        <div class="flex justify-between mt-6">
          <button
            @click="goToStep(1)"
            class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-bold py-2 px-6 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
          >
            Go Back & Change Job Details
          </button>
          <button
            @click="goToStep(3)"
            class="bg-primary hover:bg-primary-light text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1"
          >
            Finalize & Download CV
            <span class="material-icons ml-2">arrow_forward</span>
          </button>
        </div>
      </div>

      <!-- Step 3: Download -->
      <div v-else-if="currentStep === 3">
        <h2
          class="text-2xl font-semibold text-text-light dark:text-text-dark mb-4"
        >
          Step 3: Download Your CV
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
            <span class="material-icons mr-2">download</span> Download CV as
            Word
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
import { generateCvRevamp } from "@/services/cvRevampService.js";

const currentStep = ref(1);
const loading = ref(false);
const inputType = ref("text");
const jobText = ref("");
const jobFile = ref(null);
const jobFileName = ref("");
const fileInput = ref(null);
const revampedCv = ref("");
const editor = ref(null);

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

const isFormValid = computed(() => {
  if (inputType.value === "text" && jobText.value.trim().length > 0)
    return true;
  if (inputType.value === "pdf" && jobFile.value) return true;
  return false;
});

const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB
function handleJobFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;
  if (file.size > MAX_FILE_SIZE) return alert("File too large. Max 2MB.");
  jobFile.value = file;
  jobFileName.value = file.name;
}
function handleDrop(event) {
  const file = event.dataTransfer.files[0];
  if (!file) return;
  if (file.size > MAX_FILE_SIZE) return alert("File too large. Max 2MB.");
  jobFile.value = file;
  jobFileName.value = file.name;
}

async function submitJobDetails() {
  revampedCv.value = "";
  loading.value = true;
  const formData = new FormData();
  if (inputType.value === "text") formData.append("job_text", jobText.value);
  if (inputType.value === "pdf" && jobFile.value)
    formData.append("job_file", jobFile.value);

  try {
    const data = await generateCvRevamp(formData);
    if (data.success && data.revamped_cv) {
      revampedCv.value = data.revamped_cv;
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
    } else {
      revampedCv.value = `❌ Error: ${data.message || "CV generation failed."}`;
      currentStep.value = 2;
    }
  } catch (err) {
    revampedCv.value = `❌ Error generating CV.`;
    currentStep.value = 2;
  } finally {
    loading.value = false;
  }
}

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
  link.download = "revamped_cv.doc";
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
    currentStep >= step,
  "after:border-border-light dark:after:border-border-dark": currentStep < step,
});
const circleClass = (step) => ({
  "bg-primary text-white": currentStep >= step,
  "bg-border-light dark:bg-border-dark text-subtext-light dark:text-subtext-dark":
    currentStep < step,
});

onBeforeUnmount(() => {
  if (editor.value) editor.value.destroy();
});
</script>
