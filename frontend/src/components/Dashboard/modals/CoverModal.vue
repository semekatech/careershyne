<template>
  <div class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10">
    <div class="bg-white w-full md:max-w-4xl h-[80vh] relative mx-2 md:mx-0 rounded-2xl shadow-xl flex flex-col" @click.stop>
      
      <!-- Header -->
      <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl">
        <h2 class="text-2xl font-bold text-gray-800">Cover Letter</h2>
        <div class="flex items-center gap-3">
          <button v-if="editor" @click="downloadWord" class="material-icons text-green-600 hover:text-green-800 cursor-pointer">download</button>
          <button v-if="editor" @click="copyToClipboard" class="material-icons text-blue-600 hover:text-blue-800 cursor-pointer">content_copy</button>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">✕</button>
        </div>
      </div>

      <!-- Toolbar -->
      <div v-if="editor" class="flex flex-wrap gap-2 p-3 border-b bg-gray-50">
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
        <button @click="toggleBulletList" :class="toolbarBtnClass(isBulletList)">
          <span class="material-icons text-sm">format_list_bulleted</span>
        </button>
        <button @click="toggleOrderedList" :class="toolbarBtnClass(isOrderedList)">
          <span class="material-icons text-sm">format_list_numbered</span>
        </button>
        <button @click="toggleCodeBlock" :class="toolbarBtnClass(isCodeBlock)">
          <span class="material-icons text-sm">code</span>
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 p-6 overflow-y-auto">
        <!-- Loading -->
        <div v-if="!result || progress < 100" class="flex flex-col items-center justify-center h-full text-center">
          <p class="mb-4 text-gray-600">Generating cover letter...</p>
          <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div class="bg-yellow-500 h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
          </div>
          <p class="mt-3 text-gray-700 font-medium">{{ progress }}%</p>
        </div>

        <!-- Editor / Result -->
        <div v-else>
          <EditorContent
            v-if="editor"
            :editor="editor"
            class="min-h-[60vh] prose prose-sm max-w-full bg-gray-50 p-4 rounded-lg focus:outline-none"
          />
          <div v-else-if="result.error" class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg">
            {{ result.error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount, nextTick, computed } from 'vue';
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import CodeBlock from '@tiptap/extension-code-block';
import BulletList from '@tiptap/extension-bullet-list';
import OrderedList from '@tiptap/extension-ordered-list';
import ListItem from '@tiptap/extension-list-item';
import Strike from '@tiptap/extension-strike';

const props = defineProps({
  job: Object,
  result: Object,
  progress: Number
});

const editor = ref(null);

// Initialize editor when cover letter is available
watch(
  () => props.result?.coverLetter,
  async (val) => {
    if (!val) return;
    await nextTick();

    if (!editor.value) {
      editor.value = new Editor({
        extensions: [StarterKit, Underline, Link, CodeBlock, BulletList, OrderedList, ListItem, Strike],
        content: val,
      });
    } else {
      editor.value.commands.setContent(val, false);
    }
  },
  { immediate: true }
);

onBeforeUnmount(() => { if (editor.value) editor.value.destroy(); });

// Toolbar commands
function toggleBold() { editor.value.chain().focus().toggleBold().run(); }
function toggleItalic() { editor.value.chain().focus().toggleItalic().run(); }
function toggleUnderline() { editor.value.chain().focus().toggleUnderline().run(); }
function toggleStrike() { editor.value.chain().focus().toggleStrike().run(); }
function toggleBulletList() { editor.value.chain().focus().toggleBulletList().run(); }
function toggleOrderedList() { editor.value.chain().focus().toggleOrderedList().run(); }
function toggleCodeBlock() { editor.value.chain().focus().toggleCodeBlock().run(); }

// Toolbar active states
const isBold = computed(() => editor.value?.isActive('bold'));
const isItalic = computed(() => editor.value?.isActive('italic'));
const isUnderline = computed(() => editor.value?.isActive('underline'));
const isStrike = computed(() => editor.value?.isActive('strike'));
const isBulletList = computed(() => editor.value?.isActive('bulletList'));
const isOrderedList = computed(() => editor.value?.isActive('orderedList'));
const isCodeBlock = computed(() => editor.value?.isActive('codeBlock'));

// Toolbar button class
function toolbarBtnClass(active) {
  return `p-2 rounded-md hover:bg-gray-200 ${active ? 'bg-gray-300' : ''}`;
}

// Word download
function downloadWord() {
  if (!editor.value) return;
  const content = `<html><head><meta charset="utf-8"><title>Cover Letter</title></head><body>${editor.value.getHTML()}</body></html>`;
  const blob = new Blob(["\ufeff", content], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "cover-letter.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Copy to clipboard
function copyToClipboard() {
  if (!editor.value) return;
  navigator.clipboard.writeText(editor.value.getHTML()).then(() => alert("Copied to clipboard!"));
}
</script>
