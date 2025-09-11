<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <!-- Total Orders -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-yellow-100 text-yellow-600">
          <font-awesome-icon :icon="['fas', 'hourglass-half']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Total Orders</p>
          <p class="text-3xl font-bold text-gray-800">{{ allRequests }}</p>
        </div>
      </div>
    </div>

    <!-- Pending Orders -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-green-100 text-green-600">
          <font-awesome-icon :icon="['fas', 'hourglass-half']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Pending Orders</p>
          <p class="text-3xl font-bold text-gray-800">{{ pendingRequests }}</p>
        </div>
      </div>
    </div>

    <!-- Approved Orders -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-red-100 text-red-600">
          <font-awesome-icon :icon="['fas', 'check-circle']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Approved Orders</p>
          <p class="text-3xl font-bold text-gray-800">{{ approvedRequests }}</p>
        </div>
      </div>
    </div>

    <!-- Total Expected -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-yellow-100 text-yellow-600">
          <font-awesome-icon :icon="['fas', 'hourglass-half']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Total Expected</p>
          <p class="text-3xl font-bold text-gray-800">{{ totalAmount }}</p>
        </div>
      </div>
    </div>

    <!-- Total Pending -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-green-100 text-green-600">
          <font-awesome-icon :icon="['fas', 'hourglass-half']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Total Pending</p>
          <p class="text-3xl font-bold text-gray-800">{{ totalPendingAmount }}</p>
        </div>
      </div>
    </div>

    <!-- Total Received -->
    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
      <div class="flex items-center">
        <div class="p-4 rounded-full bg-red-100 text-red-600">
          <font-awesome-icon :icon="['fas', 'check-circle']" class="text-2xl" />
        </div>
        <div class="ml-5">
          <p class="text-sm font-semibold text-gray-800">Total Received</p>
          <p class="text-3xl font-bold text-gray-800">{{ totalApprovedAmount }}</p>
        </div>
      </div>
    </div>

    <!-- Paid Orders Graph (full width) -->
    <div class="col-span-1 md:col-span-2 lg:col-span-3">
      <OrdersGraph :graphData="graphData" class="h-96 w-full" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import DashboardService from "@/services/dashboardService";
import { useAuthStore } from "@/stores/auth";
import OrdersGraph from "@/components/OrdersGraph.vue";

const auth = useAuthStore();
const pendingRequests = ref(0);
const approvedRequests = ref(0);
const totalAmount = ref(0);
const totalPendingAmount = ref(0);
const totalApprovedAmount = ref(0);
const allRequests = ref(0);
const graphData = ref([]);

onMounted(async () => {
  await auth.refreshUser();
  try {
    const stats = await DashboardService.getDashboardStats();
    pendingRequests.value = stats.pending;
    approvedRequests.value = stats.approved;
    allRequests.value = stats.all;
    totalAmount.value = stats.totalAmount;
    totalApprovedAmount.value = stats.totalApprovedAmount;
    totalPendingAmount.value = stats.totalPendingAmount;
    graphData.value = stats.graphData; // ✅ set graph data
  } catch (error) {
    console.error("Failed to load dashboard stats:", error);
  }
});
</script>
