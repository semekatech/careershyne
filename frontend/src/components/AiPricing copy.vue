<template>
  <div class="bg-white dark:bg-gray-800 pt-20" id="ai-pricing">
    <!-- Section header -->
    <div class="relative mx-auto max-w-7xl px-6 text-center lg:px-8 mb-10">
      <div class="mx-auto max-w-2xl lg:max-w-4xl">
        <div class="text-center mb-12">
          <p class="text-sm font-semibold text-orange-500 uppercase tracking-wide">
            Clear. Simple. Affordable.
          </p>
          <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
         Fair Price. Real Value.
          </h2>
        </div>
      </div>
    </div>
    <!-- Pricing grid -->
    <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">

        <!-- Free CV Review -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">CV Review (Starter – Free)</h3>
            <div class="mt-4 text-3xl font-bold text-black dark:text-orange-400">KES 0</div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Kickstart your job search with a free, detailed review of your CV and personalized tips.
            </p>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-300 text-sm">
              <li>✔ Professional CV assessment</li>
              <li>✔ Personalized improvement tips</li>
              <li>✔ ATS optimization suggestions</li>
            </ul>
          </div>
          <a href="mailto:info@careershyne.com?subject=Free%20CV%20Review"
             class="mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-[#32383f] transition text-center">
            Start with a Free Review
          </a>
        </div>

        <!-- CV Revamp + Cover Letter -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">CV Revamp + Cover Letter</h3>
            <div class="mt-4 text-3xl font-bold text-black dark:text-orange-400">KES 200</div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Upgrade your CV and cover letter with a tailored solution for a strong first impression.
            </p>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-300 text-sm">
              <li>✔ 1 CV revamp (ATS-friendly, keyword optimized)</li>
              <li>✔ 1 tailored cover letter</li>
              <li>✔ Industry-specific adjustments</li>
            </ul>
          </div>
          <router-link to="/order-cv"
                       class="text-center mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-[#32383f] transition">
            Upgrade My CV
          </router-link>
        </div>

        <!-- CV from Scratch + Cover Letter -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">CV from Scratch + Cover Letter</h3>
            <div class="mt-4 text-3xl font-bold text-black dark:text-orange-400">KES 300</div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Get a brand-new CV and cover letter built from scratch to position you for success.
            </p>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-300 text-sm">
              <li>✔ CV crafted from scratch</li>
              <li>✔ 1 personalized cover letter</li>
              <li>✔ ATS-optimized formatting</li>
              <li>✔ Tailored to your career goals</li>
            </ul>
          </div>
          <router-link to="/custom-cv-order"
                       class="text-center mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-[#32383f] transition">
            Build My CV from Scratch
          </router-link>
        </div>

        <!-- Career Success Package -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">Career Success Package</h3>
            <div class="mt-4 text-3xl font-bold text-black dark:text-orange-400">KES 500</div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Go all-in with a complete professional upgrade to maximize visibility and opportunities.
            </p>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-300 text-sm">
              <li>✔ 2 CV revamps (different roles/industries)</li>
              <li>✔ 2 customized cover letters</li>
              <li>✔ LinkedIn profile optimization</li>
              <li>✔ Recruiter visibility boost</li>
            </ul>
          </div>
           <a href="https://wa.me/254758428993?text=I%20want%20to%20unlock%20my%20career%20package%20with%20CareersHyne."
             class="mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-[#32383f] transition text-center">
           Unlock My Career Package
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
