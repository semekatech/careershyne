<template>
  <PremiumJobTable />
</template>
<script setup>
import PremiumJobTable from "@/components/Dashboard/PremiumJobTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>