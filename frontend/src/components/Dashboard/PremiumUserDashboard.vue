<template>
  <div class="w-full px-2 sm:px-4 md:px-6 lg:px-8">
    <!-- Banner -->
    <!-- <UserBanner /> -->

    <!-- Space -->
    <div class="mt-4 sm:mt-6">
      <UserTabs />
    </div>
  </div>
</template>



<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";

import UserBanner from "@/components/Dashboard/UserBanner.vue";
import UserTabs from "@/components/Dashboard/UserTabs.vue";

const auth = useAuthStore();
onMounted(async () => {
  await auth.refreshUser();
});
</script>
