<template>
  <CategoriesTable />
</template>



<script setup>
import CategoriesTable from "@/components/Dashboard/CategoriesTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>