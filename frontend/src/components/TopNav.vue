<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
const isMobileMenuOpen = ref(false);
const auth = useAuthStore();

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
const isLoggedIn = computed(() => !!auth.token);
</script>

<template>
  <nav class="bg-white dark:bg-gray-900 shadow-sm sticky top-0 z-50">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center py-4">
        <!-- Logo -->
        <router-link to="/" class="flex items-center space-x-2">
          <img src="/logo.png" alt="Logo" class="h-[80px] w-auto" />
        </router-link>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center space-x-8">
          <router-link to="/" class="nav-link" exact>Home</router-link>
          <router-link to="/about-us" class="nav-link">About Us</router-link>
          <!-- <router-link to="/influencers" class="nav-link"
            >Influencers</router-link
          > -->
          <router-link to="#services" class="nav-link"
            >Services</router-link
          >
          <router-link to="#pricing" class="nav-link">Pricing </router-link>
          <router-link to="/" class="nav-link">Resources</router-link>
          <router-link to="/contact-us" class="nav-link">Contact Us</router-link>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3">
          <router-link
            to="/login"
            class="hidden md:inline-block px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-purple-800 transition"
          >
            Get Started
          </router-link>

          <!-- Mobile menu toggle -->
          <button
            @click="toggleMobileMenu"
            class="inline-flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg lg:hidden focus:outline-none"
          >
            <svg
              v-if="!isMobileMenuOpen"
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
            <svg
              v-else
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <transition name="fade">
        <div v-if="isMobileMenuOpen" class="lg:hidden pb-4">
          <div class="flex flex-col space-y-3">
            <router-link
              @click="toggleMobileMenu"
              to="/"
              class="nav-link-mobile"
              >Home</router-link
            >
            <router-link
              @click="toggleMobileMenu"
              to="/"
              class="nav-link-mobile"
              >Custom Cvs</router-link
            >
            <!-- <router-link
              @click="toggleMobileMenu"
              to="/influencers"
              class="nav-link-mobile"
              >Influencers</router-link
            > -->
            <router-link
              @click="toggleMobileMenu"
              to="/"
              class="nav-link-mobile"
              >Cover Letters</router-link
            >
            <router-link
              @click="toggleMobileMenu"
              to="/"
              class="nav-link-mobile"
              >How It Works</router-link
            >
            <router-link
              @click="toggleMobileMenu"
              to="/"
              class="nav-link-mobile"
              >Who Are We</router-link
            >
            <router-link
              @click="toggleMobileMenu"
              to="/"
              class="nav-link-mobile"
              >Contact</router-link
            >
          </div>
        </div>
      </transition>
    </div>
  </nav>
</template>

<style scoped>
.nav-link {
  @apply text-gray-700 dark:text-gray-300 hover:text-purple-700 dark:hover:text-white font-medium transition;
}
.nav-link-mobile {
  @apply block w-full px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
