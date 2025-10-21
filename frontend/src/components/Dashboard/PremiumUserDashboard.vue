<template>
  <div class="w-full px-2 sm:px-4 md:px-6 lg:px-8">
    <!-- Banner -->
    <PremiumUserStats />
    <br/>
    <PremiumUserBanner />

    <!-- Space -->
    <div class="mt-4 sm:mt-6">
      <PremiumUserTabs />
    </div>
  </div>
</template>



<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import PremiumUserBanner from "@/components/Dashboard/PremiumUserBanner.vue";
import PremiumUserStats from "@/components/Dashboard/PremiumUserStats.vue";
import PremiumUserTabs from "@/components/Dashboard/PremiumUserTabs.vue";

const auth = useAuthStore();
onMounted(async () => {
  await auth.refreshUser();
});
</script>
