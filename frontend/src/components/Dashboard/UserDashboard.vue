<template>
  <div>
    <UserBanner />
    <br/> 
    <UserTabs />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import DashboardService from "@/services/dashboardService";
import { useAuthStore } from "@/stores/auth";
import OrdersGraph from "@/components/OrdersGraph.vue";
import UserBanner from "@/components/Dashboard/UserBanner.vue";
import UserTabs from "@/components/Dashboard/UserTabs.vue";
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
