
<template>
  <div class="bg-gray-100 font-sans flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <transition name="slide">
      <aside
        v-if="isSidebarOpen || isDesktop"
        class="fixed inset-y-0 left-0 w-64 bg-surface-light dark:bg-surface-darkflex flex-col border-r border-gray-200 dark:border-gray-700 z-30 md:relative md:flex md:flex-shrink-0"
      >
        <div class="flex flex-col h-full">
          <div class="flex items-center justify-center h-16 px-4">
            <a href="/dashboard" class="flex items-center">
              <span
                class="self-center text-xl font-semibold whitespace-nowrap text-black bg-clip-text text-black"
              >
                <strong>CAREER PORTAL</strong>
              </span>
            </a>
            <!-- Close button on small screens -->
            <button
              v-if="!isDesktop"
              @click="toggleSidebar"
              class="ml-auto text-white text-xl focus:outline-none"
              aria-label="Close sidebar"
            >
              &times;
            </button>
          </div>
          <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a
              href="/dashboard"
              class="flex items-center px-4 py-2 text-white bg-primary rounded-lg"
            >
              <span class="material-icons-sharp mr-3">dashboard</span>

              Dashboard
            </a>

              <!-- ===== Admin Menus ===== -->
            <!-- Manage Orders -->
            <a
              href="/manage-orders"
              v-if="auth.user?.role !== '1098'"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <font-awesome-icon
                :icon="['fas', 'shopping-cart']"
                class="mr-3"
              />
              Manage Orders
            </a>

            <!-- Manage Payments -->
            <a
              v-if="auth.user?.role !== 'radio' && auth.user?.role !== '1098'"
              href="/manage-payments"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <font-awesome-icon :icon="['fas', 'credit-card']" class="mr-3" />
              Manage Payments
            </a>

            <a
              v-if="auth.user?.role !== 'radio' && auth.user?.role !== '1098'"
              href="/manage-users"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <font-awesome-icon :icon="['fas', 'users']" class="mr-3" />
              Manage Users
            </a>
              <a
              v-if="auth.user?.role !== 'radio' && auth.user?.role !== '1098'"
              href="/manage-jobs"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
             <span class="material-icons-sharp mr-3">business_center</span>

             Manage Jobs
            </a>
            
            <!-- ===== Jobseeker Menus ===== -->
            <!-- Personal Summary -->
            <a
              v-if="auth.user?.role != 'radio'"
              href="/personal-summary"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">person</span>
             Personal Summary
            </a>

            <!-- My Plans -->
            <a
              v-if="auth.user?.role != 'radio'"
              href="/dashboard/my-plans"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">workspace_premium</span>
              My Plans
            </a>

            <!-- My Jobs -->
            <a
              v-if="auth.user?.role != 'radio'"
              href="/my-jobs"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">business_center</span>
              My Jobs
            </a>

            <!-- My Activities -->
            <a
              v-if="auth.user?.role != 'radio'"
              href="/my-activities"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">history</span>
              My Activities
            </a>

            <!-- Cover Letters -->
            <!-- <a
              v-if="auth.user?.role != 'radio'"
              href="/cover-letter-generator"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">attach_file</span>
              Cover Letters
            </a> -->

            <!-- Email Templates -->
            <!-- <a
              v-if="auth.user?.role != 'radio'"
              href="/email-template-generator"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">email</span>

              Email Templates
            </a> -->

            <!-- CV Revamp -->
            <!-- <a
              v-if="auth.user?.role != 'radio'"
              href="/cv-revamp-generator"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <span class="material-icons-sharp mr-3">mail_outline</span>

              CV Revamp
            </a> -->

            <!-- Logout -->
            <a
              href="#"
              @click.prevent="handleLogout"
              class="flex items-center px-4 py-2 text-muted-light dark:text-muted-dark hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              <font-awesome-icon :icon="['fas', 'sign-out-alt']" class="mr-3" />
              Logout
            </a>
          </nav>

          <div class="p-4 border-t border-white-700">
            <div class="flex items-center">
              <img
                class="w-10 h-10 rounded-full"
                :src="preview || defaultAvatar"
                alt="User"
              />
              <div class="ml-3">
                <p class="text-sm font-medium">{{ auth.user?.name }}</p>
                <!-- <p class="text-xs text-blue-200">Admin</p> -->
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
          <h1
            style="color: #2c3137"
            class="text-xl font-semibold text-gray-800 ml-4"
          >
            {{ pageTitle }}
          </h1>
        </div>
        <div class="flex items-center space-x-4">
          <button class="text-gray-500 focus:outline-none">
            <i class="fas fa-bell"></i>
          </button>
          <button class="text-gray-500 focus:outline-none">
            <i class="fas fa-envelope"></i>
          </button>
          <div class="relative">
            <a href="/my-profile" class="flex items-center focus:outline-none">
              <img
                class="w-8 h-8 rounded-full"
                :src="preview || defaultAvatar"
                alt="User"
              />
            </a>
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
<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useRoute } from "vue-router";
const route = useRoute();
const router = useRouter();
const pageTitle = computed(() => route.meta.title || "Dashboard");

const auth = useAuthStore();

const isSidebarOpen = ref(false);
const isDesktop = ref(window.innerWidth >= 768);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const handleResize = () => {
  isDesktop.value = window.innerWidth >= 768;
  if (isDesktop.value) {
    isSidebarOpen.value = false;
  }
};

onMounted(() => {
  window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
});
const defaultAvatar = auth.user?.photo
  ? `https://demo.ngumzo.com/storage/${auth.user.photo}`
  : "/profile.jpg";

const handleLogout = () => {
  auth.clearToken();
  router.push("/admin");
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

.material-icons-sharp {
  font-size: 1.25rem;
}
</style>



