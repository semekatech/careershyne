<template>
  <div class="bg-white dark:bg-gray-900">
    <div class="mx-auto py-16 sm:px-6 lg:px-8">
      <div class="relative isolate overflow-hidden px-6 py-24 text-center sm:rounded-3xl sm:px-16">
        <!-- Heading -->
        <h2 class="mx-auto max-w-2xl text-3xl font-bold uppercase tracking-wide text-gray-900 dark:text-gray-100 sm:text-4xl">
          Join Our WhatsApp Career Channel
        </h2>
        <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-600 dark:text-gray-300">
          Get daily tips on CV writing, LinkedIn optimization, interview preparation, and job opportunities — 
          all directly on WhatsApp.
        </p>

        <!-- Faces (social proof) -->
        <div class="isolate mt-8 flex items-center justify-center -space-x-2 overflow-hidden">
          <img class="relative z-30 inline-block h-10 w-10 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/men/34.jpg" alt="Member">
          <img class="relative z-20 inline-block h-10 w-10 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/women/2.jpg" alt="Member">
          <img class="relative z-10 inline-block h-10 w-10 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/women/3.jpg" alt="Member">
          <img class="relative z-0 inline-block h-10 w-10 rounded-full ring-2 ring-white" src="https://randomuser.me/api/portraits/men/4.jpg" alt="Member">
          <span class="!ml-2 font-bold italic text-orange-500">500+ professionals already learning and growing with us</span>
        </div>

        <!-- CTA -->
        <div class="mt-12 flex items-center justify-center gap-x-6">
          <a
            href="https://whatsapp.com/channel/0029VbBbuYSDZ4LYzXurHm21" 
            target="_blank"
            class="text-md relative inline-flex items-center gap-x-2 rounded-lg bg-orange-600 px-6 py-4 font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
          >
            <span class="absolute -top-5 left-0 w-full text-left text-xs italic text-orange-500">Free & No Spam</span>
            Join WhatsApp Channel
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="-mr-0.5 h-5 w-5">
              <path fill-rule="evenodd" d="M16.72 7.72a.75.75 0 011.06 0l3.75 3.75a.75.75 0 010 1.06l-3.75 3.75a.75.75 0 11-1.06-1.06l2.47-2.47H3a.75.75 0 010-1.5h16.19l-2.47-2.47a.75.75 0 010-1.06z" clip-rule="evenodd"/>
            </svg>
          </a>
        </div>

        <!-- Background circle -->
        <svg viewBox="0 0 1024 1024"
          class="absolute left-1/2 top-1/2 -z-10 h-[72rem] w-[72rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]"
          aria-hidden="true">
          <circle cx="512" cy="512" r="512" fill="url(#career-channel-bg)" fill-opacity="0.7"></circle>
          <defs>
            <radialGradient id="career-channel-bg">
              <stop stop-color="#15b8a6"></stop>
              <stop offset="1" stop-color="#15b8a6"></stop>
            </radialGradient>
          </defs>
        </svg>
      </div>
    </div>
  </div>
</template>
