
<template>
  <div class="bg-gray-100 font-sans flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-if="isSidebarOpen || isDesktop"
        class="fixed inset-y-0 left-0 w-64 bg-blue-800 text-white z-30 md:relative md:flex md:flex-shrink-0"
      >
        <div class="flex flex-col h-full">
          <div class="flex items-center justify-center h-16 px-4 bg-blue-900">
            <span class="text-xl font-semibold">Hotel Vista</span>
            <!-- Close button on small screens -->
            <button
              v-if="!isDesktop"
              @click="toggleSidebar"
              class="ml-auto text-white text-xl focus:outline-none"
              aria-label="Close sidebar"
            >
              hged &times;
            </button>
          </div>
          <nav
            class="flex flex-col flex-grow px-4 py-4 overflow-y-auto space-y-2"
          >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md bg-blue-700 text-white"
              >Dashboard</a
            >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-blue-700 text-white"
              >Bookings</a
            >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-blue-700 text-white"
              >Rooms</a
            >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-blue-700 text-white"
              >Guests</a
            >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-blue-700 text-white"
              >Services</a
            >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-blue-700 text-white"
              >Reports</a
            >
            <a
              href="#"
              class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-blue-700 text-white"
              >Settings</a
            >
          </nav>
          <div class="p-4 border-t border-blue-700">
            <div class="flex items-center">
              <img
                class="w-10 h-10 rounded-full"
                src="https://randomuser.me/api/portraits/women/11.jpg"
                alt="User"
              />
              <div class="ml-3">
                <p class="text-sm font-medium">Sarah Johnson</p>
                <p class="text-xs text-blue-200">Admin</p>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </transition>

    <!-- Overlay for small screens -->
    <div
      v-if="isSidebarOpen && !isDesktop"
      @click="toggleSidebar"
      class="fixed inset-0 bg-black bg-opacity-50 z-20"
    ></div>

    <!-- Main Content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Top Navigation -->
      <header
        class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200"
      >
        <div class="flex items-center">
          <!-- Hamburger button visible on small devices -->
          <button
            class="md:hidden text-gray-500 focus:outline-none"
            @click="toggleSidebar"
            aria-label="Open sidebar"
          >
            <font-awesome-icon icon="bars" />
          </button>
          <h1 class="text-xl font-semibold text-gray-800 ml-4">Dashboard</h1>
        </div>
        <div class="flex items-center space-x-4">
          <button class="text-gray-500 focus:outline-none">
            <i class="fas fa-bell"></i>
          </button>
          <button class="text-gray-500 focus:outline-none">
            <i class="fas fa-envelope"></i>
          </button>
          <div class="relative">
            <button class="flex items-center focus:outline-none">
              <img
                class="w-8 h-8 rounded-full"
                src="https://randomuser.me/api/portraits/women/11.jpg"
                alt="User"
              />
            </button>
          </div>
        </div>
      </header>

      <!-- Main Content Area -->
      <main class="flex-1 overflow-y-auto p-6 bg-gray-100">
       <router-view />
      </main>
    </div>
  </div>
</template>

<script>
import DashboardStats from "../components/DashboardStats.vue";
import WelcomeToast from "@/components/WelcomeToast.vue";
export default {
  data() {
    return {
      isSidebarOpen: false,
      isDesktop: window.innerWidth >= 768,
    };
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    handleResize() {
      this.isDesktop = window.innerWidth >= 768;
      if (this.isDesktop) {
        this.isSidebarOpen = false; // Sidebar always visible on desktop, no overlay
      }
    },
  },
  mounted() {
    window.addEventListener("resize", this.handleResize);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.handleResize);
  },
};

</script>
<style scoped>
/* Slide transition for sidebar */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}
.slide-enter-to,
.slide-leave-from {
  transform: translateX(0);
}
</style>



