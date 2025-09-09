<template>
   <TheWelcome />
  <section class="bg-white dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <!-- Heading -->
      <div class="text-center mb-16">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
          Our Services
        </h2>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
          Tools that put your career in the spotlight
        </p>
      </div>

      <!-- CV Review & Optimization (featured block) -->
      <div class="bg-orange-50 dark:bg-gray-800 rounded-2xl shadow-lg p-10 mb-20">
        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
          🔍 CV Review & Optimization
        </h3>
        <p class="text-gray-700 dark:text-gray-300 mb-6">
          Don’t let your dream job slip away because of an unseen CV.
        </p>
        <div class="grid md:grid-cols-2 gap-8">
          <!-- Left: The Challenge -->
          <div>
            <h4 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">The Challenge</h4>
            <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
              <li>Endless applications but no interviews</li>
              <li>CVs blocked by ATS filters</li>
              <li>Skills reduced to generic job duties</li>
              <li>Missed chances due to poor formatting or errors</li>
            </ul>
          </div>
          <!-- Right: The Solution -->
          <div>
            <h4 class="font-semibold text-lg text-gray-900 dark:text-white mb-2">The Solution</h4>
            <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
              <li>Grab attention in <strong>6 seconds</strong></li>
              <li>Beat ATS with keyword optimization</li>
              <li>Highlight <strong>results, not just tasks</strong></li>
              <li>Professional, polished, error-free</li>
              <li>Tailored to your role & industry</li>
            </ul>
          </div>
        </div>
        <p class="mt-6 text-gray-800 dark:text-gray-200 font-medium">
          ✅ Result: From invisible to irresistible — more recruiter callbacks, more interviews, more opportunities.
        </p>
      </div>

      <!-- Other Services (3-card grid) -->
      <div class="grid md:grid-cols-3 gap-8">
        <!-- CV Revamp -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-8 hover:shadow-xl transition">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">📄 CV Revamps & Polishing</h3>
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            Your CV is your career passport. We transform it into a modern, achievement-driven profile that sells your value.
          </p>
          <p class="font-medium text-gray-800 dark:text-gray-200">Outcome: A professional CV that turns job applications into interview calls.</p>
        </div>

        <!-- Cover Letters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-8 hover:shadow-xl transition">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">📝 Cover Letters That Impress</h3>
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            We craft personalized cover letters that connect with recruiters, highlight your fit, and show genuine motivation.
          </p>
          <p class="font-medium text-gray-800 dark:text-gray-200">Outcome: A cover letter that feels authentic, builds connection, and wins attention.</p>
        </div>

        <!-- LinkedIn -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-8 hover:shadow-xl transition">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">🌐 LinkedIn Optimization</h3>
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            We optimize your LinkedIn profile with the right keywords, headline, and achievements so it attracts recruiters 24/7.
          </p>
          <p class="font-medium text-gray-800 dark:text-gray-200">Outcome: A LinkedIn profile that builds credibility and opens doors.</p>
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center mt-20">
        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">
          Ready to Elevate Your Career?
        </h3>
        <router-link
          to="/order-cv"
          class="px-8 py-4 rounded-2xl bg-orange-500 text-white hover:bg-gray-900 hover:text-orange-400 transition font-medium shadow-md"
        >
          Get Started Today
        </router-link>
      </div>
    </div>
  </section>
  <FooterSection />
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>