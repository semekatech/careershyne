<template>
  <main style="background: #fffbf7">
    <TheWelcome />
    <HeroSection />
    <Services />
    <WhyChooseUs />
    <Pricing />
    <Community />
    <FooterSection />
  </main>
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import HeroSection from "@/components/HeroSection.vue";
import Services from "@/components/Services.vue";
import WhyChooseUs from "@/components/WhyChooseUs.vue";
import Community from "@/components/Community.vue";
import Pricing from "@/components/Pricing.vue";
import FooterSection from "@/components/FooterSection.vue";
import { useVisitorLogger } from "@/composables/useVisitorLogger";
useVisitorLogger("HomePage");
</script>

