<template>
  <SavedJobsTable />
</template>



<script setup>
import SavedJobsTable from "@/components/Dashboard/SavedJobTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>