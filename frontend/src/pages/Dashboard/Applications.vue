<template >
  <div
    class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8"
    style="background: #fffbf7"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
      <a href="#" class="flex items-center justify-center">
        <img src="/logo.png" alt="Logo" class="h-16 w-auto" />

       
      </a>
      <h2
        class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900"
      >
        Create a new account
      </h2>
    </div>

    <!-- <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md"> -->
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-3xl" style="width: 450px">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form @submit.prevent="submitForm" novalidate>
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700"
              >Full Name</label
            >
            <input
              id="name"
              v-model="form.name"
              name="name"
              type="text"
              required
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <p v-if="errors.name" class="text-red-600 mt-1 text-sm">
              {{ errors.name[0] }}
            </p>
          </div>

          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700"
              >Email address</label
            >
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <p v-if="errors.email" class="text-red-600 mt-1 text-sm">
              {{ errors.email[0] }}
            </p>
          </div>

          <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700"
              >Phone Number</label
            >
            <input
              id="phone"
              v-model="form.phone"
              name="phone"
              type="text"
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <p v-if="errors.phone" class="text-red-600 mt-1 text-sm">
              {{ errors.phone[0] }}
            </p>
          </div>
          <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">
              Register as
            </label>
            <select
              id="role"
              v-model="form.role"
              name="role"
              required
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
              <option value="" disabled>Select role</option>
              <option value="promoter">Promoter</option>
              <option value="brand">Brand</option>
            </select>
            <p v-if="errors.role" class="text-red-600 mt-1 text-sm">
              {{ errors.role[0] }}
            </p>
          </div>

          <div class="mb-4">
            <label
              for="password"
              class="block text-sm font-medium text-gray-700"
              >Password</label
            >
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <p v-if="errors.password" class="text-red-600 mt-1 text-sm">
              {{ errors.password[0] }}
            </p>
          </div>

          <div class="mb-6">
            <label
              for="password_confirmation"
              class="block text-sm font-medium text-gray-700"
              >Confirm Password</label
            >
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
          </div>

          <button
            type="submit"
            class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            :disabled="loading"
            style="background: #fd624e"
          >
            <span v-if="!loading">Create account</span>
            <span v-else>
              <svg
                class="animate-spin h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                ></path>
              </svg>
              <span class="ml-2">Processing...</span>
            </span>
          </button>
          <router-link
            to="/login"
            class="block mt-4 text-center font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:underline transition ease-in-out duration-150"
          >
            Already registered? login
          </router-link>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
const loading = ref(false);
import { ref } from "vue";
import RegisterService from "@/services/registerService";

const form = ref({
  name: "",
  email: "",
  phone: "",
  password: "",
  roloe:"",
  password_confirmation: "",
});

const errors = ref({});

const submitForm = async () => {
  errors.value = {};
  loading.value = true; // Start loading
  try {
    const response = await RegisterService.post(form.value);
    window.location.href = "/login?registered=1";
    // Reset form
    Object.keys(form.value).forEach((key) => (form.value[key] = ""));
  } catch (error) {
    console.error("API error:", error);
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      alert("An error occurred. Please try again.");
    }
  } finally {
    loading.value = false; // End loading
  }
};
</script>
