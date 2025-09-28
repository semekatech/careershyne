<template>
  <UsersActivity />
</template>
<script setup>
import UsersActivity from "@/components/Dashboard/UserActivity.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>