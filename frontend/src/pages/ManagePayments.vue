<template>
  <PaymentsTable />
</template>
<script setup>
import PaymentsTable from "@/components/PaymentsTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>