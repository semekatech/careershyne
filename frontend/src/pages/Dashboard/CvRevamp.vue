<template>
  <cv />
</template>



<script setup>
import cv from "@/components/CvRevampGenerator.vue";
import { useAuthStore } from "@/stores/auth";
const auth = useAuthStore();
</script>