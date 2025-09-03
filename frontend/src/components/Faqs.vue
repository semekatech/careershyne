<template>
  <section class="bg-blue-50 dark:bg-slate-800 w-full" id="contact">
    <div class="px-4 py-12 lg:px-20 xl:px-32 w-full">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold  mb-4" style="color: #e11d50" >
          Frequently Asked Questions
        </h1>
        <p class="text-lg text-gray-600">
          Get quick answers about how our platform works for both promoters and brands.
        </p>
      </div>


      
      <!-- FAQ Items -->
      <div class="space-y-4">
        <!-- FAQ 1 -->
        <div class="bg-white rounded-xl shadow-md transition hover:shadow-lg overflow-hidden">
          <button class="faq-toggle w-full flex justify-between items-center p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-800">How do I join as a promoter?</h3>
            <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300"></i>
          </button>
          <div class="faq-content px-6 pb-6 hidden">
            <p class="text-gray-600">
              Simply sign up as a promoter, complete your profile with your social links, and browse available campaigns to apply. Once selected by a brand, you'll receive campaign instructions.
            </p>
          </div>

        </div>

        <!-- FAQ 2 -->
        <div class="bg-white rounded-xl shadow-md transition hover:shadow-lg overflow-hidden">
          <button class="faq-toggle w-full flex justify-between items-center p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-800">How do brands create campaigns?</h3>
            <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300"></i>
          </button>
          <div class="faq-content px-6 pb-6 hidden">
            <p class="text-gray-600">
              After signing up as a brand, go to your dashboard and click “Create Campaign.” Add campaign details, budget, target influencers, and submit for review.
            </p>
          </div>
        </div>

        <!-- FAQ 3 -->
        <div class="bg-white rounded-xl shadow-md transition hover:shadow-lg overflow-hidden">
          <button class="faq-toggle w-full flex justify-between items-center p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-800">How do payments work?</h3>
            <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300"></i>
          </button>
          <div class="faq-content px-6 pb-6 hidden">
            <p class="text-gray-600">
              Brands pay per campaign slot. Promoters earn payouts after completing campaigns as per guidelines. All payments are processed securely via M-Pesa and bank transfers.
            </p>
          </div>
        </div>

        <!-- FAQ 4 -->
        <div class="bg-white rounded-xl shadow-md transition hover:shadow-lg overflow-hidden">
          <button class="faq-toggle w-full flex justify-between items-center p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-800">Are campaigns vetted before going live?</h3>
            <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300"></i>
          </button>
          <div class="faq-content px-6 pb-6 hidden">
            <p class="text-gray-600">
              Yes, all campaigns are reviewed for content, budget, and fairness before being made available to promoters.
            </p>
          </div>
        </div>

        <!-- FAQ 5 -->
        <div class="bg-white rounded-xl shadow-md transition hover:shadow-lg overflow-hidden">
          <button class="faq-toggle w-full flex justify-between items-center p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-800">Can I upgrade or downgrade my plan?</h3>
            <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300"></i>
          </button>
          <div class="faq-content px-6 pb-6 hidden">
            <p class="text-gray-600">
              Yes! You can switch plans anytime from your dashboard. Changes will apply in the next billing cycle.
            </p>
          </div>
        </div>

        <!-- FAQ 6 -->
        <div class="bg-white rounded-xl shadow-md transition hover:shadow-lg overflow-hidden">
          <button class="faq-toggle w-full flex justify-between items-center p-6 text-left">
            <h3 class="text-lg font-semibold text-gray-800">Do I need a large following to join as a promoter?</h3>
            <i class="fas fa-chevron-down text-indigo-600 transition-transform duration-300"></i>
          </button>
          <div class="faq-content px-6 pb-6 hidden">
            <p class="text-gray-600">
              Not necessarily. Some brands target niche audiences or micro-influencers. Just ensure your profile is active and you can deliver engagement.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    const toggles = document.querySelectorAll(".faq-toggle");

    toggles.forEach((toggle) => {
      toggle.addEventListener("click", () => {
        const content = toggle.nextElementSibling;
        const icon = toggle.querySelector("i");
        content.classList.toggle("hidden");
        icon.classList.toggle("rotate-180");
      });
    });
  },
};
</script>

