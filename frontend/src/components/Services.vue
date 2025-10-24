<template>
  <section class="relative  t py-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <!-- HEADER -->
      <div class="text-center mb-16">
        <p
          class="text-sm font-semibold text-orange-600 uppercase tracking-wide mb-2"
        >
          Start in 4 Simple Steps
        </p>
        <h2
          class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight"
        >
          How CareerShyne Works
        </h2>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
          From profile setup to tracking your applications — CareerShyne
          automates your job search and keeps you in control every step of the
          way.
        </p>
      </div>

      <!-- STEPS GRID -->
      <div class="relative grid md:grid-cols-4 gap-10">
        <!-- CONNECTING LINE (desktop only) -->
        <div
          class="hidden md:block absolute top-1/2 left-0 right-0 h-0.5 bg-orange-100 transform -translate-y-1/2 z-0"
        ></div>

        <!-- STEP CARD -->
        <div
          v-for="(step, index) in steps"
          :key="index"
          class="relative bg-white rounded-2xl shadow-md p-8 text-center transition-all transform hover:-translate-y-2 hover:shadow-2xl z-10"
        >
          <div
            class="flex items-center justify-center mx-auto w-14 h-14 rounded-full bg-orange-500 text-white text-2xl font-bold mb-4 shadow-lg"
          >
            {{ index + 1 }}
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-3">
            {{ step.title }}
          </h3>
          <p class="text-gray-600">
            {{ step.description }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const steps = [
  {
    title: "Create Your Account",
    description:
      "Sign up and set up your profile. We’ll use it to tailor your applications to the right jobs.",
  },
  {
    title: "Choose the Jobs You Like",
    description:
      "Browse curated listings that match your skills and interests — no endless scrolling.",
  },
  {
    title: "We Apply for You",
    description:
      "CareerShyne customizes your CV, cover letter, and email — and sends a professional application on your behalf.",
  },
  {
    title: "Track Your Applications",
    description:
      "Stay updated in real-time. View where your applications were sent, track responses, and manage follow-ups — all from one dashboard.",
  },
];
</script>

<style scoped>
/* Add a gentle floating animation on hover */
div:hover .hover\:float {
  transform: translateY(-6px);
  transition: all 0.3s ease;
}
</style>
