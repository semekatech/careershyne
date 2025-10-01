<template>
  <DashboardStats v-if="auth.user?.role !== '1098'" />
  <UserDashboard v-else />
</template>

<script setup>
import DashboardStats from "@/components/Dashboard/DashboardStats.vue";
import UserDashboard from "@/components/Dashboard/UserDashboard.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();

</script>