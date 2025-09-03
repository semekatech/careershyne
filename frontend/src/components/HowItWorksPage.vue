<template>
  <section id="how-it-works" class="relative py-20 bg-white dark:bg-gray-900">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

      <!-- Section Header -->
      <div class="mb-14 text-center">
        <h2 class="text-4xl font-extrabold text-rose-600 dark:text-white mb-4">How It Works</h2>
        <p class="text-gray-600 dark:text-gray-300 text-lg">
          Whether you're a promoter or a brand — we make it easy.
        </p>
      </div>

      <!-- Tabs -->
      <div class="flex justify-center gap-4 mb-12">
        <button
          @click="selectedTab = 'promoters'"
          :class="tabClass('promoters')"
        >
          Promoters & Marketers
        </button>
        <button
          @click="selectedTab = 'brands'"
          :class="tabClass('brands')"
        >
          Brands & Advertisers
        </button>
      </div>

      <!-- Promoters Steps -->
      <div v-if="selectedTab === 'promoters'" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <StepBox number="1" title="Discover Opportunities" text="Explore a growing list of brand campaigns tailored for influencers, creators, and marketers." />
        <StepBox number="2" title="Submit a Bid" text="Pitch your value to brands that match your style. Let them choose you." />
        <StepBox number="3" title="Collaborate & Grow" text="Execute campaigns and track performance from your dashboard." />
      </div>

      <!-- Brands Steps -->
      <div v-if="selectedTab === 'brands'" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <StepBox number="1" title="Post Your Campaign" text="Set your goals, audience, and budget to attract the right talent." />
        <StepBox number="2" title="Review Promoter Bids" text="Choose from a pool of vetted promoters ready to represent your brand." />
        <StepBox number="3" title="Launch & Monitor" text="Track performance, ROI, and engagement in real-time." />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const selectedTab = ref('promoters')

const tabClass = (tab) =>
  `px-6 py-2 rounded-full text-sm font-medium transition-all border ${
    selectedTab.value === tab
      ? 'bg-rose-600 text-white shadow-md border-rose-600'
      : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 border-gray-300 dark:border-gray-700'
  }`

const StepBox = {
  props: {
    number: String,
    title: String,
    text: String,
  },
  template: `
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md hover:shadow-xl transition duration-300 h-full flex flex-col items-center text-center">
      <div class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-rose-600 text-white text-xl font-bold shadow-lg">
        {{ number }}
      </div>
      <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ title }}</h4>
      <p class="text-gray-600 dark:text-gray-300 text-base">{{ text }}</p>
    </div>
  `,
}
</script>
