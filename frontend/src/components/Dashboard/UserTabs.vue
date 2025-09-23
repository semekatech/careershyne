<template>
  <div>
    <div class="bg-[#f3f4f6] ">
      <div class="flex space-x-6 px-4 sm:px-6">
        <button
          v-for="(tab, index) in tabs"
          :key="index"
          @click="setActiveTab(index)"
          :class="[
            'relative py-4 px-1 text-sm font-medium transition-colors duration-200',
            activeTab === index
              ? 'text-orange-500 font-bold' 
              : 'text-black hover:text-gray-700',
          ]"
        >
          {{ tab }}
          <span
            v-if="activeTab === index"
            class="absolute inset-x-0 bottom-0 h-1 bg-[#2c3137] rounded-t-sm"
          ></span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const tabs = ['CV Revamp', 'Cover Letters', 'Email Templates', 'Interviews'];
const activeTab = ref(0);

const setActiveTab = (index) => {
  activeTab.value = index;
};
</script>
