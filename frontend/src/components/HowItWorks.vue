<template>
  <div id="services" class="bg-white dark:bg-gray-800 text-black px-6 py-20">
    <!-- Section Header -->
    <div class="max-w-3xl mx-auto text-center mb-16">
      <h2 class="text-4xl font-bold mb-4">How We Help You Shine</h2>
      <p class="text-lg text-gray-800 dark:text-gray-800">
        Practical, affordable, and professional career services designed to increase your
        visibility, get you noticed, and help you land more interviews.
      </p>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 max-w-6xl mx-auto">
      
      <!-- CV Review & Optimization -->
      <div class="flex flex-col justify-between">
        <div>
          <h3 class="text-orange-400 font-semibold text-lg mb-2">CV Review & Optimization</h3>
          <p class="text-gray-800 dark:text-gray-800 text-sm mb-4">
            Identify weaknesses and get actionable insights to make your CV recruiter-ready.
          </p>
          <ul class="space-y-2 text-gray-800 dark:text-gray-800 text-sm">
            <li>⭐ Pinpoint areas holding you back</li>
            <li>⭐ ATS compliance check</li>
            <li>⭐ Clear, practical recommendations</li>
          </ul>
        </div>
        <a href="mailto:?subject=Free%20CV%20Review" class="mt-6 text-orange-400 font-semibold hover:underline">
          Start with a Review
        </a>
      </div>

      
      <!-- CV Revamps -->
      <div class="flex flex-col justify-between">
        <div>
          <h3 class="text-orange-400 font-semibold text-lg mb-2">CV Revamps</h3>
          <p class="text-gray-800 dark:text-gray-800 text-sm mb-4">
            Transform your existing CV into a polished, impactful document that highlights your strengths.
          </p>
          <ul class="space-y-2 text-gray-800 dark:text-gray-800 text-sm">
            <li>⭐ Stronger, sharper presentation</li>
            <li>⭐ Industry-specific keyword optimization</li>
            <li>⭐ Clearer alignment with your career goals</li>
          </ul>
        </div>
        <router-link to="/order-cv" class="mt-6 text-orange-400 font-semibold hover:underline">
          Upgrade Your CV
        </router-link>
      </div>

      <!-- Cover Letters -->
      <div class="flex flex-col justify-between">
        <div>
          <h3 class="text-orange-400 font-semibold text-lg mb-2">Cover Letters that Impress</h3>
          <p class="text-gray-800 dark:text-gray-800 text-sm mb-4">
            Make a strong first impression with tailored cover letters that complement your CV.
          </p>
          <ul class="space-y-2 text-gray-800 dark:text-gray-800 text-sm">
            <li>⭐ Personalized to each role</li>
            <li>⭐ Highlights your value proposition</li>
            <li>⭐ Persuasive yet professional tone</li>
          </ul>
        </div>
        <router-link to="/order-cv" class="mt-6 text-orange-400 font-semibold hover:underline">
          Get Your Cover Letter
        </router-link>
      </div>

      <!-- LinkedIn Optimization -->
      <div class="flex flex-col justify-between">
        <div>
          <h3 class="text-orange-400 font-semibold text-lg mb-2">LinkedIn Optimization</h3>
          <p class="text-gray-800 dark:text-gray-800 text-sm mb-4">
            Boost your visibility and professional brand on the world’s biggest hiring platform.
          </p>
          <ul class="space-y-2 text-gray-800 dark:text-gray-800 text-sm">
            <li>⭐ Recruiter-focused keywords</li>
            <li>⭐ Enhanced credibility & reach</li>
            <li>⭐ Branding aligned with your CV</li>
          </ul>
        </div>
        <a href="#" class="mt-6 text-orange-400 font-semibold hover:underline">
          Shine on LinkedIn
        </a>
      </div>

    </div>
  </div>
</template>
