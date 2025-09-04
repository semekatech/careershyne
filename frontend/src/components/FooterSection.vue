<script setup>
const scrollToSection = (id) => {
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: "smooth" });
  }
};
</script>

<template>
  <footer class="flex flex-col space-y-10 justify-center m-10">
    <!-- Footer Navigation -->
    <nav class="flex justify-center flex-wrap gap-6 text-gray-500 font-medium">
      <router-link to="/" class="hover:text-gray-900">Home</router-link>
      <router-link to="/about-us" class="hover:text-gray-900">About</router-link>
      
      <!-- ✅ smooth scroll links -->
      <button @click="scrollToSection('services')" class="hover:text-gray-900">
        Services
      </button>
      <button @click="scrollToSection('pricing')" class="hover:text-gray-900">
        Pricing
      </button>
      
      <router-link to="/contact-us" class="hover:text-gray-900">Contact</router-link>
    </nav>

    <!-- Social Icons -->
    <div class="flex justify-center space-x-5">
      <a href="https://web.facebook.com/people/Career-Shyne/61580156437434/" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/facebook-new.png" />
      </a>
      <a href="https://www.linkedin.com/company/career-shyne" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/linkedin-2.png" />
      </a>
      <a href="https://www.instagram.com/careershyne/" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/instagram-new.png" />
      </a>
      <a href="https://x.com/CareerShyne" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/twitter.png" />
      </a>
    </div>

    <!-- Copy -->
    <p class="text-center text-gray-700 font-medium">
      &copy; 2025 CareerShyne. All rights reserved.
    </p>

    <!-- Floating WhatsApp Chat Button -->
    <a 
      href="https://wa.me/+254758393029" 
      target="_blank" 
      rel="noopener noreferrer"
      style="border-radius:10px"
      class="fixed bottom-5 right-5 bg-[#32e153] hover:bg-green-600 text-white px-5 py-3 shadow-lg flex items-center space-x-2 font-medium transition transform hover:scale-105"
    >
      <span>Chat with us</span>
      <img src="https://img.icons8.com/ios-filled/24/ffffff/whatsapp.png" alt="WhatsApp" />
    </a>
  </footer>
</template>
