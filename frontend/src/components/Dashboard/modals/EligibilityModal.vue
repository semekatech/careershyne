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
        <h2 class="text-2xl font-bold text-gray-800">Eligibility Check</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">✕</button>
      </div>

      <div class="flex-1 p-6 overflow-y-auto">
        <div v-if="!result || progress < 100" class="flex flex-col items-center justify-center h-full text-center">
          <p class="mb-4 text-gray-600">Comparing your CV with <strong>{{ job?.title }}</strong>...</p>
          <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
            <div class="bg-green-500 h-4 rounded-full transition-all duration-500" :style="{ width: progress + '%' }"></div>
          </div>
          <p class="mt-3 text-gray-700 font-medium">{{ progress }}%</p>
        </div>

        <div v-else class="space-y-8">
          <div class="flex flex-col items-center">
            <div class="relative flex items-center justify-center w-32 h-32 rounded-full border-8 shadow-md"
                 :class="result.matchPercentage >= 70 ? 'border-green-500' : result.matchPercentage >= 40 ? 'border-yellow-500' : 'border-red-500'">
              <span class="text-3xl font-bold text-gray-800">{{ result.matchPercentage }}%</span>
            </div>
            <p class="mt-2 text-gray-600 font-medium">Match Score</p>
          </div>

          <div v-if="result.matchedSkills?.length" class="bg-green-50 border border-green-200 rounded-lg p-5 shadow-sm">
            <h3 class="font-semibold text-green-700 mb-3 text-lg">✅ Matched Skills</h3>
            <ul class="list-disc list-inside text-sm text-gray-700 grid grid-cols-1 sm:grid-cols-2 gap-x-6">
              <li v-for="(skill, index) in result.matchedSkills" :key="index">{{ skill }}</li>
            </ul>
          </div>

          <div v-if="result.missingSkills?.length" class="bg-red-50 border border-red-200 rounded-lg p-5 shadow-sm">
            <h3 class="font-semibold text-red-700 mb-3 text-lg">❌ Missing Skills</h3>
            <ul class="list-disc list-inside text-sm text-red-600 grid grid-cols-1 sm:grid-cols-2 gap-x-6">
              <li v-for="(skill, index) in result.missingSkills" :key="index">{{ skill }}</li>
            </ul>
          </div>

          <div v-if="result.recommendations" class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm">
            <h3 class="font-semibold text-blue-700 mb-3 text-lg">💡 Recommendations</h3>
            <p class="text-sm text-gray-700 leading-relaxed">{{ result.recommendations }}</p>
          </div>

          <div v-else-if="result.error" class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg">
            {{ result.error }}
          </div>
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
