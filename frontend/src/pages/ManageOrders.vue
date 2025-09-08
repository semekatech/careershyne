<template>
  <OrdersTable />
</template>



<script setup>
import OrdersTable from "@/components/OrdersTable.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>