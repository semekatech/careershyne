<template>
  <TheWelcome />

  <!-- HERO SECTION -->
  <section class="relative bg-gray-900 text-white py-28 overflow-hidden">
    <img
      src="/img/about.jpg"
      alt="CareerShyne Team Working"
      class="absolute inset-0 w-full h-full object-cover opacity-50"
    />
    <div class="relative z-10 container mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">About CareerShyne</h1>
      <p class="text-lg md:text-xl text-orange-400 font-semibold">
        Empowering Job Seekers Through Smart Automation
      </p>
      <p class="mt-6 max-w-2xl mx-auto text-gray-200 leading-relaxed">
        We simplify the job hunt by automatically applying to positions that match your skills —
        so you can focus on preparing for interviews, not filling endless forms.
      </p>
    </div>
  </section>

  <!-- OUR STORY -->
  <section class="bg-gray-100 dark:bg-gray-900 py-20">
    <div class="container mx-auto px-6">
      <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div>
          <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
            Our Story
          </h2>
          <p class="text-lg text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
            At <span class="font-bold text-orange-500">CareerShyne</span>, we started with one question — 
            why do so many talented people struggle to land interviews despite having strong skills and experience?
          </p>
          <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
            We realized the process was broken. Candidates waste hours manually applying, 
            while opportunities slip away. So we built a system that applies <b>for you</b> — smartly, accurately, 
            and tailored to your strengths.
          </p>
        </div>

        <div class="relative">
          <img
            src="/img/why-choose-us.jpg"
            alt="Our Story"
            class="rounded-3xl shadow-lg object-cover w-full h-80"
          />
          <div class="absolute inset-0 rounded-3xl bg-orange-500/20 mix-blend-multiply"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHAT WE DO -->
  <section class="bg-white dark:bg-gray-800 py-20">
    <div class="container mx-auto px-6">
      <div class="max-w-5xl mx-auto text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          What We Do
        </h2>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
          We combine automation, personalization, and real human insight to help you land more interviews — faster.
        </p>
      </div>

      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-2xl shadow-md hover:shadow-xl transition-all">
          <h3 class="text-xl font-semibold text-orange-600 mb-3">Smart Job Applications</h3>
          <p class="text-gray-700 dark:text-gray-300">
            We automatically apply to multiple matching roles, customizing each submission with your CV and cover letter.
          </p>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-2xl shadow-md hover:shadow-xl transition-all">
          <h3 class="text-xl font-semibold text-orange-600 mb-3">Personalized Optimization</h3>
          <p class="text-gray-700 dark:text-gray-300">
            Our AI-enhanced system ensures your CV and messages stand out to recruiters and bypass automated filters.
          </p>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900 p-8 rounded-2xl shadow-md hover:shadow-xl transition-all">
          <h3 class="text-xl font-semibold text-orange-600 mb-3">Progress Tracking</h3>
          <p class="text-gray-700 dark:text-gray-300">
            Track every application in one dashboard. Know where you’ve applied, responses received, and upcoming interviews.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- MISSION & IMPACT -->
  <section class="bg-gray-900 text-white py-24">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-bold mb-6">Our Mission</h2>
      <p class="max-w-3xl mx-auto text-lg leading-relaxed text-gray-300">
        We’re on a mission to make job applications effortless and accessible for everyone.
        By merging automation with personalization, we help people tell their story the right way — 
        and connect them with opportunities that truly fit.
      </p>

      <div class="mt-10 grid md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-4xl font-bold text-orange-500 mb-2">10K+</h3>
          <p class="text-gray-300">Applications Sent</p>
        </div>
        <div>
          <h3 class="text-4xl font-bold text-orange-500 mb-2">6K+</h3>
          <p class="text-gray-300">Interviews Secured</p>
        </div>
        <div>
          <h3 class="text-4xl font-bold text-orange-500 mb-2">1000+</h3>
          <p class="text-gray-300">Satisfied Job Seekers</p>
        </div>
      </div>
    </div>
  </section>


  <FooterSection />
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
import { useVisitorLogger } from "@/composables/useVisitorLogger";
useVisitorLogger("AboutUs");
</script>
