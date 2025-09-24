<template>
  <div class="min-h-screen flex w-full">
    <div
      class="hidden md:flex flex-col justify-center items-center text-white relative flex-1 p-16"
      style="background-color: #f97316"
    >
      <div class="absolute inset-0 z-0">
        <img
          src="/img/login-bg.png"
          alt="Careershyne background"
          class="w-full h-full object-cover opacity-75"
        />
      </div>
      <div class="relative z-10 text-center">
        <p class="text-xl font-semibold mb-2">Welcome to Careershyne!</p>
        <div class="text-3xl font-bold tracking-tight mb-4 text-white">
          CREATE ACCOUNT
        </div>
        <p class="text-sm px-8 leading-relaxed">
          Sign up today to explore our full suite of AI tools. Your personalized dashboard will provide the insights you need to take the next step in your career.
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
          <h2 class="text-2xl font-semibold text-gray-800">Create Account</h2>
          <p class="text-sm text-gray-500 mt-1">
            Fill in your details to get started with Careershyne AI tools.
          </p>
          <p v-if="successMessage" class="text-sm text-green-600 mt-2 text-center">
            {{ successMessage }}
          </p>
          <p v-if="errorMessage" class="text-sm text-red-600 mt-2 text-center">
            {{ errorMessage }}
          </p>
        </div>
        <form @submit.prevent="handleRegister" class="mt-8 space-y-6">
          <div>
            <label for="fullName" class="block text-sm font-medium text-gray-700">
              Full Name
            </label>
            <div class="mt-1">
              <input
                id="fullName"
                name="fullName"
                type="text"
                v-model="fullName"
                required
                placeholder=" "
                class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
              />
            </div>
          </div>
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
                class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
              />
            </div>
          </div>
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">
              Phone Number
            </label>
            <div class="mt-1">
              <input
                id="phone"
                name="phone"
                type="tel"
                v-model="phone"
                required
                placeholder=" "
                class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
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
                class="block w-full pr-10 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
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
          <div>
            <label
              for="confirm-password"
              class="block text-sm font-medium text-gray-700"
            >
              Confirm Password
            </label>
            <div class="mt-1">
              <input
                id="confirm-password"
                name="confirm-password"
                type="password"
                v-model="confirmPassword"
                required
                placeholder=" "
                class="block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
              />
            </div>
          </div>

         
          <div>
            <button
              :disabled="loading"
              type="submit"
              class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white"
              style="background-color: #f97316"
            >
              <span v-if="loading">Registering...</span>
              <span v-else>Register</span>
            </button>
          </div>
        </form>
        <div class="mt-6 text-center text-sm">
          Already have an account?
          <router-link
            to="/login"
            class="font-medium text-orange-600 hover:text-orange-500"
          >
            Login here
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toast-notification";
import RegisterService from "@/services/registerService";

const fullName = ref("");
const email = ref("");
const phone = ref("");
const password = ref("");
const confirmPassword = ref("");

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const passwordVisible = ref(false);

const router = useRouter();
const $toast = useToast();

const togglePasswordVisibility = () => {
  passwordVisible.value = !passwordVisible.value;
};

const handleRegister = async () => {
  errorMessage.value = "";
  successMessage.value = "";

  // ✅ Validation
  if (!fullName.value || !email.value || !phone.value || !password.value || !confirmPassword.value) {
    errorMessage.value = "Please fill in all fields.";
    return;
  }

  // ✅ Phone validation
  const phonePattern = /^(?:254\d{9}|0[17]\d{8})$/;
  if (!phonePattern.test(phone.value)) {
    errorMessage.value =
      "Invalid phone number. Use format 254XXXXXXXXX (12 digits) or 07XXXXXXXX / 01XXXXXXXX (10 digits).";
    return;
  }

  if (password.value !== confirmPassword.value) {
    errorMessage.value = "Passwords do not match.";
    return;
  }

  loading.value = true;

  try {
    const response = await RegisterService.post({
      fullName: fullName.value,
      email: email.value,
      phone: phone.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
    });

    console.log("Registration response:", response);

    // ✅ Accept either Laravel's 201 or success flag
    if (response.status === 201 || response.data?.status === "success") {
      successMessage.value = "Registration successful! Redirecting to login...";

      // Clear form
      fullName.value = "";
      email.value = "";
      phone.value = "";
      password.value = "";
      confirmPassword.value = "";

      // Redirect after 3s
      setTimeout(() => {
        router.push("/admin");
      }, 3000);
    } else {
      errorMessage.value =
        response.data?.message || "Registration failed. Please try again.";
    }
  } catch (error) {
    console.error("Registration error:", error);

    if (error.response) {
      switch (error.response.status) {
        case 422:
          const errors = error.response.data.errors;
          errorMessage.value = Object.values(errors).flat().join(" ");
          break;
        case 500:
          errorMessage.value =
            "Something went wrong on our side. Please try again later.";
          break;
        default:
          errorMessage.value =
            error.response.data.message || "Registration failed. Please try again.";
      }
    } else {
      errorMessage.value = "Network error. Please check your connection.";
    }
  } finally {
    loading.value = false;
  }
};
</script>
