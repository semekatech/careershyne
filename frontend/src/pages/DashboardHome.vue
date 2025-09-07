<template>
  <DashboardStats />
</template>



<script setup>
import DashboardCharts from "@/components/DashboardCharts.vue";
import PromoterChart from "@/components/PromoterChart.vue";
import DashboardStats from "@/components/DashboardStats.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>