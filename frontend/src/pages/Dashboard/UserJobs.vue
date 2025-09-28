<template>
  <UsersJobTable />
</template>
<script setup>
import UsersJobTable from "@/components/Dashboard/UsersJobTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>