<template>
  <TheWelcome />
  <div class="bg-gray-100 dark:bg-gray-900">
    <div class="relative bg-black text-white py-24">
      <img
        src=""
        alt="Person typing on a laptop"
        class="absolute inset-0 w-full h-full object-cover opacity-50"
      />
      <div class="relative container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">About Us</h1>
        <p class="text-lg md:text-xl text-orange-400 font-semibold">
          Shaping Brighter Futures
        </p>
      </div>
    </div>

    <div class="container mx-auto px-4 py-16">
      <div
        class="max-w-4xl mx-auto space-y-8 text-lg text-gray-800 dark:text-gray-200"
      >
        <p>
          At <span class="font-bold">CareerShyne</span>, we know how
          discouraging it can feel to send out countless applications and hear
          nothing back. Many talented professionals never get past automated
          filters, not because they lack ability, but because their story isn’t
          told the right way.
        </p>
        <p>
          What drives us is simple:
          <span class="font-bold"
            >we believe everyone deserves the chance to be seen, heard, and
            valued.</span
          >
        </p>
        <p>
          We exist to give you more than a CV — we give you confidence, clarity,
          and the tools to step boldly into new opportunities. Every success
          story we help create reminds us why we started: to shape brighter
          futures, one career at a time.
        </p>
      </div>
    </div>
  </div>
  <FooterSection />
</template>

<script setup>
import TheWelcome from "@/components/TheWelcome.vue";
import FooterSection from "@/components/FooterSection.vue";
</script>

