<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 overflow-auto p-4"
  >
    <div
      class="bg-white w-full md:max-w-4xl h-[85vh] rounded-xl shadow-xl flex flex-col overflow-hidden"
    >
      <!-- Header -->
      <!-- Header -->
      <div class="border-b bg-white sticky top-0 z-10">
        <div class="flex flex-wrap justify-between items-center gap-3 p-4">
          <!-- Title + Close -->
          <div class="flex items-center justify-between w-full sm:w-auto">
            <h2 class="text-2xl font-semibold text-gray-800">
             Cover Letter
            </h2>
            <button
              @click="$emit('close')"
              class="text-gray-500 hover:text-gray-800 text-2xl font-bold sm:hidden"
            >
              ✕
            </button>
          </div>

          <!-- Buttons -->
          <div
            class="flex flex-wrap justify-end items-center gap-3 w-full sm:w-auto"
          >
            <button
              v-if="editor"
              @click="downloadWord"
              class="flex items-center gap-1 bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded-md text-sm font-medium w-full sm:w-auto justify-center"
            >
              <span class="material-icons text-base">description</span>
              Download Word
            </button>

            <button
              v-if="editor"
              @click="downloadPDF"
              class="flex items-center gap-1 bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded-md text-sm font-medium w-full sm:w-auto justify-center"
            >
              <span class="material-icons text-base">picture_as_pdf</span>
              Download PDF
            </button>
<!-- 
            <button
              v-if="editor"
              @click="copyToClipboard"
              class="flex items-center gap-1 bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded-md text-sm font-medium w-full sm:w-auto justify-center"
            >
              <span class="material-icons text-base">content_copy</span>
              Copy HTML
            </button> -->

            <button
              @click="$emit('close')"
              class="hidden sm:block text-gray-500 hover:text-gray-800 text-2xl font-bold"
            >
              ✕
            </button>
          </div>
        </div>
      </div>

      <!-- Toolbar -->
      <div
        v-if="editor && props.progress >= 100"
        class="flex flex-wrap gap-2 p-3 border-b bg-gray-50 items-center"
      >
        <button @click="toggleBold" :class="toolbarBtnClass(isBold)">
          <span class="material-icons text-sm">format_bold</span>
        </button>
        <button @click="toggleItalic" :class="toolbarBtnClass(isItalic)">
          <span class="material-icons text-sm">format_italic</span>
        </button>
        <button @click="toggleUnderline" :class="toolbarBtnClass(isUnderline)">
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
        <button @click="toggleCodeBlock" :class="toolbarBtnClass(isCodeBlock)">
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
          <option v-for="family in fontFamilies" :key="family" :value="family">
            {{ family }}
          </option>
        </select>

        <input
          type="color"
          v-model="textColor"
          @input="setTextColor"
          title="Text color"
          class="w-8 h-8 p-0 border-0 cursor-pointer"
        />

        <button
          @click="setTextAlign('left')"
          class="p-2 rounded-md hover:bg-gray-200"
          title="Align Left"
        >
          <span class="material-icons text-sm">format_align_left</span>
        </button>
        <button
          @click="setTextAlign('center')"
          class="p-2 rounded-md hover:bg-gray-200"
          title="Align Center"
        >
          <span class="material-icons text-sm">format_align_center</span>
        </button>
        <button
          @click="setTextAlign('right')"
          class="p-2 rounded-md hover:bg-gray-200"
          title="Align Right"
        >
          <span class="material-icons text-sm">format_align_right</span>
        </button>
        <button
          @click="setTextAlign('justify')"
          class="p-2 rounded-md hover:bg-gray-200"
          title="Justify"
        >
          <span class="material-icons text-sm">format_align_justify</span>
        </button>
      </div>

      <!-- Editor -->
      <div class="flex-1 p-4 overflow-y-auto bg-gray-50 relative">
        <div
          v-if="!props.result?.coverLetter || props.progress < 100"
          class="flex flex-col items-center justify-center h-full text-center space-y-2"
        >
          <p class="text-gray-600 text-lg font-medium">
            Generating cover letter
            <span class="inline-block animate-pulse">...</span>
          </p>

          <div class="w-3/4 h-4 rounded-full overflow-hidden bg-gray-200">
            <div
              class="h-4 rounded-full animate-slide"
              :style="{
                width: props.progress + '%',
                background: `linear-gradient(to right, #3b82f6, #6366f1, #10b981)`,
              }"
            ></div>
          </div>
          <p class="text-gray-700 font-medium">{{ props.progress }}%</p>
        </div>

        <div v-else>
          <div
            class="bg-white shadow-lg rounded-lg p-10 w-full max-w-4xl min-h-[70vh] mx-auto"
          >
            <EditorContent
              v-if="editor"
              :editor="editor"
              class="prose prose-base max-w-none w-full focus:outline-none leading-relaxed"
            />
            <div
              v-else
              class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg w-full"
            >
              {{ props.result?.error || "No template available." }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount, nextTick, computed } from "vue";
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
import html2pdf from "html2pdf.js";

