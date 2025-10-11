<template>
  <section
    class="
      relative
      bg-[#32383f]
      py-20
      text-white
      rounded-xl
      shadow-xl
      mx-4
    "
  >
    <!-- Subtle overlay texture -->
    <div
      class="
        absolute inset-0
        bg-[url('https://www.toptal.com/designers/subtlepatterns/patterns/dots.png')]
        opacity-10
        rounded-3xl
      "
    ></div>

    <div class="relative max-w-4xl mx-auto text-center px-6">
      <!-- HEADLINE -->
      <h2 class="text-3xl md:text-4xl font-extrabold drop-shadow-lg">
        Ready to Land More Interviews?
      </h2>
      <p class="mt-4 text-lg md:text-xl text-white-100 drop-shadow-lg max-w-2xl mx-auto">
        CareerShyne AI tools help you craft a resume that gets noticed. <br />
        Stop missing opportunities — let your resume shine today.
      </p>

      <!-- SOCIAL PROOF -->
      <div class="mt-6 flex flex-col items-center justify-center">
        <div class="flex text-yellow-400 mb-2">
          <svg
            v-for="i in 5"
            :key="i"
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 24 24"
            class="w-6 h-6"
          >
            <path
              fill-rule="evenodd"
              d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l1.36 3.27a1.25 1.25 0 00.95.765l3.517.52c1.164.172 1.63 1.6.787 2.417l-2.544 2.478a1.25 1.25 0 00-.359 1.107l.6 3.497c.2 1.16-1.02 2.046-2.06 1.497l-3.146-1.655a1.25 1.25 0 00-1.164 0l-3.146 1.655c-1.04.55-2.26-.337-2.06-1.497l.6-3.497a1.25 1.25 0 00-.359-1.107L4.17 10.18c-.843-.817-.377-2.245.787-2.417l3.517-.52a1.25 1.25 0 00.95-.765l1.364-3.27z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <p class="text-white-100 text-sm font-medium">
          ⭐ Trusted by <span class="font-semibold text-orange-400">500+ professionals</span> in the last 30 days
        </p>
      </div>

            <!-- CTA BUTTONS -->
      <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
        <a
          href="/login"
          class="px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg shadow-md hover:bg-white hover:text-orange-500 transition duration-300"
        >
          Unlock My Career
        </a>
      </div>
    </div>
  </section>
</template>
