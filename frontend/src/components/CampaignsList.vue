<script setup>
import { onMounted, ref, computed } from "vue";
import homeService from "@/services/homeService";

const campaigns = ref([]);
const searchQuery = ref("");
const selectedLocation = ref("");

// Fetch all unique locations
const locations = computed(() => {
  const all = campaigns.value.map((c) => c.location).filter(Boolean);
  return [...new Set(all)];
});

onMounted(async () => {
  try {
    campaigns.value = await homeService.getAll();
  } catch (error) {
    console.error("Failed to load campaigns:", error);
  }
});

// Filtered campaigns based on search and location
const filteredCampaigns = computed(() => {
  return campaigns.value.filter((campaign) => {
    const query = searchQuery.value.toLowerCase();
    const matchesSearch =
      campaign.title.toLowerCase().includes(query) ||
      (campaign.about && campaign.about.toLowerCase().includes(query)) ||
      (campaign.brand && campaign.brand.toLowerCase().includes(query)); 

    const matchesLocation =
      !selectedLocation.value || campaign.location === selectedLocation.value;

    return matchesSearch && matchesLocation;
  });
});

const slugify = (text) => {
  return text
    .toString()
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9 -]/g, "")
    .replace(/\s+/g, "-")
    .replace(/-+/g, "-");
};
</script>

<template>
  <div
    class="relative bg-gradient-to-br from-primary-50 to-white dark:from-gray-800 dark:to-gray-900 min-h-screen"
  >
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
      <!-- Heading -->
      <h1 class="text-4xl font-extrabold tracking-tight text-center text-rose-600 dark:text-white mb-4">
        🎯 Browse Campaigns
      </h1>
      <br />

      <!-- Filters -->
      <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8 w-full">
        <!-- Search Input -->
        <div class="w-full sm:w-1/2">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="🔍 Search campaigns..."
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-primary-500 dark:bg-gray-900 dark:text-white"
          />
        </div>

        <!-- Location Filter -->
        <div class="w-full sm:w-1/2">
          <select
            v-model="selectedLocation"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg dark:bg-gray-900 dark:text-white"
          >
            <option value="">🌍 All Locations</option>
            <option
              v-for="location in locations"
              :key="location"
              :value="location"
            >
              {{ location }}
            </option>
          </select>
        </div>
      </div>

      <!-- Campaigns Grid -->
      <div v-if="filteredCampaigns.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        <div
          v-for="campaign in filteredCampaigns"
          :key="campaign.id"
          class="flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100 dark:border-gray-700"
        >
          <!-- Image -->
          <div class="relative">
            <img
              v-if="campaign.assets.length > 0"
              :src="`https://demo.ngumzo.com/storage/${campaign.assets[0]}`"
              alt="Campaign Image"
              class="w-full h-48 object-cover"
            />
          </div>

          <!-- Campaign Content -->
          <div class="flex flex-col flex-grow p-5">
            <h3 class="text-lg font-semibold text-primary-800 dark:text-white mb-1">
              {{ campaign.title }}
            </h3>

            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium mb-1">
              🏢 {{ campaign.brand || 'Unknown Company' }}
            </p>

            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">
              {{ campaign.about || "No description provided." }}
            </p>

            <div class="flex justify-between items-center mb-4">
              <span class="inline-block bg-rose-100 text-rose-700 dark:bg-rose-800 dark:text-rose-100 font-semibold text-xs px-3 py-1 rounded-full">
                Compensation: {{ campaign.budget || "N/A" }}
              </span>
            </div>

            <!-- View Button -->
            <router-link
              style="background: #e11d50"
              :to="`/campaigns/${campaign.id}/${slugify(campaign.title)}`"
              class="mt-auto w-full bg-primary-600 text-white py-2 px-4 text-sm rounded-lg hover:bg-primary-700 transition-colors text-center"
            >
              👁️ View Campaign
            </router-link>
          </div>
        </div>
      </div>

      <!-- No Results Message -->
      <div v-else class="text-center text-gray-500 dark:text-gray-400 mt-12">
        🚫 No campaigns found matching your search or location.
      </div>
    </div>
  </div>
</template>

