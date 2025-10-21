<template>
  <DashboardStats v-if="auth.user?.role !== '1098' && auth.user?.user_type !== 'registered'" />
  <PremiumUserDashboard v-else-if="auth.user?.user_type === 'registered'" />
  <UserDashboard v-else />
</template>

<script setup>
import DashboardStats from "@/components/Dashboard/DashboardStats.vue";
import UserDashboard from "@/components/Dashboard/UserDashboard.vue";
import PremiumUserDashboard from "@/components/Dashboard/PremiumUserDashboard.vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
</script>
