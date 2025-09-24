<template>
  <div
    class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8"
    style="background: #fffbf7; width: 450px"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <a href="/" class="flex items-center justify-center">
        <img src="/logo.png" alt="Logo" class="h-16 w-auto" />
      </a>
      <h2
        class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900"
      >
        Login Here
      </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="handleLogin">
          <div class="mt-6">
            <label
              for="email"
              class="block text-sm font-medium leading-5 text-gray-700"
            >
              Email address
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                id="email"
                name="email"
                type="email"
                v-model="email"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              />
            </div>
          </div>

          <div class="mt-6">
            <label
              for="password"
              class="block text-sm font-medium leading-5 text-gray-700"
            >
              Password
            </label>
            <div class="mt-1 rounded-md shadow-sm">
              <input
                id="password"
                name="password"
                type="password"
                v-model="password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
              />
            </div>
          </div>

          <div class="mt-6">
            <span class="block w-full rounded-md shadow-sm">
              <button
                :disabled="loading"
                style="background: #fd624e"
                type="submit"
                class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out"
              >
                <span v-if="loading">Logging in...</span>
                <span v-else>Login</span>
              </button>
            </span>
            <router-link
              to="/register"
              class="block mt-4 text-center font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:underline transition ease-in-out duration-150"
            >
              Create an account
            </router-link>
          </div>
        </form>
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
const route = useRoute();
const router = useRouter();
const $toast = useToast();
const auth = useAuthStore();
const handleLogin = async () => {
  if (!email.value || !password.value) {
    $toast.error("Please fill in both email and password.");
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
    const redirectPath = route.query.redirect || "/dashboard";
    router.push(redirectPath);
  } catch (error) {
    if (error.response) {
      $toast.error("Invalid login credentials!", { position: "top-right" });
    } else {
      $toast.error("Server error. Please try again.", {
        position: "top-right",
      });
    }
  } finally {
    loading.value = false;
  }
};
</script>


