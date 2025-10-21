<template>
  <section class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-950 py-20">
    <div class="text-center mb-16">
      <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white">
        Find the Perfect Plan
      </h1>
      <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
        Let CareerShyne take the stress out of job hunting — choose a plan that works as hard as you do.
      </p>
    </div>

    <div
      class="w-full max-w-6xl mx-auto flex flex-col lg:flex-row lg:space-x-8 space-y-8 lg:space-y-0 px-6"
    >
      <!-- STARTER PLAN -->
      <div
        class="flex flex-col justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300"
      >
        <div>
          <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Starter</h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            A great place to start if you’re beginning your job search journey. Apply smartly and confidently.
          </p>

          <div class="mt-8">
            <span class="text-5xl font-extrabold text-gray-900 dark:text-gray-50">KES 1,000</span>
            <span class="text-gray-600 dark:text-gray-400">/ month</span>
          </div>

          <ul class="mt-8 space-y-4 text-gray-700 dark:text-gray-300">
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>Access to verified and active job listings</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>We apply to <strong>20 jobs per month</strong> on your behalf</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>Professionally written CV & cover letter templates</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>Friendly 24/7 email support whenever you need help</span>
            </li>
          </ul>
        </div>

        <button
          @click="selectPlan()"
          class="mt-10 w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors"
        >
          Choose Starter
        </button>
      </div>

      <!-- PROFESSIONAL PLAN -->
      <div
        class="flex flex-col justify-between bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-2xl shadow-2xl p-8 border-4 border-orange-400 relative transform hover:scale-105 transition-all duration-300"
      >
        <span
          class="absolute -top-4 right-6 bg-white text-orange-600 px-4 py-1 rounded-full text-sm font-bold shadow-md"
        >
          Most Popular
        </span>

        <div>
          <h3 class="text-2xl font-bold">Professional</h3>
          <p class="mt-2 text-orange-100">
            For serious job seekers who want consistent results — every application is personalized to help you stand out.
          </p>

          <div class="mt-8">
            <span class="text-5xl font-extrabold">KES 1,500</span>
            <span class="text-orange-200">/ month</span>
          </div>

          <ul class="mt-8 space-y-4 text-orange-50">
            <li class="flex items-start">
              <span class="material-icons text-white mr-3">check_circle</span>
              <span>Unlimited access to high-quality job openings</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-white mr-3">check_circle</span>
              <span>We apply to <strong>35 jobs per month</strong> for you</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-white mr-3">check_circle</span>
              <span>Customized CV & cover letter for each job role</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-white mr-3">check_circle</span>
              <span>Real-time dashboard to track your applications</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-white mr-3">check_circle</span>
              <span>Priority 24/7 chat support — always there for you</span>
            </li>
          </ul>
        </div>

        <button
          @click="selectPlan()"
          class="mt-10 w-full bg-white text-orange-600 py-3 rounded-lg font-semibold hover:bg-orange-50 transition-colors"
        >
          Choose Professional
        </button>
      </div>

      <!-- PREMIUM PLAN -->
      <div
        class="flex flex-col justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300"
      >
        <div>
          <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Premium</h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Designed for ambitious professionals — we take over the heavy lifting and maximize every opportunity for you.
          </p>

          <div class="mt-8">
            <span class="text-5xl font-extrabold text-gray-900 dark:text-gray-50">KES 3,000</span>
            <span class="text-gray-600 dark:text-gray-400">/ month</span>
          </div>

          <ul class="mt-8 space-y-4 text-gray-700 dark:text-gray-300">
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>Access to exclusive premium & remote jobs</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>We apply to <strong>80 jobs per month</strong> that match your goals</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>Every application reviewed by a human expert</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>Career insights, interview prep & coaching</span>
            </li>
            <li class="flex items-start">
              <span class="material-icons text-green-500 mr-3">check_circle</span>
              <span>VIP priority support — instant help when you need it</span>
            </li>
          </ul>
        </div>

        <button
          @click="selectPlan()"
          class="mt-10 w-full bg-orange-500 text-white py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors"
        >
          Choose Premium
        </button>
      </div>
    </div>
  </section>
</template>
<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
const selectPlan = (plan, price) => {
  localStorage.setItem('selectedPlan', JSON.stringify({ plan, price }))

  router.push({ path: '/login' })
}
</script>
