<template>
  <PromoterStats v-if="auth.user?.role === 'promoter'" />
  <DashboardStats v-else />

  <PromoterChart v-if="auth.user?.role === 'promoter'" />
  <DashboardCharts v-else />
</template>



<script setup>
import DashboardStats from "@/components/DashboardStats.vue";
import DashboardCharts from "@/components/DashboardCharts.vue";
import PromoterChart from "@/components/PromoterChart.vue";
import PromoterStats from "@/components/PromoterStats.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>