<template>
  <div class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10">
    <div class="bg-white w-full md:max-w-4xl h-[80vh] relative mx-2 md:mx-0 rounded-2xl shadow-xl flex flex-col" @click.stop>
      <!-- Header -->
      <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl">
        <h2 class="text-2xl font-bold text-gray-800">Email Template</h2>
        <div class="flex items-center gap-3">
          <button v-if="result?.template" @click="downloadWord" class="material-icons text-green-600 hover:text-green-800 cursor-pointer">download</button>
          <button v-if="result?.template" @click="copyToClipboard(result.template)" class="material-icons text-blue-600 hover:text-blue-800 cursor-pointer">content_copy</button>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">✕</button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 p-6 overflow-y-auto">
        <!-- Loading -->
        <div v-if="!result || progress < 100" class="flex flex-col items-center justify-center h-full text-center">
          <p class="mb-4 text-gray-600">Generating email template...</p>
          <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div class="bg-purple-500 h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
          </div>
          <p class="mt-3 text-gray-700 font-medium">{{ progress }}%</p>
        </div>

        <!-- Result -->
        <div v-else class="space-y-6">
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Generated Email Template</h3>
          <div v-if="result.template" class="prose max-w-full text-gray-700 bg-gray-50 p-4 rounded-lg shadow-sm" v-html="result.template"></div>
          <div v-else-if="result.error" class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg">
            {{ result.error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  job: Object,
  result: Object,
  progress: Number
});

function downloadWord() {
  if (!props.result?.template) return;
  const content = `
    <html xmlns:o="urn:schemas-microsoft-com:office:office" 
          xmlns:w="urn:schemas-microsoft-com:office:word" 
          xmlns="http://www.w3.org/TR/REC-html40">
      <head><meta charset="utf-8"><title>Email Template</title></head>
      <body>${props.result.template}</body>
    </html>
  `;
  const blob = new Blob(["\ufeff", content], { type: "application/msword" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "email-template.doc";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => alert("Copied to clipboard!"));
}
</script>
