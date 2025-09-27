<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
    @click="$emit('close')"
  >
    <div
      class="bg-white w-full md:max-w-4xl h-[80vh] relative mx-2 md:mx-0 rounded-2xl shadow-xl flex flex-col"
      @click.stop
    >
      <div class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl">
        <h2 class="text-2xl font-bold text-gray-800">CV Revamp</h2>
        <div class="flex items-center gap-3">
          <button v-if="result?.revampedCv" @click="$emit('download')" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">Download Word</button>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">✕</button>
        </div>
      </div>

      <div class="flex-1 p-6 overflow-y-auto">
        <div v-if="!result || progress < 100" class="flex flex-col items-center justify-center h-full text-center">
          <p class="mb-4 text-gray-600">Revamping your CV for <strong>{{ job?.title }}</strong>...</p>
          <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div class="bg-blue-500 h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
          </div>
          <p class="mt-3 text-gray-700 font-medium">{{ progress }}%</p>
        </div>

        <div v-else class="space-y-6">
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Revamped CV</h3>
          <div v-if="result.revampedCv" class="prose max-w-full text-gray-700 bg-gray-50 p-4 rounded-lg shadow-sm" v-html="result.revampedCv"></div>

          <div v-if="result.recommendations?.length" class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm">
            <h3 class="font-semibold text-blue-700 mb-3 text-lg">💡 Recommendations</h3>
            <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
              <li v-for="(rec, idx) in result.recommendations" :key="idx">{{ rec }}</li>
            </ul>
          </div>

          <div v-else-if="result.error" class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg">{{ result.error }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  job: Object,
  progress: Number,
  result: Object
});
</script>
