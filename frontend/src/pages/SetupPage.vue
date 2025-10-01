<template>
  <div class="min-h-screen flex justify-center items-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl space-y-6">
      
      <!-- Header -->
      <div class="text-center">
        <img src="/logo.png" alt="Logo" class="h-16 w-auto mx-auto mb-4" />
        <h2 class="text-3xl font-bold text-gray-800">
          Hello, <span class="text-orange-500">{{ userName }}</span>!
        </h2>
        <p class="mt-2 text-lg text-gray-600">Almost there...</p>
        <p class="text-sm text-gray-500">Complete your profile to tailor your experience.</p>

        <p v-if="successMessage" class="mt-4 text-green-600 font-medium">{{ successMessage }}</p>
        <p v-if="errorMessage" class="mt-4 text-red-600 font-medium">{{ errorMessage }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleProfileUpdate" class="space-y-6">

        <!-- Industry -->
        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
          <label class="block text-sm font-medium text-gray-700">Industry <span class="text-red-500">*</span></label>
          <select v-model="industry" class="mt-2 block w-full px-4 py-2 border rounded-md focus:ring-orange-400 focus:border-orange-400" :class="errors.industry ? 'border-red-500' : 'border-gray-300'">
            <option value="" disabled>Select your industry</option>
            <option v-for="option in industryOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
          </select>
          <p v-if="errors.industry" class="text-red-500 text-xs mt-1">{{ errors.industry }}</p>
        </div>

        <!-- Education -->
        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
          <label class="block text-sm font-medium text-gray-700">Education Level <span class="text-red-500">*</span></label>
          <select v-model="educationLevel" class="mt-2 block w-full px-4 py-2 border rounded-md focus:ring-orange-400 focus:border-orange-400" :class="errors.educationLevel ? 'border-red-500' : 'border-gray-300'">
            <option value="" disabled>Select your highest education</option>
            <option v-for="option in educationOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
          </select>
          <p v-if="errors.educationLevel" class="text-red-500 text-xs mt-1">{{ errors.educationLevel }}</p>
        </div>

        <!-- County -->
        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
          <label class="block text-sm font-medium text-gray-700">County <span class="text-red-500">*</span></label>
          <select v-model="county" class="mt-2 block w-full px-4 py-2 border rounded-md focus:ring-orange-400 focus:border-orange-400" :class="errors.county ? 'border-red-500' : 'border-gray-300'">
            <option value="" disabled>Select your county</option>
            <option v-for="option in countyOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
          </select>
          <p v-if="errors.county" class="text-red-500 text-xs mt-1">{{ errors.county }}</p>
        </div>

        <!-- CV Upload -->
        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer" @click="triggerCvSelect">
          <label class="block text-sm font-medium text-gray-700">Upload CV <span class="text-red-500">*</span></label>
          <div class="mt-2 flex flex-col items-center justify-center p-4 border-2 border-dashed rounded-md hover:border-orange-400 transition-colors">
            <p class="text-gray-500 text-sm">PDF only, max 5MB</p>
            <p v-if="cvFile" class="text-green-600 text-sm mt-1">{{ cvFile.name }}</p>
          </div>
          <input ref="cvInput" type="file" class="hidden" @change="handleCvUpload" accept=".pdf" />
          <p v-if="errors.cvFile" class="text-red-500 text-xs mt-1">{{ errors.cvFile }}</p>
        </div>

        <!-- Cover Letter Upload -->
        <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer" @click="triggerCoverLetterSelect">
          <label class="block text-sm font-medium text-gray-700">Upload Cover Letter <span class="text-gray-400">(Optional)</span></label>
          <div class="mt-2 flex flex-col items-center justify-center p-4 border-2 border-dashed rounded-md hover:border-orange-400 transition-colors">
            <p class="text-gray-500 text-sm">PDF, DOC, DOCX up to 5MB</p>
            <p v-if="coverLetterFile" class="text-green-600 text-sm mt-1">{{ coverLetterFile.name }}</p>
          </div>
          <input ref="coverLetterInput" type="file" class="hidden" @change="handleCoverLetterUpload" accept=".pdf,.doc,.docx" />
        </div>

        <!-- Submit -->
        <button type="submit" :disabled="loading" class="w-full py-3 px-4 rounded-md text-white font-medium bg-orange-500 hover:bg-orange-600 disabled:opacity-50 transition-colors">
          <span v-if="loading">Saving...</span>
          <span v-else>Complete Profile</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import ProfileService from "@/services/profileService";
import OptionsService from "@/services/optionsService";
import { useRouter } from "vue-router";
const router = useRouter();
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
  if (file && file.type === "application/pdf" && file.size <= 2 * 1024 * 1024) {
    cvFile.value = file;
    errors.value.cvFile = "";
  } else {
    cvFile.value = null;
    errors.value.cvFile = "CV must be a PDF file under 2MB.";
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
  if (!educationLevel.value)
    errors.value.educationLevel = "Education level is required.";
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
  if (coverLetterFile.value)
    formData.append("coverLetterFile", coverLetterFile.value); // match backend field

  const response = await ProfileService.updateProfile(formData);

  if (response.status >= 200 && response.status < 300) {
    successMessage.value = "Profile completed successfully!";
    setTimeout(() => {
      router.push("/dashboard");
    }, 3000);
  } else {
    errorMessage.value =
      response.data?.message || "Profile update failed. Please try again.";
  }
} catch (err) {
  console.error(err);
  errorMessage.value = "Failed to update profile. Try again.";
} finally {
  loading.value = false;
}
};
</script>
