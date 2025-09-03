<template>
  <main style="background: #fffbf7">
    <TheWelcome />
    <HeroSection />
    <HowItWorks />
    <FeaturedCampaigns />
       <ForBrands />
    <PricingPlans />
     <!-- <Faqs /> -->
    <FooterSection />
  </main>
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import HeroSection from "@/components/HeroSection.vue";
import HowItWorks from "@/components/HowItWorks.vue";
import FeaturedCampaigns from "@/components/FeaturedCampaigns.vue";
import PricingPlans from "@/components/PricingPlans.vue";
import ForBrands from "@/components/ForBrands.vue";
// import Faqs from "@/components/Faqs.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>
