<template>
  <emailGenerator />
</template>



<script setup>
import emailGenerator from "@/components/EmailGenerator";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>