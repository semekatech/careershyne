<template>
  <DashboardStats />
</template>



<script setup>
import DashboardStats from "@/components/DashboardStats.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>