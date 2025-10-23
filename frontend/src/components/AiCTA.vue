<template>
  <section
    class="relative bg-[#1e293b] py-20 text-white rounded-xl shadow-xl mx-4 overflow-hidden"
  >
    <!-- Subtle overlay texture -->
    <div
      class="absolute inset-0 bg-[url('https://www.toptal.com/designers/subtlepatterns/patterns/dots.png')] opacity-10 rounded-3xl"
    ></div>

    <div class="relative max-w-4xl mx-auto text-center px-6">
      <!-- HEADLINE -->
      <h2 class="text-3xl md:text-4xl font-extrabold drop-shadow-lg">
        Join Our Career Network Today 🚀
      </h2>
      <p
        class="mt-4 text-lg md:text-xl text-white-100 drop-shadow-lg max-w-2xl mx-auto"
      >
        Get access to the latest job opportunities, professional support, and
        exclusive career tools. Connect with others, learn, and grow through our
        WhatsApp community.
      </p>

      <!-- SOCIAL PROOF -->
      <div class="mt-6 flex flex-col items-center justify-center">
        <div class="flex text-green-400 mb-2">
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
        <!-- <p class="text-white-100 text-sm font-medium">
          🌍 Already trusted by
          <span class="font-semibold text-orange-400">500+ job seekers</span>
          across Africa
        </p> -->
      </div>

      <!-- CTA BUTTONS -->
      <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
        <!-- Secondary CTA -->
        <a
    
          href="/learn-more"
          class="px-6 py-3 border border-white text-white font-semibold rounded-lg hover:bg-white hover:text-gray-900 transition duration-300"

        >
          Learn More
        </a>

        <!-- Primary CTA -->
        <a
          href="https://chat.whatsapp.com/IYIf4uf3xOw7HhEuVmiAb0"
          target="_blank"
          class="px-6 py-3 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 transition duration-300 flex items-center justify-center gap-2"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="currentColor"
            viewBox="0 0 24 24"
            class="w-5 h-5"
          >
            <path
              fill-rule="evenodd"
              d="M12 2C6.486 2 2 6.383 2 11.99c0 2.11.688 4.065 1.84 5.66L2 22l4.516-1.774A9.988 9.988 0 0012 22c5.514 0 10-4.383 10-9.99C22 6.383 17.514 2 12 2zm0 18a7.951 7.951 0 01-4.266-1.223l-.303-.188-2.682 1.054.512-2.616-.17-.266A7.928 7.928 0 014 11.99C4 7.59 7.589 4 12 4s8 3.59 8 7.99-3.589 8.01-8 8.01zm4.235-5.27c-.229-.114-1.358-.671-1.569-.747-.211-.076-.364-.114-.517.114-.152.229-.594.747-.727.901-.134.152-.267.171-.496.057-.229-.114-.968-.357-1.844-1.138a6.916 6.916 0 01-1.26-1.563c-.134-.229-.014-.353.1-.467.103-.102.229-.267.343-.4.114-.134.152-.229.229-.381.076-.152.038-.286-.019-.4-.057-.114-.517-1.245-.709-1.705-.186-.445-.375-.385-.517-.393l-.44-.008c-.152 0-.4.057-.611.286-.211.229-.8.781-.8 1.904 0 1.122.82 2.204.934 2.356.114.152 1.61 2.472 3.9 3.468 2.29.996 2.29.664 2.703.624.413-.038 1.358-.552 1.55-1.084.191-.533.191-.99.134-1.084-.057-.095-.211-.152-.44-.267z"
              clip-rule="evenodd"
            />
          </svg>
          Join WhatsApp Group
        </a>
      </div>
    </div>
  </section>
</template>
