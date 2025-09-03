<template>
  <section class="bg-white w-full h-[80vh]">
    <div class="grid grid-cols-1 md:grid-cols-[3fr_2fr] h-full">
      <!-- Left Image -->
      <div class="hidden md:block">
        <img
          src="/img/hero.png"
          alt="CV writing"
          class="w-full h-[80vh] object-cover"
        />
      </div>

      <!-- Right Content -->
      <div
        class="bg-[#32383f] text-white px-8 py-16 flex flex-col justify-center h-full"
        style="
          background-color: #32383f;
          color: white;
          padding: 4rem 2rem;
          display: flex;
          flex-direction: column;
          justify-content: center;
          height: 100%;
        "
      >
        <div class="max-w-xl mx-auto">
          <h2
            class="text-4xl font-extrabold leading-tight tracking-tight"
            style="
              font-size: 2.25rem;
              font-weight: 700;
              line-height: 1.25;
              font-family: sans-serif;
            "
          >
            Land More Interviews, <br />Shine Brighter
          </h2>
          <p
            class="mt-4 text-lg text-gray-300"
            style="
              margin-top: 1rem;
              font-size: 1.125rem;
              color: #d1d5db;
              font-family: sans-serif;
            "
          >
            Get an ATS-optimized CV that stands out,<br />
            gets noticed, and moves you<br />
            closer to your dream job.
          </p>

          <!-- Buttons -->
          <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a
              href="mailto:?subject=Free%20CV%20Review"
              class="px-6 py-3 rounded-lg border border-orange-500 text-white hover:bg-orange-500 hover:text-white transition inline-block"
            >
              Get a Free CV Review
            </a>

            <router-link
              to="/order-cv"
              class="px-6 py-3 rounded-lg bg-[#ff9c30] text-white hover:bg-orange-600 transition inline-block"
            >
              Order Your Custom CV
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
