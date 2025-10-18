<template>
  <AiTheWelcome />

  <!-- Section header -->

  <div
    class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden mt-5"
  >
    <div class="layout-container flex h-full grow flex-col">
      <main class="flex-1 px-4 sm:px-10 lg:px-20 py-12">
        <div class="w-full flex flex-col gap-10">
          <div
            class="relative -mt-20 pt-20 pb-10 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-900 dark:to-slate-800 overflow-hidden"
          >
            <div
              class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 dark:opacity-[0.02]"
            ></div>
            <div
              class="relative px-6 sm:px-12 flex flex-col gap-6 items-center text-center"
            >
              <h1
                class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight"
              >
                Find Your <span class="text-primary">Dream Job</span> Today
              </h1>
              <p class="max-w-2xl text-slate-600 dark:text-slate-400">
                Search through thousands of job listings from top companies and
                find the perfect fit for your career.
              </p>
              <div class="relative w-full max-w-2xl mt-4">
                <label class="flex flex-col w-full">
                  <div class="relative flex w-full items-center">
                    <div
                      class="absolute left-0 top-0 bottom-0 flex items-center pl-5 text-slate-500 dark:text-slate-400 pointer-events-none"
                    >
                      <span class="material-icons-sharp">search</span>
                    </div>
                    <input
                      class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full text-slate-900 dark:text-white focus:ring-4 focus:ring-primary/40 border-2 border-transparent bg-white/70 dark:bg-slate-900/50 backdrop-blur-sm h-16 placeholder:text-slate-500 dark:placeholder:text-slate-400 pl-14 pr-6 text-base font-medium leading-normal shadow-lg dark:shadow-lg-dark focus:border-primary/50 transition-all duration-300"
                      placeholder="Job title, keywords, or company"
                      value=""
                    />
                    <button
                      class="absolute right-2 top-1/2 -translate-y-1/2 flex min-w-[100px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-12 px-6 bg-primary text-white text-sm font-bold leading-normal tracking-wide hover:bg-primary-focus transition-all duration-300 shadow-md shadow-primary/40 hover:shadow-lg hover:shadow-primary/40"
                    >
                      <span class="truncate">Search</span>
                    </button>
                  </div>
                </label>
              </div>
            </div>
          </div>
          <div
            class="flex flex-wrap items-center gap-3 p-2 rounded-full glassmorphic w-full lg:w-max mx-auto shadow-md"
          >
            <button
              class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
            >
              <span
                class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
                >location_on</span
              >
              <p class="text-sm font-semibold leading-normal">Location</p>
            </button>
            <button
              class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
            >
              <span
                class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
                >work</span
              >
              <p class="text-sm font-semibold leading-normal">Job Type</p>
            </button>
            <button
              class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
            >
              <span
                class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
                >business_center</span
              >
              <p class="text-sm font-semibold leading-normal">Industry</p>
            </button>
            <button
              class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
            >
              <span
                class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
                >paid</span
              >
              <p class="text-sm font-semibold leading-normal">Salary</p>
            </button>
            <button
              class="text-sm font-medium text-primary hover:underline ml-auto pr-4"
            >
              Clear Filters
            </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              class="p-6 bg-white dark:bg-card-dark rounded-xl shadow-lg hover:shadow-2xl dark:hover:shadow-lg-dark hover:-translate-y-1 transition-all duration-300 group"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="flex flex-col gap-4 w-full">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-primary text-2xl font-bold"
                    >
                      <span class="material-icons-sharp !text-3xl">code</span>
                    </div>
                    <div class="flex-1">
                      <p
                        class="text-lg font-bold leading-tight text-slate-900 dark:text-white group-hover:text-primary transition-colors"
                      >
                        Senior Software Engineer
                      </p>
                      <p
                        class="text-sm font-medium text-text-light dark:text-text-dark"
                      >
                        Tech Solutions Inc.
                      </p>
                    </div>
                  </div>
                  <div
                    class="flex items-center gap-2 text-sm text-text-light dark:text-text-dark mt-1"
                  >
                    <span class="material-icons-sharp text-base"
                      >location_on</span
                    >
                    <span>Remote</span>
                  </div>
                  <p
                    class="text-sm text-text-light dark:text-text-dark line-clamp-3"
                  >
                    We are seeking a talented Senior Software Engineer to join
                    our dynamic team. The ideal candidate will have extensive
                    experience in building scalable web applications...
                  </p>
                  <button
                    class="mt-2 w-full flex items-center justify-center rounded-lg h-11 px-4 bg-primary text-white text-sm font-bold leading-normal hover:bg-primary-focus transition-colors duration-300 shadow-lg shadow-primary/30 transform group-hover:scale-105"
                  >
                    <span class="truncate">Apply Now</span>
                  </button>
                </div>
              </div>
            </div>
            <div
              class="p-6 bg-white dark:bg-card-dark rounded-xl shadow-lg hover:shadow-2xl dark:hover:shadow-lg-dark hover:-translate-y-1 transition-all duration-300 group"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="flex flex-col gap-4 w-full">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-600 text-2xl font-bold"
                    >
                      <span class="material-icons-sharp !text-3xl"
                        >devices</span
                      >
                    </div>
                    <div class="flex-1">
                      <p
                        class="text-lg font-bold leading-tight text-slate-900 dark:text-white group-hover:text-primary transition-colors"
                      >
                        Product Manager
                      </p>
                      <p
                        class="text-sm font-medium text-text-light dark:text-text-dark"
                      >
                        Innovate Corp.
                      </p>
                    </div>
                  </div>
                  <div
                    class="flex items-center gap-2 text-sm text-text-light dark:text-text-dark mt-1"
                  >
                    <span class="material-icons-sharp text-base"
                      >location_on</span
                    >
                    <span>New York, NY</span>
                  </div>
                  <p
                    class="text-sm text-text-light dark:text-text-dark line-clamp-3"
                  >
                    Innovate Corp. is looking for a passionate Product Manager
                    to drive product strategy and execution. You will work
                    closely with cross-functional teams to define...
                  </p>
                  <button
                    class="mt-2 w-full flex items-center justify-center rounded-lg h-11 px-4 bg-primary text-white text-sm font-bold leading-normal hover:bg-primary-focus transition-colors duration-300 shadow-lg shadow-primary/30 transform group-hover:scale-105"
                  >
                    <span class="truncate">Apply Now</span>
                  </button>
                </div>
              </div>
            </div>
            <div
              class="p-6 bg-white dark:bg-card-dark rounded-xl shadow-lg hover:shadow-2xl dark:hover:shadow-lg-dark hover:-translate-y-1 transition-all duration-300 group"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="flex flex-col gap-4 w-full">
                  <div class="flex items-center gap-4">
                    <div
                      class="w-14 h-14 rounded-xl bg-teal-100 dark:bg-teal-900 flex items-center justify-center text-teal-600 text-2xl font-bold"
                    >
                      <span class="material-icons-sharp !text-3xl"
                        >design_services</span
                      >
                    </div>
                    <div class="flex-1">
                      <p
                        class="text-lg font-bold leading-tight text-slate-900 dark:text-white group-hover:text-primary transition-colors"
                      >
                        UX/UI Designer
                      </p>
                      <p
                        class="text-sm font-medium text-text-light dark:text-text-dark"
                      >
                        Creative Minds LLC
                      </p>
                    </div>
                  </div>
                  <div
                    class="flex items-center gap-2 text-sm text-text-light dark:text-text-dark mt-1"
                  >
                    <span class="material-icons-sharp text-base"
                      >location_on</span
                    >
                    <span>San Francisco, CA</span>
                  </div>
                  <p
                    class="text-sm text-text-light dark:text-text-dark line-clamp-3"
                  >
                    We are on the lookout for a creative and detail-oriented
                    UX/UI Designer to create amazing user experiences. The ideal
                    candidate will have a strong portfolio...
                  </p>
                  <button
                    class="mt-2 w-full flex items-center justify-center rounded-lg h-11 px-4 bg-primary text-white text-sm font-bold leading-normal hover:bg-primary-focus transition-colors duration-300 shadow-lg shadow-primary/30 transform group-hover:scale-105"
                  >
                    <span class="truncate">Apply Now</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <FooterSection />
</template>

<script setup>
import AiTheWelcome from "@/components/AiTheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
import JobService from "@/services/jobService";
</script>