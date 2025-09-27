<template>
  <JobsTable />
</template>



<script setup>
import JobsTable from "@/components/Dashboard/JobsTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>