const props = defineProps({ job: Object, result: Object, progress: Number });

const editor = ref(null);
const fontSize = ref("12pt");
const fontFamily = ref("Times New Roman");
const textColor = ref("#000000");
const fontSizes = ["10pt", "11pt", "12pt", "14pt", "16pt", "18pt"];
const fontFamilies = [
  "Times New Roman",
  "Arial",
  "Verdana",
  "Georgia",
  "Courier New",
];

watch(
  () => props.result?.coverLetter,
  async (val) => {
    if (!val) return;
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
        content: `<div style="font-family:'Times New Roman'; font-size:12pt; line-height:1.6;">${val}</div>`,
        editorProps: {
          handleKeyDown(view, event) {
            if (event.key === "Tab" && !event.shiftKey) {
              event.preventDefault();
              const { state, dispatch } = view;
              const { from, to } = state.selection;
              dispatch(
                state.tr.insertText("\u00a0\u00a0\u00a0\u00a0", from, to)
              );
              return true;
            }
            return false;
          },
        },
      });
    } else {
      editor.value.commands.setContent(val, false);
    }
  },
  { immediate: true }
);

onBeforeUnmount(() => editor.value?.destroy());

// Formatting
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

// --- EXPORTS ---

async function downloadWord() {
  if (!editor.value) return;
  editor.value.commands.blur();
  await nextTick();
  const editorEl = document.querySelector(".ProseMirror");
  const latestHTML = editorEl ? editorEl.innerHTML : editor.value.getHTML();

  const content = `
    <html>
      <head><meta charset="utf-8"><title>Cover Letter</title></head>
      <body style="font-family:'Times New Roman'; font-size:12pt; line-height:1.6;">${latestHTML}</body>
    </html>
  `;

  const blob = new Blob(["\ufeff", content], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "cover_letter.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

async function downloadPDF() {
  if (!editor.value) return;
  const editorEl = document.querySelector(".ProseMirror");
  const clone = editorEl.cloneNode(true);
  const container = document.createElement("div");
  container.style.fontFamily = "'Times New Roman'";
  container.style.fontSize = "12pt";
  container.style.lineHeight = "1.6";
  container.style.whiteSpace = "pre-wrap";
  container.style.padding = "20px";
  container.appendChild(clone);
  html2pdf()
    .set({ margin: 10, filename: "cover_letter.pdf" })
    .from(container)
    .save();
}

function copyToClipboard() {
  const editorEl = document.querySelector(".ProseMirror");
  const html = editorEl ? editorEl.innerHTML : editor.value?.getHTML() || "";
  navigator.clipboard.writeText(html).then(() => alert("Copied!"));
}
</script>

<style>
@keyframes slide {
  0%,
  100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}
.animate-slide {
  background-size: 200% 100%;
  animation: slide 1.5s linear infinite;
}
.ProseMirror:focus {
  outline: none !important;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}
</style>
