<template>
  <div class="min-h-screen flex w-full">
    <div
      class="hidden md:flex flex-col justify-center items-center text-white relative flex-1 p-16"
      style="background-color: #f97316"
    >
      <div class="absolute inset-0 z-0">
        <img
          src="/img/login-bg.png"
          alt="Merchandise background"
          class="w-full h-full object-cover opacity-75"
        />
      </div>
      <div class="relative z-10 text-center">
        <p class="text-xl font-semibold mb-2">Your AI tools are waiting.</p>
        <div class="text-3xl font-bold tracking-tight mb-4 text-white">
          WELCOME BACK
        </div>
        <p class="text-sm px-8 leading-relaxed">
          Log in to continue exploring the full suite of Careershyne AI tools.
          Your personalized dashboard is ready to provide the insights you need
          to take the next step in your career.
        </p>
      </div>
    </div>

    <div
      class="bg-white flex flex-col justify-center items-center flex-1 py-12 px-4 sm:px-6 lg:px-8"
    >
      <div class="w-full max-w-sm">
        <div class="flex flex-col items-center">
          <img
            src="/logo.png"
            alt="Careershyne Logo"
            class="h-20 w-auto mb-4"
          />
          <h2 class="text-2xl font-semibold text-gray-800">Login Account</h2>
          <p class="text-sm text-gray-500 mt-1">
            Sign in to continue exploring Careershyne AI tools.
          </p>
          <p v-if="errorMessage" class="text-sm text-red-600 mt-2 text-center">
            {{ errorMessage }}
          </p>
        </div>
        <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email Address
            </label>
            <div class="mt-1">
              <input
                id="email"
                name="email"
                type="email"
                v-model="email"
                required
                placeholder=" "
                class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
              />
            </div>
          </div>

          <div>
            <label
              for="password"
              class="block text-sm font-medium text-gray-700"
            >
              Password
            </label>
            <div class="mt-1 relative">
              <input
                id="password"
                name="password"
                :type="passwordVisible ? 'text' : 'password'"
                v-model="password"
                required
                placeholder=" "
                class="block w-full pr-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"
              />
              <button
                type="button"
                @click="togglePasswordVisibility"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5"
              >
                <svg
                  v-if="!passwordVisible"
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                  ></path>
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                  ></path>
                </svg>
                <svg
                  v-else
                  class="h-5 w-5 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.487 0 2.906.331 4.25 1M17 14V9"
                  ></path>
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M10.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.487 0 2.906.331 4.25 1M17 14V9"
                  ></path>
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5H4a2 2 0 00-2 2v9a2 2 0 002 2h4l1.5 1.5M16 12l2-2 4-4M19 12l-2 2-4 4"
                  ></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Remember Me
              </label>
            </div>
            <div class="text-sm">
              <a href="#" class="font-medium text-red-600 hover:text-red-500">
                Forgot Password?
              </a>
            </div>
          </div>

          <div>
            <button
              :disabled="loading"
              type="submit"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white"
              style="background-color: #f97316"
            >
              <span v-if="loading">Logging in...</span>
              <span v-else>Login</span>
            </button>
          </div>
        </form>
        <div class="mt-6 text-center text-sm">
          Don't have an account?
          <a
            href="/register"
            class="font-medium text-red-600 hover:text-red-500"
          >
            Register here
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toast-notification";
import { useAuthStore } from "@/stores/auth";
import LoginService from "@/services/loginService";

const email = ref("");
const password = ref("");
const loading = ref(false);
const errorMessage = ref("");
const passwordVisible = ref(false);
const route = useRoute();
const router = useRouter();
const $toast = useToast();
const auth = useAuthStore();

const togglePasswordVisibility = () => {
  passwordVisible.value = !passwordVisible.value;
};

const handleLogin = async () => {
  errorMessage.value = "";

  if (!email.value || !password.value) {
    errorMessage.value = "Please fill in both email and password.";
    return;
  }

  loading.value = true;

  try {
    const response = await LoginService.post({
      email: email.value,
      password: password.value,
    });
    const data = response.data;
    $toast.success("Login successful!", { position: "top-right" });

    const token = data.access_token;
    const user = data.user;
    auth.setToken(token);
    auth.setUser(user);
    const redirectPath = user.redirect || "/dashboard";
    router.push({ name: redirectPath });
  } catch (error) {
    if (error.response) {
      errorMessage.value = "Invalid login credentials! Please try again";
    } else {
      errorMessage.value = "Server error. Please try again.";
    }
  } finally {
    loading.value = false;
  }
};
</script>