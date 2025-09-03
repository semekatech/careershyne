<template>
  <main
    class="bg-gradient-to-br from-rose-50 to-white dark:from-gray-900 dark:to-gray-800"
  >
    <TheWelcome />
    <CampaignsList />
    <FooterSection />
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import campaignService from "@/services/campaignService";
import HeroSection from "@/components/HeroSection.vue";
import TheWelcome from "@/components/TheWelcome.vue";
import CampaignsList from "@/components/CampaignsList.vue";
import FooterSection from "@/components/FooterSection.vue";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const campaign = ref(null);
const mainImage = ref("");
const showBidForm = ref(false);
const bidAmount = ref("");
const note = ref("");

const auth = useAuthStore();
const { user } = auth;

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString();
}

function changeImage(src) {
  mainImage.value = src;
}


</script>

<style scoped>
.prose {
  max-width: 100%;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
