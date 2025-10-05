<template>
  <section class="bg-white py-16" id="ai-pricing">
    <div class="text-center mb-1">
      <p class="text-sm font-semibold text-orange-500 uppercase tracking-wide">
        Clear. Simple. Affordable.
      </p>
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
       Pay As You Go
      </h2>
      <p>Only pay for what you use. Build your own plan based on your needs.</p>
    </div>
    <div class="max-w-5xl mx-auto px-1">
      <div
        class="bg-white p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start"
      >
        <!-- Budget Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
         
          <p class="text-gray-600 mb-6">
            Adjust the slider to see the corresponding features.
          </p>

          <label class="block text-gray-700 text-sm font-medium mb-2"
            >Amount</label
          >
          <div
            class="flex items-center bg-gray-50 border border-gray-300 rounded-lg p-2 mb-6"
          >
            <!-- <span class="mr-2">🇰🇪</span> -->
            <span class="text-gray-700 mr-3">KES</span>
            <input
              type="number"
              readonly
              v-model="budget"
              min="100"
              max="5000"
              step="100"
              class="bg-transparent text-gray-900 w-24 outline-none"
            />
          </div>

          <input
            type="range"
            min="100"
            max="5000"
            step="100"
            v-model="budget"
            class="w-full accent-orange-500 appearance-none h-2 rounded-lg outline-none cursor-pointer bg-gray-200"
          />
        </div>

        <!-- Items Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-6">You Will Receive</h3>

          <ul class="mt-8 space-y-4 text-sm">
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2"
                  >description</span
                >CV Items</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.cv
              }}</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2">mail</span>Cover
                Letters</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.coverLetter
              }}</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2"
                  >check_circle_outline</span
                >Eligibility Checks</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.eligibility
              }}</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2">email</span
                >Email Credits</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.email
              }}</span>
            </li>
          </ul>

          <hr class="border-gray-200 mb-6" />
          <div
            class="flex justify-between text-lg font-bold text-gray-900 mb-6"
          >
            <span>Total cost</span>
            <span>KES {{ items.total }}</span>
          </div>

          <button
            :disabled="items.total === 0"
            @click="openModal = true"
            class="w-full py-3 rounded-lg font-semibold text-white bg-orange-500 hover:bg-orange-600 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Get Started
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
  
  </section>
</template>

<script setup>
import { ref, reactive, computed, watch } from "vue";
// Budget
const budget = ref(100);

// Prices
const prices = reactive({
  cv: 20,
  coverLetter: 20,
  eligibility: 20,
  email: 10,
});

// Computed items
const items = computed(() => {
  const P = budget.value;
  if (P <= 0 || P % 100 !== 0)
    return { cv: 0, coverLetter: 0, eligibility: 0, email: 0, total: 0 };
  const n = Math.floor(P / 100);
  const cv = 2 * n;
  const eligibility = n;
  const email = 2 * Math.floor(P / 200);
  const coverLetter = 2 * n - Math.floor(P / 200);
  const total =
    cv * prices.cv +
    coverLetter * prices.coverLetter +
    eligibility * prices.eligibility +
    email * prices.email;
  return { cv, coverLetter, eligibility, email, total };
});


</script>
