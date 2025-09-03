<template>
  <div class="w-full max-w-7xl mx-auto">
    <div class="text-center mb-12 md:mb-16">
      <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
        Choose Your Perfect Plan
      </h2>
      <p class="max-w-2xl mx-auto text-gray-600 text-lg">
        Select the best pricing option for your needs.
      </p>

      <!-- Toggle -->
      <div class="inline-flex items-center mt-8 bg-gray-100 p-1 rounded-lg">
        <button class="px-4 py-2 rounded-md bg-white shadow text-gray-800 font-medium" id="promotersBtn">
          For Promoters
        </button>
        <button class="px-4 py-2 rounded-md text-gray-600 font-medium" id="brandsBtn">
          For Brands
        </button>
      </div>
    </div>

    <!-- Pricing Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="pricing-card bg-white border border-gray-200 rounded-2xl shadow-sm transition-all hover:scale-105 hover:-translate-y-1">
        <div class="p-6 md:p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-2">Starter</h3>
          <p class="text-gray-600 mb-6">Perfect for getting started</p>
          <div class="flex items-baseline mb-8">
            <span class="text-4xl md:text-5xl font-extrabold text-gray-900">KES 0</span>
            <span class="pricing-duration text-gray-500 font-medium ml-2">/ month</span>
          </div>
          <button class="w-full py-3 px-6 rounded-lg border border-primary-600 text-primary-600 font-medium hover:bg-primary-50 mb-6">
            Get Started
          </button>
          <ul class="space-y-4 text-gray-600">
            <li class="flex items-start"><svg class="w-5 h-5 mr-3 mt-0.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>1 Campaign Per Month</li>
          </ul>
        </div>
      </div>

      <div class="pricing-card pricing-popular bg-gradient-to-br from-primary-600 to-primary-700 text-white shadow-lg rounded-2xl transition-all hover:scale-105 hover:-translate-y-1" style="background:#db2777">
        <div class="p-6 md:p-8">
          <h3 class="text-xl font-bold mb-2">Growth</h3>
          <p class="text-primary-100 mb-6">For growing promoters</p>
          <div class="flex items-baseline mb-8">
            <span class="text-4xl md:text-5xl font-extrabold">KES 500</span>
            <span class="pricing-duration text-primary-100 font-medium ml-2">/ month</span>
          </div>
          <button class="w-full py-3 px-6 rounded-lg bg-white text-primary-700 font-medium hover:bg-primary-50 mb-6">
            Get Started
          </button>
          <ul class="space-y-4 text-white">
            <li class="flex items-start"><svg class="w-5 h-5 mr-3 mt-0.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Up to 4 Campaigns</li>
            <li class="flex items-start"><svg class="w-5 h-5 mr-3 mt-0.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>1 Per Week</li>
          </ul>
        </div>
      </div>

      <div class="pricing-card bg-white border border-gray-200 rounded-2xl shadow-sm transition-all hover:scale-105 hover:-translate-y-1">
        <div class="p-6 md:p-8">
          <h3 class="text-xl font-bold text-gray-900 mb-2">Unlimited</h3>
          <p class="text-gray-600 mb-6">Best for active promoters</p>
          <div class="flex items-baseline mb-8">
            <span class="text-4xl md:text-5xl font-extrabold text-gray-900">KES 1,000</span>
            <span class="pricing-duration text-gray-500 font-medium ml-2">/ month</span>
          </div>
          <button class="w-full py-3 px-6 rounded-lg border border-primary-600 text-primary-600 font-medium hover:bg-primary-50 mb-6">
            Get Started
          </button>
          <ul class="space-y-4 text-gray-600">
            <li class="flex items-start"><svg class="w-5 h-5 mr-3 mt-0.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Unlimited Campaigns</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script scoped>
document.addEventListener("DOMContentLoaded", () => {
  const promotersBtn = document.getElementById("promotersBtn");
  const brandsBtn = document.getElementById("brandsBtn");

  const prices = document.querySelectorAll(".text-4xl.md\\:text-5xl");
  const durations = document.querySelectorAll(".pricing-duration");
  const descriptions = document.querySelectorAll(".pricing-card p.text-gray-600, .pricing-card p.text-primary-100");
  const titles = document.querySelectorAll(".pricing-card h3");
  const features = document.querySelectorAll(".pricing-card ul");

  const promoterPrices = ["KES 0", "KES 500", "KES 1,000"];
  const brandPrices = ["KES 1,000", "KES 4,500", "KES 10,000"];
  const promoterDurations = ["/ month", "/ month", "/ month"];
  const brandDurations = ["", "", ""];
  const promoterDescriptions = ["Perfect for getting started", "For growing promoters", "Best for active promoters"];
  const brandDescriptions = ["Great for small campaigns", "Ideal for agencies", "Unlimited campaigns all year"];
  const promoterTitles = ["Starter", "Growth", "Unlimited"];
  const brandTitles = ["Solo", "Business", "Enterprise"];
  const promoterFeatures = [
    ["1 Campaign Per Month"],
    ["Up to 4 Campaigns", "1 Per Week"],
    ["Unlimited Campaigns"],
  ];
  const brandFeatures = [
    ["1 Campaign Slots"],
    ["5 Campaign Slots"],
    ["Unlimited Slots", "Valid for 1 Year"],
  ];

  function renderPlan(index, title, desc, price, duration, featuresList) {
    titles[index].textContent = title;
    descriptions[index].textContent = desc;
    prices[index].textContent = price;
    durations[index].textContent = duration;

    const ul = features[index];
    ul.innerHTML = "";
    featuresList.forEach((feature) => {
      ul.innerHTML += `
        <li class="flex items-start">
          <svg class="w-5 h-5 mr-3 mt-0.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
          <span>${feature}</span>
        </li>`;
    });
  }

  function setActive(btn1, btn2) {
    btn1.classList.add("bg-white", "shadow", "text-gray-800");
    btn1.classList.remove("text-gray-600");
    btn2.classList.remove("bg-white", "shadow", "text-gray-800");
    btn2.classList.add("text-gray-600");
  }

  promotersBtn.addEventListener("click", () => {
    setActive(promotersBtn, brandsBtn);
    for (let i = 0; i < 3; i++) {
      renderPlan(i, promoterTitles[i], promoterDescriptions[i], promoterPrices[i], promoterDurations[i], promoterFeatures[i]);
    }
  });

  brandsBtn.addEventListener("click", () => {
    setActive(brandsBtn, promotersBtn);
    for (let i = 0; i < 3; i++) {
      renderPlan(i, brandTitles[i], brandDescriptions[i], brandPrices[i], brandDurations[i], brandFeatures[i]);
    }
  });
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
