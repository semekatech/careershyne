<template>
  <section class="bg-white w-full">
    <div class="grid grid-cols-1 md:grid-cols-[2fr_2fr] h-full">
      <!-- Left Image -->
      <div>
        <img
          src="/img/hero.jpg"
          alt="CV writing"
          class="w-full h-64 md:h-[80vh] object-cover object-center"
        />
      </div>

      <!-- Right Content -->
      <div
        class="bg-[#2c3137] text-white px-8 py-16 flex flex-col justify-center h-full"
      >
        <div class="max-w-md mx-auto">
          <!-- Heading -->
          <h2 class="text-3xl md:text-4xl font-extrabold leading-snug mb-4 ">
            Land More Interviews,<br />Shine Brighter
          </h2>

          <!-- Subtext -->
          <p class="text-base md:text-lg text-gray-300 mb-8">
            Get an ATS-optimized CV that stands out, gets noticed, and moves you
            closer to your dream job.
          </p>

          <!-- Buttons -->
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Outline Button -->
            <a
              href="mailto:?subject=Free%20CV%20Review"
              class="px-6 py-3 rounded-lg border border-orange-500 text-orange-500 bg-transparent hover:bg-orange-500 hover:text-white transition inline-block text-center font-medium"
            >
              Get a Free CV Review
            </a>

            <!-- Solid Button -->
            <router-link
              to="/order-cv"
              class="px-6 py-3 rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition inline-block text-center font-medium"
            >
              Order Your Custom CV
            </router-link>
          </div>

          <!-- Bottom Note -->
          <p class="mt-6 text-gray-300">
            Trusted by
            <span class="text-orange-500 font-semibold">500+</span>
            professionals across industries
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
