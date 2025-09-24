<template>
  <div class="min-h-screen flex justify-center items-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8" style="width: 100%">
    <div class="w-full max-w-2xl">
      
      <!-- Header -->
      <div class="flex flex-col items-center text-center">
        <img src="/logo.png" alt="Careershyne Logo" class="h-20 w-auto mb-4" />
        <h2 class="text-3xl font-bold text-gray-800">
          Hello, <span class="text-orange-500">{{ userName }}</span>!
        </h2>
        <p class="mt-2 text-xl text-gray-600">Almost there...</p>
        <p class="mt-1 text-sm text-gray-500">
          Help us tailor your experience by completing your profile.
        </p>

        <!-- Global messages -->
        <p v-if="successMessage" class="mt-4 text-sm text-green-600">{{ successMessage }}</p>
        <p v-if="errorMessage" class="mt-4 text-sm text-red-600">{{ errorMessage }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleProfileUpdate" class="mt-8 space-y-6">
        
        <!-- Industry -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Industry <span class="text-red-500">*</span></label>
          <select v-model="industry" class="mt-1 block w-full px-4 py-2 border rounded-md shadow-sm sm:text-sm" :class="errors.industry ? 'border-red-500' : 'border-gray-300'">
            <option value="" disabled>Select your industry</option>
            <option v-for="option in industryOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
          </select>
          <p v-if="errors.industry" class="text-red-500 text-xs mt-1">{{ errors.industry }}</p>
        </div>

        <!-- Education -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Education Level <span class="text-red-500">*</span></label>
          <select v-model="educationLevel" class="mt-1 block w-full px-4 py-2 border rounded-md shadow-sm sm:text-sm" :class="errors.educationLevel ? 'border-red-500' : 'border-gray-300'">
            <option value="" disabled>Select your highest education level</option>
            <option v-for="option in educationOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
          </select>
          <p v-if="errors.educationLevel" class="text-red-500 text-xs mt-1">{{ errors.educationLevel }}</p>
        </div>

        <!-- County -->
        <div>
          <label class="block text-sm font-medium text-gray-700">County <span class="text-red-500">*</span></label>
          <select v-model="county" class="mt-1 block w-full px-4 py-2 border rounded-md shadow-sm sm:text-sm" :class="errors.county ? 'border-red-500' : 'border-gray-300'">
            <option value="" disabled>Select your county</option>
            <option v-for="option in countyOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
          </select>
          <p v-if="errors.county" class="text-red-500 text-xs mt-1">{{ errors.county }}</p>
        </div>

        <!-- CV Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Attach CV <span class="text-red-500">*</span></label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md cursor-pointer" :class="errors.cvFile ? 'border-red-500' : 'border-gray-300'" @click="triggerCvSelect">
            <div class="space-y-1 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-8m12 4h.01M12 24h.01M16 24h.01M20 24h.01M24 24h.01M28 24h.01M32 24h.01M36 24h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <p class="text-sm text-gray-600">Upload your CV</p>
              <p class="text-xs text-gray-500">PDF only, up to 5MB</p>
            </div>
            <input ref="cvInput" type="file" class="hidden" @change="handleCvUpload" accept=".pdf" />
          </div>
          <p v-if="cvFile" class="text-xs text-green-600 mt-1">{{ cvFile.name }}</p>
          <p v-if="errors.cvFile" class="text-red-500 text-xs mt-1">{{ errors.cvFile }}</p>
        </div>

        <!-- Cover Letter Upload -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Attach Cover Letter <span class="text-gray-400">(Optional)</span></label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md cursor-pointer" @click="triggerCoverLetterSelect">
            <div class="space-y-1 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-8m12 4h.01M12 24h.01M16 24h.01M20 24h.01M24 24h.01M28 24h.01M32 24h.01M36 24h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <p class="text-sm text-gray-600">Upload your cover letter</p>
              <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 5MB</p>
            </div>
            <input ref="coverLetterInput" type="file" class="hidden" @change="handleCoverLetterUpload" accept=".pdf,.doc,.docx"/>
          </div>
          <p v-if="coverLetterFile" class="text-xs text-green-600 mt-1">{{ coverLetterFile.name }}</p>
        </div>

        <!-- Submit -->
        <div>
          <button :disabled="loading" type="submit" class="w-full flex justify-center py-3 px-4 border rounded-md shadow-sm text-base font-medium text-white disabled:opacity-50" style="background-color: #f97316">
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
import { useAuthStore } from "@/stores/auth";
import ProfileService from "@/services/profileService";
import OptionsService from "@/services/optionsService";
const auth = useAuthStore();
const userName = ref(auth.user?.name || "User");
const industry = ref("");
const educationLevel = ref("");
const county = ref("");
const cvFile = ref(null);
const coverLetterFile = ref(null);

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const errors = ref({});

// Dropdown options
const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

// Load options
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
  } catch (err) {
    console.error(err);
    errorMessage.value = "Failed to load options. Please refresh.";
  }
});

// File refs
const cvInput = ref(null);
const coverLetterInput = ref(null);
const triggerCvSelect = () => cvInput.value.click();
const triggerCoverLetterSelect = () => coverLetterInput.value.click();

// File handlers
const handleCvUpload = (e) => {
  const file = e.target.files[0];
  if (file && file.type === "application/pdf" && file.size <= 5 * 1024 * 1024) {
    cvFile.value = file;
    errors.value.cvFile = "";
  } else {
    cvFile.value = null;
    errors.value.cvFile = "CV must be a PDF file under 5MB.";
  }
};
const handleCoverLetterUpload = (e) => {
  const file = e.target.files[0];
  if (file && file.size <= 5 * 1024 * 1024) coverLetterFile.value = file;
  else {
    coverLetterFile.value = null;
    errorMessage.value = "Cover letter must be under 5MB.";
  }
};

// Form validation
const validateForm = () => {
  errors.value = {};
  if (!industry.value) errors.value.industry = "Industry is required.";
  if (!educationLevel.value) errors.value.educationLevel = "Education level is required.";
  if (!county.value) errors.value.county = "County is required.";
  if (!cvFile.value) errors.value.cvFile = "CV is required.";
  return Object.keys(errors.value).length === 0;
};

// Submit
const handleProfileUpdate = async () => {
  if (!validateForm()) return;

  loading.value = true;
  errorMessage.value = "";
  successMessage.value = "";

  try {
    const formData = new FormData();
    formData.append("industry_id", industry.value);
    formData.append("education_level_id", educationLevel.value);
    formData.append("county_id", county.value);
    formData.append("cv", cvFile.value);
    if (coverLetterFile.value) formData.append("cover_letter", coverLetterFile.value);

    await ProfileService.updateProfile(formData);
    successMessage.value = "Profile completed successfully!";
  } catch (err) {
    console.error(err);
    errorMessage.value = "Failed to update profile. Try again.";
  } finally {
    loading.value = false;
  }
};
</script>
