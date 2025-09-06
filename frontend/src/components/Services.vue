<template>
  <div id="services" class="bg-white dark:bg-gray-800 text-black px-6 py-20">
    <!-- Section Header -->
    <div class="max-w-3xl mx-auto text-center mb-16">
      <h2 class="text-4xl font-bold text-black dark:text-orange-400 mb-4">How We Help You Shine</h2>
      <p class="text-lg text-gray-800 dark:text-gray-300">
        Practical, affordable, and professional career services designed to increase your
        visibility, get you noticed, and help you land more interviews.
      </p>
    </div>

    <!-- Services Grid -->
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
      
  <!-- CV Review & Optimization -->
  <div class="flex flex-col justify-between border rounded-xl shadow-sm p-6 bg-white dark:bg-gray-900">
    <div>
      <h3 class="text-orange-500 font-semibold text-lg mb-2">CV Review & Optimization</h3>
      <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
        Spot gaps and get detailed, actionable feedback to make your CV recruiter-ready.
      </p>
      <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
        <li>⭐ Identify problem areas</li>
        <li>⭐ ATS compliance check</li>
        <li>⭐ Practical recommendations</li>
      </ul>
    </div>
    <a href="mailto:?subject=Free%20CV%20Review" class="mt-6 text-orange-500 font-semibold hover:underline">
      🔹 Start with a Review
    </a>
  </div>

  <!-- CV Revamps -->
  <div class="flex flex-col justify-between border rounded-xl shadow-sm p-6 bg-white dark:bg-gray-900">
    <div>
      <h3 class="text-orange-500 font-semibold text-lg mb-2">CV Revamps & Polishing</h3>
      <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
        Transform your CV into a polished document highlighting all your strongest skills.
      </p>
      <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
        <li>⭐ Sharper presentation</li>
        <li>⭐ Keyword optimization</li>
        <li>⭐ Career goal alignment</li>
      </ul>
    </div>
    <router-link to="/order-cv" class="mt-6 text-orange-500 font-semibold hover:underline">
      🔹 Upgrade Your CV
    </router-link>
  </div>

  <!-- Cover Letters -->
  <div class="flex flex-col justify-between border rounded-xl shadow-sm p-6 bg-white dark:bg-gray-900">
    <div>
      <h3 class="text-orange-500 font-semibold text-lg mb-2">Cover Letters that Impress</h3>
      <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
        Stand out with highly customized cover letters that perfectly complement your CV.
      </p>
      <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
        <li>⭐ Role-specific writing</li>
        <li>⭐ Showcases your value</li>
        <li>⭐ Professional tone</li>
      </ul>
    </div>
    <router-link to="/order-cv" class="mt-6 text-orange-500 font-semibold hover:underline">
      🔹 Get Your Cover Letter
    </router-link>
  </div>

  <!-- LinkedIn Optimization -->
  <div class="flex flex-col justify-between border rounded-xl shadow-sm p-6 bg-white dark:bg-gray-900">
    <div>
      <h3 class="text-orange-500 font-semibold text-lg mb-2">LinkedIn Optimization</h3>
      <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">
        Boost your professional visibility and credibility on the world’s largest hiring platform.
      </p>
      <ul class="space-y-2 text-gray-700 dark:text-gray-300 text-sm">
        <li>⭐ Recruiter-focused keywords</li>
        <li>⭐ Wider reach & impact</li>
        <li>⭐ Branding with your CV</li>
      </ul>
    </div>
    <a href="#" class="mt-6 text-orange-500 font-semibold hover:underline">
      🔹 Shine on LinkedIn
    </a>
  </div>

</div>

  </div>
</template>
