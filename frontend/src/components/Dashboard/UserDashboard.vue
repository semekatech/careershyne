<template>
  <div>
    <UserBanner />
    <br/> 
    <UserTabs />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import UserBanner from "@/components/Dashboard/UserBanner.vue";
import UserTabs from "@/components/Dashboard/UserTabs.vue";
const auth = useAuthStore();
onMounted(async () => {
  await auth.refreshUser();
});
</script>
