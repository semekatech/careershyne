<template>
  <div class="bg-gray-100 font-sans flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-if="isSidebarOpen || isDesktop"
        class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-900 flex flex-col border-r border-gray-200 dark:border-gray-700 z-30 md:relative md:flex md:flex-shrink-0"
      >
        <div class="flex flex-col h-full">
          <!-- Logo & Close Button -->
          <div class="flex items-center justify-between h-16 px-4">
            <a href="/dashboard" class="flex items-center">
              <img
                src="/logo.png"
                alt="Careershyne Logo"
                class="h-15 w-auto mb-0"
              />
            </a>
            <button
              v-if="!isDesktop"
              @click="toggleSidebar"
              class="text-black dark:text-white text-2xl focus:outline-none"
              aria-label="Close sidebar"
            >
              &times;
            </button>
          </div>

          <!-- Sidebar Navigation -->
          <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <router-link
              v-for="link in filteredLinks"
              :key="link.name"
              :to="link.href"
              @click="link.name === 'Logout' ? handleLogout() : null"
              class="flex items-center px-4 py-2 rounded-lg transition-colors"
              :class="
                isActive(link.href)
                  ? 'bg-orange-500 text-white'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'
              "
            >
              <!-- Material Icon -->
              <span
                v-if="link.icon && link.iconName"
                :class="link.icon + ' mr-3'"
              >
                {{ link.iconName }}
              </span>

              <!-- FontAwesome Icon -->
              <font-awesome-icon
                v-if="link.faIcon"
                :icon="link.faIcon"
                class="mr-3"
              />

              {{ link.name }}
            </router-link>
          </nav>

          <!-- User Info -->
          <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
              <img
                class="w-10 h-10 rounded-full"
                :src="preview || defaultAvatar"
                alt="User"
              />
              <div class="ml-3">
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                  {{ auth.user?.name }}
                </p>
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
    <div class="flex flex-col flex-1 h-screen">
      <!-- Top Navigation -->
      <header
        class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200 flex-shrink-0"
      >
        <div class="flex items-center">
          <!-- Hamburger button on small devices -->
          <button
            class="md:hidden text-gray-500 dark:text-gray-300 focus:outline-none"
            @click="toggleSidebar"
            aria-label="Open sidebar"
          >
            <font-awesome-icon icon="bars" />
          </button>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white ml-4">
            {{ pageTitle }}
          </h1>
        </div>

        <div class="flex items-center space-x-4">
          <button class="text-gray-500 dark:text-gray-300 focus:outline-none">
            <i class="fas fa-bell"></i>
          </button>
          <button class="text-gray-500 dark:text-gray-300 focus:outline-none">
            <i class="fas fa-envelope"></i>
          </button>
          <a href="/profile" class="flex items-center focus:outline-none">
            <img
              class="w-8 h-8 rounded-full"
              :src="preview || defaultAvatar"
              alt="User"
            />
          </a>
        </div>
      </header>
      <main class="flex-1 overflow-y-auto p-6 bg-gray-100 dark:bg-gray-800">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const pageTitle = computed(() => route.meta.title || "Dashboard");

// Sidebar state
const isSidebarOpen = ref(false);
const isDesktop = ref(window.innerWidth >= 768);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const handleResize = () => {
  isDesktop.value = window.innerWidth >= 768;
  if (isDesktop.value) isSidebarOpen.value = false;
};

onMounted(() => window.addEventListener("resize", handleResize));
onBeforeUnmount(() => window.removeEventListener("resize", handleResize));

const defaultAvatar = auth.user?.photo
  ? `https://demo.ngumzo.com/storage/${auth.user.photo}`
  : "/profile.jpg";
const preview = ref(null); // optional for avatar preview

// Logout function
const handleLogout = () => {
  auth.clearToken();
  router.push("/admin");
};

// Sidebar links with Material & FontAwesome icons
const sidebarLinks = [
  {
    name: "Dashboard",
    href: "/dashboard",
    icon: "material-icons-sharp",
    iconName: "dashboard",
  },
  {
    name: "Manage Orders",
    href: "/manage-orders",
    faIcon: ["fas", "shopping-cart"],
    roles: ["1109", "manager"],
  },
  {
    name: "Manage Payments",
    href: "/manage-payments",
    faIcon: ["fas", "credit-card"],
    roles: ["1109", "manager"],
  },
  {
    name: "Manage Users",
    href: "/manage-users",
    faIcon: ["fas", "users"],
    roles: ["1109", "manager"],
  },
  {
    name: "Manage Jobs",
    href: "/manage-jobs",
    icon: "material-icons-sharp",
    iconName: "business_center",
    roles: ["1109", "manager"],
  },
  {
    name: "Personal Summary",
    href: "/profile",
    icon: "material-icons-sharp",
    iconName: "person",
    roles: ["1109", "1098"],
  },
  {
    name: "My Plans",
    href: "/my-plans",
    icon: "material-icons-sharp",
    iconName: "workspace_premium",
    roles: ["1098"],
  },
  {
    name: "My Jobs",
    href: "/browse-jobs",
    icon: "material-icons-sharp",
    iconName: "business_center",
    roles: ["1098"],
  },
  {
    name: "My Activities",
    href: "/browse-acctivities",
    icon: "material-icons-sharp",
    iconName: "history",
    roles: ["1098"],
  },
  { name: "Logout", href: "#", faIcon: ["fas", "sign-out-alt"] },
];

// Filter links by user role
const filteredLinks = computed(() => {
  return sidebarLinks.filter(
    (link) => !link.roles || link.roles.includes(auth.user?.role)
  );
});

// Highlight active route
const isActive = (href) => {
  return route.path === href;
};
</script>

<style scoped>
/* Sidebar slide transition */
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

/* Material icon size */
.material-icons-sharp {
  font-size: 1.25rem;
  line-height: 1;
  vertical-align: middle;
}
</style>