<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- All Campaigns -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-indigo-100 text-indigo-600">
          <font-awesome-icon :icon="['fas', 'bullhorn']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-bold text-gray-500" style="color:black;font-weight: bold">Total Campaigns</p>
          <p class="text-3xl font-bold text-gray-800">{{ campaignCount }}</p>
        </div>
      </div>
    </div>

    <!-- Pending Requests -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-yellow-100 text-yellow-600">
          <font-awesome-icon :icon="['fas', 'hourglass-half']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-500" style="color:black;font-weight: bold">Pending Campaigns</p>
          <p class="text-3xl font-bold text-gray-800">{{ pendingRequests }}</p>
        </div>
      </div>
    </div>

    <!-- Active Campaigns -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-green-100 text-green-600">
          <font-awesome-icon :icon="['fas', 'check-circle']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-500" style="color:black;font-weight: bold">Active Campaigns</p>
          <p class="text-3xl font-bold text-gray-800">{{ approvedEngagements }}</p>
        </div>
      </div>
    </div>

    <!-- Promoter Requests -->
   <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
  <div class="flex items-center">
    <div class="p-4 rounded-full bg-red-100 text-red-600">
      <font-awesome-icon :icon="['fas', 'user-plus']" class="text-2xl" />
    </div>
    <div class="ml-5">
      <p class="text-sm font-semibold text-gray-500" style="color:black;font-weight: bold">Promotion Requests</p>
      <p class="text-3xl font-bold text-gray-800">{{ teamMembers }}</p>
    </div>
  </div>
</div>

  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import DashboardService from "@/services/dashboardService";

const campaignCount = ref(0);
const pendingRequests = ref(0);
const approvedEngagements = ref(0);
const teamMembers = ref(0);

onMounted(async () => {
  try {
    const stats = await DashboardService.getDashboardStats();
    campaignCount.value = stats.campaigns;
    pendingRequests.value = stats.pending;
    approvedEngagements.value = stats.approved;
    teamMembers.value = stats.team;
  } catch (error) {
    console.error("Failed to load dashboard stats:", error);
  }
});
</script>
