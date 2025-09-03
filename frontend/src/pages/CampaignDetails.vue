<template>
  <div class="bg-white min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4">
      
      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center h-[400px]">
        <svg class="animate-spin h-10 w-10 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
          ></path>
        </svg>
      </div>

      <!-- Campaign Content -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Campaign Image & Thumbnails -->
        <div>
          <div class="rounded-lg overflow-hidden border">
            <img
              :src="mainImage"
              alt="Main Asset"
              class="w-full h-[400px] object-cover"
            />
          </div>

          <div class="flex flex-wrap gap-3 mt-4">
            <img
              v-for="(asset, idx) in campaign?.assets"
              :key="idx"
              :src="`https://demo.ngumzo.com/storage/${asset}`"
              @click="changeImage(`https://demo.ngumzo.com/storage/${asset}`)"
              class="w-20 h-20 object-cover rounded-md border cursor-pointer hover:opacity-80"
              :class="{
                'ring-2 ring-pink-500': mainImage === `https://demo.ngumzo.com/storage/${asset}`
              }"
            />
          </div>
        </div>

        <!-- Campaign Details -->
        <div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">
            {{ campaign?.title }}
          </h1>
          <p class="text-sm text-gray-500 mb-4">
            Created: {{ formatDate(campaign?.created_at) }}
          </p>

          <div class="mb-6 space-y-2">
            <p class="text-lg font-semibold text-gray-700">
              {{ campaign?.brand }}
              <span class="text-sm text-gray-500"> | {{ campaign?.website }}</span>
            </p>
            <p class="text-sm">
              <strong>Start:</strong> {{ formatDate(campaign?.start_date) }} |
              <strong>End:</strong> {{ formatDate(campaign?.end_date) }}
            </p>
            <p class="text-sm">
              <strong>Budget:</strong> {{ campaign?.budget }}
            </p>
          </div>

          <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">About</h2>
            <p class="text-gray-700 text-sm">{{ campaign?.about }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <p><strong>Target Age:</strong> {{ campaign?.target_age }}</p>
            <p><strong>Target Location:</strong> {{ campaign?.residence }}</p>
            <p><strong>Target Interests:</strong> {{ campaign?.target_interests }}</p>
            <p><strong>Primary Goal:</strong> {{ campaign?.primary_goal }}</p>
          </div>

          <div class="mt-6 space-y-3 text-sm">
            <div>
              <h3 class="font-semibold">Brief:</h3>
              <div v-html="campaign?.brief" class="prose prose-sm text-gray-700"></div>
            </div>
            <div>
              <h3 class="font-semibold">Guidance:</h3>
              <div v-html="campaign?.guidance" class="prose prose-sm text-gray-700"></div>
            </div>
            <div>
              <h3 class="font-semibold">Eligibility:</h3>
              <div v-html="campaign?.eligibility" class="prose prose-sm text-gray-700"></div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import campaignService from "@/services/campaignService";
import Hashids from "hashids";

const route = useRoute();
const campaign = ref(null);
const mainImage = ref("");
const loading = ref(true); // <-- here

const hashids = new Hashids("32esdf33esdfg4321", 6);

function decodeId(hash) {
  return hashids.decode(hash)[0];
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString();
}

function changeImage(src) {
  mainImage.value = src;
}

onMounted(async () => {
  const id = decodeId(route.params.id);
  const res = await campaignService.fetch(id);
  campaign.value = res.data;

  if (res.data.assets.length > 0) {
    mainImage.value = `https://demo.ngumzo.com/storage/${res.data.assets[0]}`;
  }

  loading.value = false;
});
</script>

<style scoped>
.prose {
  max-width: 100%;
}
</style>
