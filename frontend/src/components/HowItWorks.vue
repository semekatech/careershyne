<template>
  <div
    id="services" class="px-4 py-16 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl md:px-24 lg:px-8 lg:py-20"
  >
    <div class="max-w-xl mb-10 md:mx-auto sm:text-center lg:max-w-2xl md:mb-12">
      <h2
        class="max-w-lg mb-6 font-sans text-3xl font-bold leading-none tracking-tight text-gray-900 sm:text-4xl md:mx-auto"
      >
        <span class="relative inline-block">
          <svg
            viewBox="0 0 52 24"
            fill="currentColor"
            class="absolute top-0 left-0 z-0 hidden w-32 -mt-8 -ml-20 text-slate-100 lg:w-32 lg:-ml-28 lg:-mt-10 sm:block"
          >
            <defs>
              <pattern
                id="fdca20a0-aeb4-4caf-ba1b-4351eee42363"
                x="0"
                y="0"
                width=".135"
                height=".30"
              >
                <circle cx="1" cy="1" r=".7"></circle>
              </pattern>
            </defs>
            <rect
              fill="url(#fdca20a0-aeb4-4caf-ba1b-4351eee42363)"
              width="52"
              height="24"
            ></rect>
          </svg>
          <span class="relative">Our</span>
        </span>
        Professional CV & Career Services
      </h2>
      <p class="text-base text-gray-700 md:text-lg">
        We go beyond templates. Our services are tailored to help you stand out
        in today’s competitive job market with a CV that works as hard as you
        do.
      </p>
    </div>

    <div
      class="grid max-w-md gap-8 row-gap-10 sm:mx-auto lg:max-w-full lg:grid-cols-3"
    >
      <!-- CV Writing -->
      <div class="flex flex-col sm:flex-row">
        <div class="sm:mr-4">
          <div
            class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-green-50"
          >
            <svg
              class="w-12 h-12 text-blue-400"
              stroke="currentColor"
              viewBox="0 0 52 52"
            >
              <polygon
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
                points="29 13 14 29 25 29 23 39 38 23 27 23"
              ></polygon>
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-2 font-semibold leading-5">Custom CV Writing</h6>
          <p class="mb-3 text-sm text-gray-900">
            Professionally crafted CVs designed to showcase your strengths,
            highlight your achievements, and make recruiters take notice.
          </p>
          <ul class="mb-4 -ml-1 space-y-2">
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Tailored to your career goals
            </li>
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              ATS-friendly formatting
            </li>
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Industry-specific keywords
            </li>
          </ul>
          <router-link
            to="/order-cv"
            class="inline-flex items-center font-semibold transition-colors duration-200 text-blue-400 hover:text-blue-800"
          >
            Place Order
          </router-link>
        </div>
      </div>

      <!-- CV Review -->
      <div class="flex flex-col sm:flex-row">
        <div class="sm:mr-4">
          <div
            class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-green-50"
          >
            <svg
              class="w-12 h-12 text-blue-400"
              stroke="currentColor"
              viewBox="0 0 52 52"
            >
              <polygon
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
                points="29 13 14 29 25 29 23 39 38 23 27 23"
              ></polygon>
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-2 font-semibold leading-5">Free CV Review</h6>
          <p class="mb-3 text-sm text-gray-900">
            Already have a CV? Our experts will review it and provide
            personalized feedback to improve structure, clarity, and impact.
          </p>
          <ul class="mb-4 -ml-1 space-y-2">
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Professional feedback
            </li>
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Recruiter’s perspective analysis
            </li>
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Actionable improvement tips
            </li>
          </ul>
          <a
            href="mailto:?subject=Free%20CV%20Review"
            class="inline-flex items-center font-semibold transition-colors duration-200 text-blue-400 hover:text-green-800"
          >
            Get Your Free Review
          </a>
        </div>
      </div>

      <!-- LinkedIn Optimization -->
      <div class="flex flex-col sm:flex-row">
        <div class="sm:mr-4">
          <div
            class="flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-green-50"
          >
            <svg
              class="w-12 h-12 text-blue-400"
              stroke="currentColor"
              viewBox="0 0 52 52"
            >
              <polygon
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
                points="29 13 14 29 25 29 23 39 38 23 27 23"
              ></polygon>
            </svg>
          </div>
        </div>
        <div>
          <h6 class="mb-2 font-semibold leading-5">
            LinkedIn Profile Optimization
          </h6>
          <p class="mb-3 text-sm text-gray-900">
            Build a professional online presence with a polished LinkedIn
            profile that boosts recruiter visibility and complements your CV.
          </p>
          <ul class="mb-4 -ml-1 space-y-2">
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Keyword optimization
            </li>
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Recruiter visibility boost
            </li>
            <li class="flex items-start">
              <span class="mr-1">
                <svg
                  class="w-5 h-5 mt-px text-blue-400"
                  stroke="currentColor"
                  viewBox="0 0 52 52"
                >
                  <polygon
                    stroke-width="4"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    fill="none"
                    points="29 13 14 29 25 29 23 39 38 23 27 23"
                  ></polygon>
                </svg>
              </span>
              Branding for your career
            </li>
          </ul>
          <a
            href="#"
            class="inline-flex items-center font-semibold transition-colors duration-200 text-blue-400 hover:text-green-800"
          >
            Optimize My LinkedIn
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
