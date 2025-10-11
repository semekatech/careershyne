<template>
  <section class="bg-white py-16">
    <div class="max-w-5xl mx-auto px-4">

      <!-- Header -->
      <div class="text-center mb-12">
        <p class="text-sm font-semibold text-orange-500 uppercase tracking-wide">
            Clear. Simple. Affordable.
        </p>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
          Fair Price. Real Value.
        </h2>
      </div>

      <!-- Parent Card -->
      <div class="bg-white rounded-3xl shadow-xl p-8 grid grid-cols-1 md:grid-cols-2 gap-8 border border-gray-200">

        <!-- Left Inner Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-2">See what you get</h3>
          <p class="text-gray-600 mb-6">
            Slide or input the amount you want to see the items you will receive.
          </p>

          <!-- Amount Input -->
          <label class="block text-gray-700 text-sm font-medium mb-2">Amount</label>
          <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg p-2 mb-6">
            <span class="mr-2">🇰🇪</span>
            <span class="text-gray-700 mr-3">KES</span>
            <input
              type="number"
              v-model="budget"
              min="100"
              max="10000"
              step="100"
              class="bg-transparent text-gray-900 w-24 outline-none"
            />
          </div>

          <!-- Slider -->
          <input
            type="range"
            min="100"
            max="10000"
            step="100"
            v-model="budget"
            class="w-full accent-orange-500"
          />
        </div>

        <!-- Right Inner Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-6">You Will Receive</h3>

          <ul class="space-y-4 mb-6">
            <li v-for="(product, key) in products" :key="key" class="flex justify-between">
              <span class="text-gray-700">{{ product.label }}</span>
              <span class="text-orange-600 font-semibold">{{ calculateItems(product.price) }}</span>
            </li>
          </ul>

          <hr class="border-gray-200 mb-6" />

          <div class="flex justify-between text-lg font-bold text-gray-900 mb-6">
            <span>Total amount</span>
            <span>KES {{ budget }}</span>
          </div>

          <!-- CTA Button -->
          <button
            @click="$router.push('/login')"
            class="w-full py-3 rounded-lg font-semibold text-white bg-orange-500 hover:bg-orange-600 transition shadow-md"
          >
            Get Started Today
          </button>

            <!-- <button
            class="w-full py-3 rounded-lg font-semibold text-white bg-orange-500 hover:bg-orange-600 transition shadow-md"
          >
            Pay KES {{ budget }} with M-PESA
          </button> -->
        </div>

      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "PricingSection",
  data() {
    return {
      budget: 500,
      products: {
        cv: { label: "CV Items", price: 20 },
        coverLetter: { label: "Cover Letters", price: 20 },
        eligibility: { label: "Eligibility Checks", price: 20 },
        email: { label: "Email Credits", price: 10 }
      }
    }
  },
  methods: {
    calculateItems(price) {
      return Math.floor(this.budget / (price * Object.keys(this.products).length));
    }
  }
}
</script>
