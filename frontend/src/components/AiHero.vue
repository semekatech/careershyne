<template>
  <section class="relative bg-gradient-to-br from-green-50 via-white to-purple-100 py-32">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20 flex justify-center items-starts text-center">
      <!-- LEFT CONTENT -->
      <div>
        <p class="mt-[-50px] text-sm font-semibold text-orange-600 uppercase tracking-wide">
          AI THAT GETS YOU HIRED
        </p>

        <h1 class="mt-4 text-4xl font-extrabold text-gray-900 sm:text-5xl text-center">
          Your All-in-One AI Career <br /> Wingman
        </h1>

        <p class="mt-6 text-lg text-gray-600 text-center">
          Why juggle multiple tools? Our AI handles it all — from resumes and cover letters <br />
          to job eligibility checks and polished emails — so you can focus on landing the job.
        </p>

        <!-- SOCIAL PROOF -->
        <div class="mt-8 flex items-center justify-center gap-4">
          <!-- AVATAR STACK -->
          <div class="flex items-center space-x-[-10px]">
            <div
              class="w-12 h-12 rounded-full bg-purple-500 flex items-center justify-center text-white font-bold
                     transform transition duration-300 hover:scale-110 hover:rotate-6 hover:z-10 cursor-pointer"
            >
              N
            </div>

            <div
              class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white font-bold
                     transform transition duration-300 hover:scale-110 hover:-rotate-6 hover:z-10 cursor-pointer"
            >
              C
            </div>

            <div
              class="w-12 h-12 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold
                     transform transition duration-300 hover:scale-110 hover:rotate-3 hover:z-10 cursor-pointer"
            >
              J
            </div>

            <div
              class="w-12 h-12 rounded-full bg-violet-600 flex items-center justify-center text-white font-bold
                     transform transition duration-300 hover:scale-110 hover:-rotate-3 hover:z-10 cursor-pointer"
            >
              K
            </div>

            <div
              class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold
                     transform transition duration-300 hover:scale-110 hover:rotate-6 hover:z-10 cursor-pointer"
            >
              99+
            </div>
          </div>

          <!-- STARS + TEXT -->
          <div class="flex items-center gap-2">
            <!-- Stars -->
            <div class="flex text-yellow-400">
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

            <p class="text-gray-700 text-sm font-medium">
              Trusted by 500+ paid users (30d)
            </p>
          </div>
        </div>

        <!-- Primary CTA -->
        <div class="mt-6 flex flex-col sm:flex-row justify-center gap-6">
          <router-link
            to="/free-review"
            class="px-6 py-3 bg-orange-600 text-white font-semibold rounded-lg shadow-lg hover:bg-orange-700 transition text-center rounded-lg"
          >
            Unlock My Career
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>
