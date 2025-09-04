<template>
  <section class="bg-blue-50 dark:bg-slate-800" id="contact">
    <TheWelcome />
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
      <div class="mb-4">
        <div class="mb-6 max-w-3xl text-center sm:text-center md:mx-auto md:mb-12">
          <h2 style="color: orangered" class="font-heading mb-4 font-bold tracking-tight text-gray-900 dark:text-white text-3xl sm:text-5xl">
            Get in Touch
          </h2>
          <p class="mx-auto mt-4 max-w-3xl text-xl text-gray-600 dark:text-slate-400">
            We're here to help you grow your brand or connect with opportunities in Kenya and beyond.
          </p>
        </div>
      </div>

      <div class="flex items-stretch justify-center">
        <div class="grid md:grid-cols-2">
          <!-- Contact Details -->
          <div class="h-full pr-6">
            <p class="mt-3 mb-12 text-lg text-gray-600 dark:text-slate-400">
              Reach out to us for collaborations, support, or just to say hello. We're ready to connect!
            </p>
            <ul class="mb-6 md:mb-0">
              <li class="flex">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-blue-900 text-gray-50" style="background-color: orangered">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                       stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                    <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                  </svg>
                </div>
                <div class="ml-4 mb-4">
                  <h3 class="mb-2 text-lg font-medium leading-6 text-gray-900 dark:text-white">Our Address</h3>
                  <p class="text-gray-600 dark:text-slate-400">Valley View Office Nairobi, Kenya 00200</p>
                  <p class="text-gray-600 dark:text-slate-400">Nairobi, Kenya</p>
                </div>
              </li>
              <li class="flex">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-blue-900 text-gray-50" style="background-color:orangered">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                       stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                    <path d="M15 7a2 2 0 0 1 2 2"></path>
                    <path d="M15 3a6 6 0 0 1 6 6"></path>
                  </svg>
                </div>
                <div class="ml-4 mb-4">
                  <h3 class="mb-2 text-lg font-medium leading-6 text-gray-900 dark:text-white">Contact</h3>
                  <p class="text-gray-600 dark:text-slate-400">Mobile: +254 758 428 993</p>
                  <p class="text-gray-600 dark:text-slate-400">Email: info@careershyne.com</p>
                </div>
              </li>
              <li class="flex">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-blue-900 text-gray-50" style="background-color: orangered">
                  <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                       stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                    <path d="M12 7v5l3 3"></path>
                  </svg>
                </div>
                <div class="ml-4 mb-4">
                  <h3 class="mb-2 text-lg font-medium leading-6 text-gray-900 dark:text-white">Working Hours</h3>
                  <p class="text-gray-600 dark:text-slate-400">Mon – Fri: 8:00 AM – 5:00 PM</p>
                  <p class="text-gray-600 dark:text-slate-400">Sat: 9:00 AM – 1:00 PM</p>
                </div>
              </li>
            </ul>
          </div>

          <!-- Contact Form -->
          <div class="card h-fit max-w-6xl p-5 md:p-12" id="form">
            <h2 class="mb-4 text-2xl font-bold dark:text-white">Send Us a Message</h2>
            <form id="contactForm">
              <div class="mb-6">
                <div class="mb-4">
                  <input type="text" id="name" name="name" autocomplete="name" placeholder="Your Name"
                         class="w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm dark:text-gray-300" />
                </div>
                <div class="mb-4">
                  <input type="email" id="email" name="email" autocomplete="email" placeholder="Your Email Address"
                         class="w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm dark:text-gray-300" />
                </div>
                <div class="mb-4">
                  <textarea id="textarea" name="textarea" rows="5" placeholder="Your message..."
                            class="w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm dark:text-gray-300"></textarea>
                </div>
              </div>
              <div class="text-center">
                <button type="submit"  style="background-color: #fd624e"
                        class="w-full bg-blue-800 text-white px-6 py-3 font-semibold rounded-md hover:bg-blue-700 transition">
                  Send Message
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <FooterSection />
  </section>
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>
