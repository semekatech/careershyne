<template>
  <section id="contact">
    <!-- ===== Header ===== -->
    <TheWelcome />

    <!-- ===== Hero Section ===== -->
 <div class="relative bg-black text-white py-24">
      <img
        src="/img/contact-hero.jpg"
        alt="Careershyne contact hero"
        class="absolute inset-0 w-full h-full object-cover"
      />
      <div class="absolute inset-0 bg-gray-900/50"></div>



      <!-- Hero Text -->
      <div class="relative text-center text-white z-10">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">Contact Us</h1>
        <p class="text-lg md:text-xl text-orange-400 font-semibold">Let’s Shape Your Brighter Future Together</p>
      </div>
    </div>

    <!-- ===== Intro Section ===== -->
<!-- ===== Intro Section ===== -->
<div class="bg-gray-100 dark:bg-gray-900">
  <div class="py-12 md:py-16 lg:py-20">
    <div class="max-w-4xl mx-auto text-center px-6">
      <p class="text-lg text-gray-700 dark:text-gray-300">
        Have a question about our services? Need help choosing the right package?  
        Or just want to talk about your career goals? We’d love to hear from you.
      </p>
    </div>
  </div>



    <!-- ===== Contact Section ===== -->
    <div class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8 mt-12 md:mt-16">
      <div class="grid md:grid-cols-2 gap-12 items-start">
        <!-- Contact Details -->
        <div class="pr-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Details</h2>
          <ul class="space-y-6">
            <li class="flex">
              <div class="flex h-10 w-10 items-center justify-center rounded text-white" style="background-color: orange">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                  <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium">Our Address</h3>
                <p class="text-gray-600">Valley View Office Nairobi, Kenya 00200</p>
              </div>
            </li>
            <li class="flex">
              <div class="flex h-10 w-10 items-center justify-center rounded text-white" style="background-color: orange">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                  <path d="M15 7a2 2 0 0 1 2 2"></path>
                  <path d="M15 3a6 6 0 0 1 6 6"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium">Contact</h3>
                <p class="text-gray-600">Mobile: +254 758 428 993</p>
                <p class="text-gray-600">Email: info@careershyne.com</p>
              </div>
            </li>
            <li class="flex">
              <div class="flex h-10 w-10 items-center justify-center rounded text-white" style="background-color: orange">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                  <path d="M12 7v5l3 3"></path>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium">Working Hours</h3>
                <p class="text-gray-600">Mon – Fri: 8:00 AM – 5:00 PM</p>
                <p class="text-gray-600">Sat: 9:00 AM – 1:00 PM</p>
              </div>
            </li>
          </ul>
        </div>

        <!-- Contact Form -->
        <div class="card h-fit max-w-6xl p-5 md:p-12 shadow-lg rounded-lg bg-white">
          <h2 class="mb-4 text-2xl font-bold text-gray-900">Send Us a Message</h2>
          <form id="contactForm">
            <div class="space-y-4">
              <input type="text" name="name" placeholder="Your Name"
                class="w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm" />
              <input type="email" name="email" placeholder="Your Email Address"
                class="w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm" />
              <textarea name="message" rows="5" placeholder="Your message..."
                class="w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm"></textarea>
            </div>
            <div class="mt-6 text-center">
              <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 font-semibold rounded-md transition">
                Send Message
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
    <!-- ===== Footer ===== -->
    <FooterSection />
  </section>
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>
