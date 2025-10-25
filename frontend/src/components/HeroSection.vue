<template>
  <section
    class="relative bg-gradient-to-br from-green-50 via-white to-purple-100 py-24 sm:py-32"
    id="ai-hero"
  >
    <div
      class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col justify-center items-center text-center"
    >
      <div class="max-w-3xl">
        <p
          class="text-base font-semibold text-orange-600 uppercase tracking-wide mb-2"
        >
          THE FUTURE OF JOB HUNT IS HERE
        </p>

        <h1
          class="mt-4 text-4xl font-extrabold text-gray-900 sm:text-6xl leading-tight"
        >
          Automate Your Job Hunt. <br class="hidden sm:inline" />Get Interviews Faster.
        </h1>

        <p class="mt-6 text-xl text-gray-700 leading-relaxed">
          CareerShyne is your personal job assistant — we help you find the right roles,
          craft standout applications, and apply for jobs faster. <b>Skip the stress</b>
          and let us get you closer to your next interview opportunity.
        </p>

        <div class="mt-8 flex items-center justify-center gap-4">
          <div class="flex items-center gap-2">
            <div class="flex text-yellow-500">
              <svg
                v-for="i in 5"
                :key="i"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 24 24"
                class="w-5 h-5"
              >
                <path
                  fill-rule="evenodd"
                  d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l1.36 3.27a1.25 1.25 0 00.95.765l3.517.52c1.164.172 1.63 1.6.787 2.417l-2.544 2.478a1.25 1.25 0 00-.359 1.107l.6 3.497c.2 1.16-1.02 2.046-2.06 1.497l-3.146-1.655a1.25 1.25 0 00-1.164 0l-3.146 1.655c-1.04.55-2.26-.337-2.06-1.497l.6-3.497a1.25 1.25 0 00-.359-1.107L4.17 10.18c-.843-.817-.377-2.245.787-2.417l3.517-.52a1.25 1.25 0 00.95-.765l1.364-3.27z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>

            <p class="text-gray-800 text-base font-medium">
              Trusted by <b>500+</b>job seekers
            </p>
          </div>
        </div>

        <div class="mt-10 flex justify-center">
          <router-link
            to="/login"
            class="inline-flex items-center justify-center px-8 py-4 bg-orange-600 text-white text-lg font-bold rounded-xl shadow-xl shadow-orange-500/50 hover:bg-orange-700 transition duration-300 transform hover:scale-[1.02] active:scale-100"
          >
            Start Applying Smarter
            <svg
              class="w-5 h-5 ml-2"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M14 5l7 7m0 0l-7 7m7-7H3"
              />
            </svg>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>