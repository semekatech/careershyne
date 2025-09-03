<template>
  <section
    class="bg-[#0c1c2c] dark:bg-slate-900 pt-10 pb-16 sm:py-12 relative overflow-hidden"
  >
    <div
      class="absolute inset-0 z-0 bg-[url('/img/bg-texture.png')] bg-repeat opacity-20"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
        <div class="relative hidden md:block">
          <img
            src="/img/hero-image.png"
            alt="MyPerfectResume AI Resume Builder"
            class="w-full max-w-2xl mx-auto drop-shadow-xl"
          />
        </div>

        <div class="text-center md:text-left text-white">
          <h1
            class="text-4xl sm:text-5xl md:text-5xl font-extrabold leading-tight"
          >
            Stop Guessing, <br />
            <span class="text-[#fd624e]">Start Getting Hired.</span> Get a CV
            Designed to Impress
          </h1>

          <p class="mt-6 text-lg text-gray-300 max-w-xl mx-auto md:mx-0">
            In today's competitive market, a generic CV just won't cut it. At
            Careershyne, we don't use AI—we use expertise. Our team of
            seasoned professionals works with you to create, review, and
            customize a CV that genuinely reflects your skills and stands out to
            recruiters
          </p>

          <div
            class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start"
          >
            <a
              href="#"
              class="flex items-center justify-center gap-2 bg-transparent border-2 border-white hover:bg-white hover:text-[#1b2554] text-white font-semibold px-6 py-3 rounded-lg shadow-md text-lg transition duration-300"
            >
              Get a Free CV Review
            </a>
            <a
              href="#"
              class="flex items-center justify-center gap-2 bg-[#4c85c9] hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md text-lg transition duration-300"
            >
              Order Your Custom CV
            </a>
          </div>

          <div
            class="mt-8 flex flex-col sm:flex-row justify-center md:justify-start items-center gap-6"
          >
            <div class="flex flex-col items-center sm:items-start text-white">
              <div class="flex items-center gap-2">
                <span class="text-2xl font-bold text-[#62d294]">↑ 70%</span>
                <p>Increase in job interview invites</p>
              </div>
              <div class="flex items-center gap-2 mt-2">
                <span class="text-2xl font-bold text-[#62d294]">↑ 60%</span>
                <p>Higher recruiter engagement</p>
              </div>
            </div>
          </div>
        </div>

        <div class="relative hidden mt-8">
          <img
            src="/img/hero-image.png"
            alt="MyPerfectResume AI Resume Builder"
            class="w-full max-w-sm mx-auto drop-shadow-xl"
          />
        </div>
      </div>

   

      <div class="fixed bottom-6 right-6 z-50">
        <button
          class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-5 rounded-full shadow-lg transition duration-300"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M8 10h.01M12 10h.01M16 10h.01M9 16l3-3m0 0l3 3m-3-3v5"
            />
          </svg>
          Chat with us
        </button>
      </div>
    </div>
  </section>
</template>