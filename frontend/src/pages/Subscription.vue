<template>
  <div class="w-full max-w-7xl mx-auto">
    <div class="text-center mb-12 md:mb-16">
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
        Choose Your Perfect Plan
      </h2>
      <!-- <p class="max-w-2xl mx-auto text-gray-600 text-lg">
        Select the best pricing option for your needs.
      </p> -->

      <!-- Toggle only if not locked to one role -->
      <div class="inline-flex items-center mt-8 bg-gray-100 p-1 rounded-lg" v-if="!isAutoRole">
        <button
          class="px-4 py-2 rounded-md font-medium"
          :class="role === 'promoter' ? 'bg-white shadow text-gray-800' : 'text-gray-600'"
          @click="role = 'promoter'"
        >
          For Promoters
        </button>
        <button
          class="px-4 py-2 rounded-md font-medium"
          :class="role === 'brand' ? 'bg-white shadow text-gray-800' : 'text-gray-600'"
          @click="role = 'brand'"
        >
          For Brands
        </button>
      </div>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div
        v-for="(plan, index) in filteredPlans"
        :key="index"
        :class="[
          'pricing-card rounded-2xl transition-all hover:scale-105 hover:-translate-y-1',
          plan.popular
            ? 'bg-gradient-to-br from-primary-600 to-primary-700 text-white shadow-lg'
            : 'bg-white border border-gray-200 shadow-sm'
        ]"
        :style="plan.popular ? 'background:#db2777' : ''"
      >
        <div class="p-6 md:p-8">
          <h3 class="text-xl font-bold mb-2" :class="plan.popular ? 'text-white' : 'text-gray-900'">
            {{ plan.title }}
          </h3>
          <p class="mb-6" :class="plan.popular ? 'text-primary-100' : 'text-gray-600'">
            {{ plan.description }}
          </p>
          <div class="flex items-baseline mb-8">
            <span class="text-4xl md:text-5xl font-extrabold">{{ plan.price }}</span>
            <span class="ml-2 font-medium" :class="plan.popular ? 'text-primary-100' : 'text-gray-500'">
              {{ plan.duration }}
            </span>
          </div>
          <button
            class="w-full py-3 px-6 rounded-lg font-medium mb-6" style="color:black"
            :class="plan.popular ? 'bg-white text-primary-700 hover:bg-primary-50' : 'border border-primary-600 text-primary-600 hover:bg-primary-50'"
          >
            Get Started
          </button>
          <ul class="space-y-4" :class="plan.popular ? 'text-white' : 'text-gray-600'">
            <li v-for="(feature, i) in plan.features" :key="i" class="flex items-start">
              <svg class="w-5 h-5 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>{{ feature }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

const userRole = auth.user?.role || "promoter";
const isAutoRole = true; // true to auto-select based on user role, false to show toggle
const role = ref(userRole);

// Plan definitions
const plans = {
  promoter: [
    {
      title: "Starter",
      description: "Perfect for getting started",
      price: "KES 0",
      duration: "/ month",
      features: ["1 Campaign Per Month"],
    },
    {
      title: "Growth",
      description: "For growing promoters",
      price: "KES 500",
      duration: "/ month",
      features: ["Up to 4 Campaigns"],
      popular: true,
    },
    {
      title: "Unlimited",
      description: "Best for active promoters",
      price: "KES 1,000",
      duration: "/ month",
      features: ["Unlimited Campaigns"],
    },
  ],
  brand: [
    {
      title: "Solo",
      description: "Great for small campaigns",
      price: "KES 1,000",
      duration: "",
      features: ["1 Campaign Slots"],
    },
    {
      title: "Business",
      description: "Ideal for agencies",
      price: "KES 4,500",
      duration: "",
      features: ["5 Campaign Slots"],
      popular: true,
    },
    {
      title: "Enterprise",
      description: "Unlimited campaigns all year",
      price: "KES 10,000",
      duration: "",
      features: ["Unlimited Slots", "Valid for 1 Year"],
    },
  ],
};

// Hide free plans
const filteredPlans = computed(() => {
  const hideFreeTitle = {
    promoter: "Starter",
    brand: "Solo",
  };
  return plans[role.value].filter(plan => plan.title !== hideFreeTitle[role.value]);
});
</script>

<style>
body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background-color: #f9fafb;
}
.pricing-popular {
  position: relative;
  overflow: hidden;
}
.pricing-popular::before {
  content: 'POPULAR';
  position: absolute;
  top: 26px;
  right: -26px;
  transform: rotate(45deg);
  background-color: #0ea5e9;
  color: white;
  padding: 2px 28px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  z-index: 1;
}
</style>
