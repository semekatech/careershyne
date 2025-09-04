<template>
  <div class="bg-white dark:bg-gray-800 pt-20" id="pricing">
    <!-- Section header -->
    <div class="relative mx-auto max-w-7xl px-6 text-center lg:px-8 mb-10">
      <div class="mx-auto max-w-2xl lg:max-w-4xl">
        <h2 class="text-4xl font-bold leading-8 text-orange-500 dark:text-orange-400">
          Pricing
        </h2>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-200">
          Invest in your career with the right plan — affordable, transparent, and designed to help you land interviews faster.
        </p>
      </div>
    </div>

    <!-- Pricing grid -->
    <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">

        <!-- Free CV Review -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">CV Review (Starter – Free)</h3>
            <div class="mt-4 text-3xl font-bold">KES 0</div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              Kickstart your job search with a free, detailed review of your CV and personalized tips.
            </p>
            <ul class="mt-4 space-y-2 text-gray-600 dark:text-gray-300 text-sm">
              <li>✔ Professional CV assessment</li>
              <li>✔ Personalized improvement tips</li>
              <li>✔ ATS optimization suggestions</li>
            </ul>
          </div>
          <a href="mailto:?subject=Free%20CV%20Review"
             class="mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-purple-800 transition text-center">
            Get Your Free Review
          </a>
        </div>

        <!-- CV Revamp + Cover Letter -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">CV Revamp + Cover Letter</h3>
            <div class="mt-4 text-3xl font-bold">KES 200</div>
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
                       class="text-center mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-purple-800 transition">
            Order Now
          </router-link>
        </div>

        <!-- CV from Scratch + Cover Letter -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">CV from Scratch + Cover Letter</h3>
            <div class="mt-4 text-3xl font-bold">KES 300</div>
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
          <router-link to="/order-cv"
                       class="text-center mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-purple-800 transition">
            Get Started
          </router-link>
        </div>

        <!-- Career Success Package -->
        <div class="flex flex-col justify-between rounded-3xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm hover:shadow-md transition p-6">
          <div>
            <h3 class="text-sm text-orange-500 font-semibold">Career Success Package</h3>
            <div class="mt-4 text-3xl font-bold">KES 500</div>
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
          <router-link to="/order-cv"
                       class="text-center mt-6 w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-purple-800 transition">
            Unlock Your Package
          </router-link>
        </div>

      </div>
    </div>
  </div>
</template>
