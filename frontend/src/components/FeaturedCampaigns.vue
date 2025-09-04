<template>
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl md:text-4xl font-semibold text-gray-800 mb-8 text-center">Pricing Comparison</h1>

<!-- Desktop Table Layout -->
<div class="hidden lg:grid grid-cols-[1.5fr_1fr_1fr_1fr_1fr] gap-px bg-gray-200 shadow-xl rounded-lg overflow-hidden">
  <!-- Table Headers -->
  <div class="col-span-1 bg-white p-4 font-semibold text-left text-gray-600">Feature</div>
  <div class="col-span-1 bg-white p-4 font-semibold text-center text-gray-600">CV Review (Free)</div>
  <div class="col-span-1 bg-white p-4 font-semibold text-center text-gray-600">Revamp + Cover (KES 200)</div>
  <div class="col-span-1 bg-white p-4 font-semibold text-center text-gray-600">From Scratch + Cover (KES 300)</div>
  <div class="col-span-1 bg-white p-4 font-semibold text-center text-gray-600">Career Success Package (KES 500)</div>

  <!-- Table Rows -->
  <template v-for="(feature, index) in features" :key="index">
    <div class="col-span-1 bg-white p-4 text-gray-700 font-medium border-t border-gray-200">{{ feature.name }}</div>
    <div v-for="(packageItem, pkgIndex) in packages" :key="pkgIndex" class="col-span-1 bg-white p-4 flex items-center justify-center border-t border-gray-200">
      <template v-if="feature.values[packageItem.id] === true">
        <span class="text-green-500 text-2xl">✓</span>
      </template>
      <template v-else-if="feature.values[packageItem.id] === false">
        <span class="text-red-500 text-2xl">✕</span>
      </template>
      <template v-else>
        {{ feature.values[packageItem.id] }}
      </template>
    </div>
  </template>
</div>

<!-- Mobile/Tablet Card Layout -->
<div class="lg:hidden">
  <div v-for="(packageItem, index) in packages" :key="index" class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
    <div class="bg-gray-100 p-4 text-center font-bold text-lg text-gray-800">{{ packageItem.name }}</div>
    <ul class="divide-y divide-gray-200">
      <li v-for="(feature, fIndex) in features" :key="fIndex" class="flex items-center justify-between p-4">
        <span class="text-gray-700 font-medium">{{ feature.name }}</span>
        <div class="flex-shrink-0">
          <template v-if="feature.values[packageItem.id] === true">
            <span class="text-green-500 text-xl">✓</span>
          </template>
          <template v-else-if="feature.values[packageItem.id] === false">
            <span class="text-red-500 text-xl">✕</span>
          </template>
          <template v-else>
            <span class="text-gray-600">{{ feature.values[packageItem.id] }}</span>
          </template>
        </div>
      </li>
    </ul>
  </div>
</div>

</div>
</template>

<script setup>
import { ref } from 'vue';

const packages = ref([
{ id: 'free', name: 'CV Review (Free)' },
{ id: 'revamp', name: 'Revamp + Cover (KES 200)' },
{ id: 'scratch', name: 'From Scratch + Cover (KES 300)' },
{ id: 'success', name: 'Career Success Package (KES 500)' },
]);

const features = ref([
{
name: 'Professional CV assessment',
values: {
free: true,
revamp: true,
scratch: true,
success: true,
},
},
{
name: 'ATS optimization tips',
values: {
free: true,
revamp: true,
scratch: true,
success: true,
},
},
{
name: 'CV revamp',
values: {
free: false,
revamp: '1 CV',
scratch: false,
success: '2 CVs',
},
},
{
name: 'CV from scratch',
values: {
free: false,
revamp: false,
scratch: '1 CV',
success: false,
},
},
{
name: 'Tailored cover letter',
values: {
free: false,
revamp: '1',
scratch: '1',
success: '2',
},
},
{
name: 'LinkedIn optimization',
values: {
free: false,
revamp: false,
scratch: false,
success: true,
},
},
{
name: 'Recruiter visibility boost',
values: {
free: false,
revamp: false,
scratch: false,
success: true,
},
},
]);
</script>

<style scoped>
/* Assuming Tailwind CSS is configured in your project. /
/ This section is only needed for any custom styles not covered by Tailwind. */
</style>