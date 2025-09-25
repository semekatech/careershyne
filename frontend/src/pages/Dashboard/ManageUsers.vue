<template>
  <UsersTable />
</template>



<script setup>
import UsersTable from "@/components/Dashboard/UsersTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>