<template>
  <section class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-2 lg:px-8">
      
      <!-- Header -->
      <div class="text-center mb-12">
        <p class="text-sm font-semibold text-orange-500 uppercase tracking-wide">
            Clear. Simple. Affordable.
        </p>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
          Fair Price. Real Value.
        </h2>
      </div>

      <!-- Pricing Grid -->
      <div class="flex flex-col md:flex-row justify-center items-stretch gap-12">
        
        <!-- Free Plan -->
        <div class="relative bg-white rounded-xl shadow-xl overflow-hidden w-[380px] border-2 border-orange-500 min-h-[600px] flex flex-col">
          <div class="p-6 pt-12 sm:p-8 sm:pt-16 flex-1 flex flex-col">
            <h2 class="text-2xl font-bold text-orange-600 mb-4">Basic</h2>

            <!-- Crossed old price -->
            <p class="text-xl text-gray-500 font-semibold">Free</p>
            
            <!-- New price -->
            <p class="text-5xl font-extrabold text-gray-900 mb-2">
              KES 0
              <span class="text-xl font-semibold text-gray-500">/month</span>
            </p>

            <button class="w-full mt-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-[#32383f] transition-colors duration-300 shadow-md">
              Get Started
            </button>

            <div class="mt-8 flex-1">
              <h3 class="text-base font-semibold text-gray-700 mb-3">Included Features</h3>
              <ul class="space-y-3">
                <li v-for="(feature, index) in freeFeatures" :key="index" class="flex items-start">
                  <svg class="flex-shrink-0 w-6 h-6 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span class="ml-3 text-gray-700 text-base">{{ feature }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Premium Plan -->
        <div class="relative bg-white rounded-xl shadow-xl overflow-hidden w-[380px] border-2 border-orange-500 min-h-[600px] flex flex-col">
          <div class="absolute top-0 right-0 bg-orange-500 text-white text-xs font-semibold uppercase px-4 py-1 rounded-bl-xl">
            Most Popular
          </div>

          <div class="p-6 pt-12 sm:p-8 sm:pt-16 flex-1 flex flex-col">
            <h2 class="text-2xl font-bold text-orange-600 mb-4">Premium</h2>
            
            <!-- Crossed old price -->
            <p class="text-xl text-gray-500 font-semibold line-through">KES 1,500</p>
            
            <!-- New price -->
            <p class="text-5xl font-extrabold text-gray-900 mb-2">
              KES 1,000
              <span class="text-xl font-semibold text-gray-500">/month</span>
            </p>

            <button class="w-full mt-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-[#32383f] transition-colors duration-300 shadow-md">
              Subscribe Now
            </button>

            <div class="mt-8 flex-1">
              <h3 class="text-base font-semibold text-gray-700 mb-3">Included Features</h3>
              <ul class="space-y-3">
                <li v-for="(feature, index) in premiumFeatures" :key="index" class="flex items-start">
                  <svg class="flex-shrink-0 w-6 h-6 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <span :class="{'ml-3 font-bold': feature.highlight}" class="ml-3 text-gray-700 text-base">
                    {{ feature.text }}
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "AiPricing",
  data() {
    return {
      freeFeatures: [
        "1 CV Revamp",
        "1 Cover Letter",
        "1 Email Draft",
        "1 Job Eligibility Check"
      ],
      premiumFeatures: [
        { text: "5 CV Revamps", highlight: true },
        { text: "5 Cover Letters", highlight: false },
        { text: "10 Email Drafts", highlight: false },
        { text: "8 Job Eligibility Checks", highlight: false },
        { text: "Priority Support", highlight: false }
      ]
    }
  }
}
</script>
