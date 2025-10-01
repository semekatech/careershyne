<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-60 flex justify-center items-center z-50 overflow-auto p-4"
  >
    <div
      class="bg-white w-full md:max-w-4xl h-[80vh] rounded-xl shadow-xl flex flex-col overflow-hidden"
    >
      <!-- Header -->
      <div
        class="flex justify-between items-center p-4 border-b bg-white sticky top-0 z-10"
      >
        <h2 class="text-2xl font-semibold text-gray-800">Email Template</h2>
        <div class="flex items-center gap-3">
          <button
            v-if="editor"
            @click="downloadWord"
            class="text-green-600 hover:text-green-800"
            title="Download Word"
          >
            <span class="material-icons">download</span>
          </button>
          <button
            v-if="editor"
            @click="copyToClipboard"
            class="text-blue-600 hover:text-blue-800"
            title="Copy HTML"
          >
            <span class="material-icons">content_copy</span>
          </button>
          <button
            @click="$emit('close')"
            class="text-gray-500 hover:text-gray-800 text-xl font-bold"
          >
            ✕
          </button>
        </div>
      </div>

      <!-- Toolbar -->
      <div
        v-if="editor && props.progress >= 100"
        class="flex flex-wrap gap-2 p-3 border-b bg-gray-50 items-center"
      >
        <!-- Basic formatting -->
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

        <!-- Lists -->
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

        <!-- Undo / Redo -->
        <button @click="undo" class="p-2 rounded-md hover:bg-gray-200">
          <span class="material-icons text-sm">undo</span>
        </button>
        <button @click="redo" class="p-2 rounded-md hover:bg-gray-200">
          <span class="material-icons text-sm">redo</span>
        </button>

        <!-- Select All -->
        <button
          @click="selectAll"
          class="p-2 rounded-md hover:bg-gray-200"
          title="Select All"
        >
          <span class="material-icons text-sm">select_all</span>
        </button>

        <!-- Font size -->
        <select
          v-model="fontSize"
          @change="setFontSize"
          class="border rounded p-1 text-sm"
        >
          <option v-for="size in fontSizes" :key="size" :value="size">
            {{ size }}
          </option>
        </select>

        <!-- Font family -->
        <select
          v-model="fontFamily"
          @change="setFontFamily"
          class="border rounded p-1 text-sm"
        >
          <option v-for="family in fontFamilies" :key="family" :value="family">
            {{ family }}
          </option>
        </select>

        <!-- Text color -->
        <input
          type="color"
          v-model="textColor"
          @input="setTextColor"
          title="Text color"
          class="w-8 h-8 p-0 border-0 cursor-pointer"
        />

        <!-- Text Align -->
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

      <!-- Editor / Loading / Error -->
      <!-- Editor / Loading / Error -->
      <div class="flex-1 p-4 overflow-y-auto bg-gray-50 relative">
        <div
          v-if="!props.result?.template || props.progress < 100"
          class="flex flex-col items-center justify-center h-full text-center space-y-2"
        >
          <p class="text-gray-600 text-lg font-medium">
            Generating email template
            <span class="inline-block animate-pulse">...</span>
          </p>

          <!-- Progress messages -->
          <p class="text-gray-700 font-medium">
            {{
              props.progress < 25
                ? "Initializing..."
                : props.progress < 50
                ? "Fetching content..."
                : props.progress < 75
                ? "Applying formatting..."
                : props.progress < 100
                ? "Almost done..."
                : "Finishing up..."
            }}
          </p>

          <!-- Progress bar -->
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
          <EditorContent
            v-if="editor"
            :editor="editor"
            class="min-h-[60vh] prose prose-sm max-w-full focus:outline-none w-full"
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

const props = defineProps({ job: Object, result: Object, progress: Number });

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

watch(
  () => props.result?.template,
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
        content: val,
      });
    } else {
      editor.value.commands.setContent(val, false);
    }
  },
  { immediate: true }
);

onBeforeUnmount(() => {
  if (editor.value) editor.value.destroy();
});

// Toolbar actions
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

// Text style
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

// Toolbar state
const isBold = computed(() => editor.value?.isActive("bold"));
const isItalic = computed(() => editor.value?.isActive("italic"));
const isUnderline = computed(() => editor.value?.isActive("underline"));
const isStrike = computed(() => editor.value?.isActive("strike"));
const isBulletList = computed(() => editor.value?.isActive("bulletList"));
const isOrderedList = computed(() => editor.value?.isActive("orderedList"));
const isCodeBlock = computed(() => editor.value?.isActive("codeBlock"));

const toolbarBtnClass = (active) =>
  `p-2 rounded-md hover:bg-gray-200 ${active ? "bg-gray-300" : ""}`;

// Word download
function downloadWord() {
  if (!editor.value) return;
  const content = `<html><head><meta charset="utf-8"><title>Email</title></head><body>${editor.value.getHTML()}</body></html>`;
  const blob = new Blob(["\ufeff", content], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "email-template.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Copy
function copyToClipboard() {
  if (!editor.value) return;
  navigator.clipboard
    .writeText(editor.value.getHTML())
    .then(() => alert("Copied!"));
}
</script>

<style>
@keyframes slide {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}
.animate-slide {
  background-size: 200% 100%;
  animation: slide 1.5s linear infinite;
}
@keyframes pulse {
  0%,
  100% {
    opacity: 0;
  }
  50% {
    opacity: 1;
  }
}
.animate-pulse {
  animation: pulse 1s infinite;
}
</style>
