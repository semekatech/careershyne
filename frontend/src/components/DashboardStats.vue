<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <div
      class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow"
    >
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-yellow-100 text-yellow-600">
          <font-awesome-icon
            :icon="['fas', 'hourglass-half']"
            class="text-2xl"
          />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Pending Requests</p>
          <p class="text-3xl font-bold text-gray-800">{{ pendingRequests }}</p>
        </div>
      </div>
    </div>

    <!-- Approved Requests -->
    <div
      class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow"
    >
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-green-100 text-green-600">
          <font-awesome-icon :icon="['fas', 'check-circle']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Approved Requests</p>
          <p class="text-3xl font-bold text-gray-800">
            {{ approvedRequests }}
          </p>
        </div>
      </div>
    </div>

    <!-- Bid Limit -->
    <div
      class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow"
    >
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-red-100 text-red-600">
          <font-awesome-icon :icon="['fas', 'user-plus']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Requests Limit</p>
          <p class="text-3xl font-bold text-gray-800">{{
            auth.user?.limit
          }}</p>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import DashboardService from "@/services/promoterdashboardService";
import { useAuthStore } from '@/stores/auth'
const auth = useAuthStore()
const pendingRequests = ref(0);
const approvedRequests = ref(0);
const teamMembers = ref(0);
onMounted(async () => {
   await auth.refreshUser();
  try {
    const stats = await DashboardService.getDashboardStats();
    pendingRequests.value = stats.pending;
    approvedRequests.value = stats.approved;
    teamMembers.value = stats.team;
  } catch (error) {
    console.error("Failed to load dashboard stats:", error);
  }
   
});
</script>
