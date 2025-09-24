<template>
  <div
    class="min-h-screen flex justify-center items-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    style="width: 100%"
  >
    <div class="w-full max-w-2xl">
      <div class="flex flex-col items-center text-center">
        <img src="/logo.png" alt="Careershyne Logo" class="h-20 w-auto mb-4" />
        <h2 class="text-3xl font-bold text-gray-800">
          Hello, <span class="text-orange-500">{{ userName }}</span
          >!
        </h2>
        <p class="mt-2 text-xl text-gray-600">Almost there...</p>
        <p class="mt-1 text-sm text-gray-500">
          Help us tailor your experience by completing your profile.
        </p>
        <p v-if="successMessage" class="mt-4 text-sm text-green-600">
          {{ successMessage }}
        </p>
        <p v-if="errorMessage" class="mt-4 text-sm text-red-600">
          {{ errorMessage }}
        </p>
      </div>

      <form @submit.prevent="handleProfileUpdate" class="mt-8 space-y-6">
        <div>
          <label for="industry" class="block text-sm font-medium text-gray-700">
            Industry
          </label>
          <select
            id="industry"
            name="industry"
            v-model="industry"
            required
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
          >
            <option value="" disabled>Select your industry</option>
            <option
              v-for="option in industryOptions"
              :key="option"
              :value="option"
            >
              {{ option }}
            </option>
          </select>
        </div>

        <div>
          <label
            for="education"
            class="block text-sm font-medium text-gray-700"
          >
            Education Level
          </label>
          <select
            id="education"
            name="education"
            v-model="educationLevel"
            required
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
          >
            <option value="" disabled>
              Select your highest education level
            </option>
            <option
              v-for="option in educationOptions"
              :key="option"
              :value="option"
            >
              {{ option }}
            </option>
          </select>
        </div>

        <div>
          <label for="county" class="block text-sm font-medium text-gray-700">
            County
          </label>
          <select
            id="county"
            name="county"
            v-model="county"
            required
            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
          >
            <option value="" disabled>Select your county</option>
            <option
              v-for="option in countyOptions"
              :key="option"
              :value="option"
            >
              {{ option }}
            </option>
          </select>
        </div>

        <div>
          <label for="cv" class="block text-sm font-medium text-gray-700">
            Attach CV <span class="text-red-500">*</span>
          </label>
          <div
            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
          >
            <div class="space-y-1 text-center">
              <svg
                class="mx-auto h-12 w-12 text-gray-400"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 48 48"
                aria-hidden="true"
              >
                <path
                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-8m12 4h.01M12 24h.01M16 24h.01M20 24h.01M24 24h.01M28 24h.01M32 24h.01M36 24h.01"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              <div class="flex text-sm text-gray-600">
                <label
                  for="cv-upload"
                  class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500"
                >
                  <span>Upload your CV</span>
                  <input
                    id="cv-upload"
                    name="cv-upload"
                    type="file"
                    class="sr-only"
                    required
                    @change="handleCvUpload"
                    accept=".pdf,.doc,.docx"
                  />
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
            </div>
          </div>
        </div>

        <div>
          <label
            for="coverLetter"
            class="block text-sm font-medium text-gray-700"
          >
            Attach Cover Letter <span class="text-gray-400">(Optional)</span>
          </label>
          <div
            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
          >
            <div class="space-y-1 text-center">
              <svg
                class="mx-auto h-12 w-12 text-gray-400"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 48 48"
                aria-hidden="true"
              >
                <path
                  d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-8m12 4h.01M12 24h.01M16 24h.01M20 24h.01M24 24h.01M28 24h.01M32 24h.01M36 24h.01"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              <div class="flex text-sm text-gray-600">
                <label
                  for="cover-letter-upload"
                  class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500"
                >
                  <span>Upload your cover letter</span>
                  <input
                    id="cover-letter-upload"
                    name="cover-letter-upload"
                    type="file"
                    class="sr-only"
                    @change="handleCoverLetterUpload"
                    accept=".pdf,.doc,.docx"
                  />
                </label>
                <p class="pl-1">or drag and drop</p>
              </div>
              <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
            </div>
          </div>
        </div>

        <div>
          <button
            :disabled="loading"
            type="submit"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white"
            style="background-color: #f97316"
          >
            <span v-if="loading">Saving...</span>
            <span v-else>Complete Profile</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import ProfileService from "@/services/profileService";
import OptionsService from "@/services/optionsService";

const auth = useAuthStore();
const router = useRouter();

const userName = ref(auth.user?.fullName || "User");
const industry = ref("");
const educationLevel = ref("");
const county = ref("");
const cvFile = ref(null);
const coverLetterFile = ref(null);

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// Dropdown data
const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

onMounted(async () => {
  try {
    const [industriesRes, educationRes, countiesRes] = await Promise.all([
      OptionsService.getIndustries(),
      OptionsService.getEducationLevels(),
      OptionsService.getCounties(),
    ]);

    industryOptions.value = industriesRes.data;
    educationOptions.value = educationRes.data;
    countyOptions.value = countiesRes.data;
  } catch (error) {
    console.error("Failed to fetch options:", error);
    errorMessage.value = "Failed to load options. Please refresh.";
  }
});
</script>
