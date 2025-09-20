<template>
  <coverletter />
</template>



<script setup>
import coverletter from "@/components/CoverletterGenerator.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>