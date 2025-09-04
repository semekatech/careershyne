<template>
  <footer class="flex flex-col space-y-10 justify-center m-10">

    <nav class="flex justify-center flex-wrap gap-6 text-gray-500 font-medium">
      <a class="hover:text-gray-900" href="#">Home</a>
      <a class="hover:text-gray-900" href="#">About</a>
      <a class="hover:text-gray-900" href="#">Services</a>
      <a class="hover:text-gray-900" href="#">Media</a>
      <a class="hover:text-gray-900" href="#">Gallery</a>
      <a class="hover:text-gray-900" href="#">Contact</a>
    </nav>

    <div class="flex justify-center space-x-5">
      <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/facebook-new.png" />
      </a>
      <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/linkedin-2.png" />
      </a>
      <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/instagram-new.png" />
      </a>
      <a href="https://messenger.com" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/facebook-messenger--v2.png" />
      </a>
      <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">
        <img src="https://img.icons8.com/fluent/30/000000/twitter.png" />
      </a>
    </div>

    <p class="text-center text-gray-700 font-medium">&copy; 2022 Company Ltd. All rights reservered.</p>

    <!-- Floating WhatsApp Chat Button -->
    <a 
      href="https://wa.me/+254758393029" 
      target="_blank" 
      rel="noopener noreferrer" style="border-radius:10px"
      class="fixed bottom-5 right-5 bg-[#32e153] hover:bg-green-600 text-white px-5 py-3  shadow-lg flex items-center space-x-2 font-medium transition transform hover:scale-105"
    >
      <span>Chat with us</span>
      <img src="https://img.icons8.com/ios-filled/24/ffffff/whatsapp.png" alt="WhatsApp" />
    </a>
  </footer>
</template>
