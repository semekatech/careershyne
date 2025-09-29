<template>
  <main class="flex-1 p-8 overflow-y-auto bg-white dark:bg-gray-800 pt-20">
    <div>
      <!-- Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-600">
        <nav aria-label="Tabs" class="-mb-px flex space-x-8">
          <!-- Profile Edit Tab -->
          <a
            href="#"
            @click.prevent="activeTab = 'profile'"
            :class="[
              activeTab === 'profile'
                ? 'border-primary text-primary border-b-2'
                : 'border-transparent text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark hover:border-gray-300 dark:hover:border-gray-500',
              'whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center',
            ]"
          >
            <i class="material-icons mr-2 !text-base">account_circle</i>
            Profile Edit
          </a>

          <!-- My Uploads Tab -->
          <a
            href="#"
            @click.prevent="activeTab = 'uploads'"
            :class="[
              activeTab === 'uploads'
                ? 'border-primary text-primary border-b-2'
                : 'border-transparent text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark hover:border-gray-300 dark:hover:border-gray-500',
              'whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center',
            ]"
          >
            <i class="material-icons mr-2 !text-base">cloud_upload</i>
            My Uploads
          </a>

          <!-- Change Password Tab -->
          <a
            href="#"
            @click.prevent="activeTab = 'password'"
            :class="[
              activeTab === 'password'
                ? 'border-primary text-primary border-b-2'
                : 'border-transparent text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark hover:border-gray-300 dark:hover:border-gray-500',
              'whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center',
            ]"
          >
            <i class="material-icons mr-2 !text-base">lock</i>
            Change Password
          </a>
        </nav>
      </div>

      <!-- Profile Form -->
      <div v-if="activeTab === 'profile'" class="mt-8">
        <div class="bg-card-light dark:bg-card-dark p-8 rounded-lg shadow-md">
          <h2
            class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
          >
            Edit Profile
          </h2>

          <form @submit.prevent="submitForm" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Full Name -->
              <div>
                <label class="block text-gray-700 font-medium">Full Name</label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="John Doe"
                  class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
                />
                <p
                  v-if="validationErrors.name"
                  class="text-red-600 text-sm mt-1"
                >
                  {{ validationErrors.name[0] }}
                </p>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-gray-700 font-medium">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="you@example.com"
                  class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
                />
                <p
                  v-if="validationErrors.email"
                  class="text-red-600 text-sm mt-1"
                >
                  {{ validationErrors.email[0] }}
                </p>
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-gray-700 font-medium">Phone</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  placeholder="+254700000000"
                  class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
                />
                <p
                  v-if="validationErrors.phone"
                  class="text-red-600 text-sm mt-1"
                >
                  {{ validationErrors.phone[0] }}
                </p>
              </div>

            
          
              <!-- Industry -->
              <div>
                <label class="block text-gray-700 font-medium">Industry</label>
                <select
                  v-model="form.industry_id"
                  class="w-full px-3 py-2 border rounded-lg"
                >
                  <option value="" disabled>Select industry</option>
                  <option
                    v-for="option in industryOptions"
                    :key="option.id"
                    :value="option.id"
                  >
                    {{ option.name }}
                  </option>
                </select>
                <p
                  v-if="validationErrors.industry_id"
                  class="text-red-600 text-sm mt-1"
                >
                  {{ validationErrors.industry_id[0] }}
                </p>
              </div>

              <!-- Education Level -->
              <div>
                <label class="block text-gray-700 font-medium"
                  >Education Level</label
                >
                <select
                  v-model="form.education_level_id"
                  class="w-full px-3 py-2 border rounded-lg"
                >
                  <option value="" disabled>Select education level</option>
                  <option
                    v-for="option in educationOptions"
                    :key="option.id"
                    :value="option.id"
                  >
                    {{ option.name }}
                  </option>
                </select>
                <p
                  v-if="validationErrors.education_level_id"
                  class="text-red-600 text-sm mt-1"
                >
                  {{ validationErrors.education_level_id[0] }}
                </p>
              </div>

              <!-- County -->
              <div>
                <label class="block text-gray-700 font-medium">County</label>
                <select
                  v-model="form.county_id"
                  class="w-full px-3 py-2 border rounded-lg"
                >
                  <option value="" disabled>Select county</option>
                  <option
                    v-for="option in countyOptions"
                    :key="option.id"
                    :value="option.id"
                  >
                    {{ option.name }}
                  </option>
                </select>
                <p
                  v-if="validationErrors.county_id"
                  class="text-red-600 text-sm mt-1"
                >
                  {{ validationErrors.county_id[0] }}
                </p>
              </div>

             

            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="loading || !isFormValid"
              class="w-full bg-[#ff9c30] text-white py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-600 mt-4"
            >
              <span v-if="loading">Submitting...</span>
              <span v-else>{{ editMode ? "Update User" : "Submit User" }}</span>
            </button>

            <!-- General Error Message -->
            <p v-if="errorMessage" class="text-red-600 text-sm mt-2">
              {{ errorMessage }}
            </p>
            <p v-if="successMessage" class="text-green-600 text-sm mt-2">
              {{ successMessage }}
            </p>
          </form>
        </div>
      </div>

      <!-- Uploads Tab -->
      <div v-if="activeTab === 'uploads'" class="mt-8">
        <div class="bg-card-light dark:bg-card-dark p-8 rounded-lg shadow-md">
          <h2
            class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
          >
            My Uploads
          </h2>

          <form
            action="#"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
          >
            <!-- CV Upload -->
            <div>
              <label
                for="cv"
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >Upload CV (PDF only)</label
              >
              <input
                id="cv"
                name="cv"
                type="file"
                accept=".pdf"
                class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-primary file:text-white hover:file:bg-orange-600"
              />
            </div>

            <!-- Cover Letter Upload -->
            <div>
              <label
                for="cover_letter"
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >Cover Letter (PDF/DOC/DOCX)</label
              >
              <input
                id="cover_letter"
                name="cover_letter"
                type="file"
                accept=".pdf,.doc,.docx"
                class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-gray-500 file:text-white hover:file:bg-gray-600"
              />
            </div>

            <!-- Actions -->
            <div class="flex justify-end pt-4">
              <button
                type="submit"
                class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors"
              >
                Upload Files
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Change Password Tab -->
      <div v-if="activeTab === 'password'" class="mt-8">
        <div class="bg-card-light dark:bg-card-dark p-8 rounded-lg shadow-md">
          <h2
            class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
          >
            Change Password
          </h2>

          <form action="#" method="POST" class="space-y-6">
            <div>
              <label
                for="current_password"
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >Current Password</label
              >
              <input
                id="current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm focus:border-primary focus:ring-primary"
              />
            </div>
            <div>
              <label
                for="new_password"
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >New Password</label
              >
              <input
                id="new_password"
                name="new_password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm focus:border-primary focus:ring-primary"
              />
            </div>
            <div>
              <label
                for="confirm_password"
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >Confirm Password</label
              >
              <input
                id="confirm_password"
                name="confirm_password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm focus:border-primary focus:ring-primary"
              />
            </div>
            <div class="flex justify-end pt-4">
              <button
                type="submit"
                class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors"
              >
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import usersService from "@/services/profileService"; // you already set up
import OptionsService from "@/services/optionsService";
import { useToast } from "vue-toast-notification";
import { useRoute, useRouter } from "vue-router";

const activeTab = ref("profile"); // default tab
const auth = useAuthStore();
const $toast = useToast();
const route = useRoute();
const router = useRouter();

const showModal = ref(false);
const editMode = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const validationErrors = ref({});

// Profile form
const form = ref({
  id: null,
  name: "",
  email: "",
  phone: "",
  role: "user",
  status: "active",
  industry_id: "",
  education_level_id: "",
  county_id: "",
  cv: null,
  cover_letter: null,
  photo_url: null, // in case you want to preview photo
});

// Options
const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

// Fetch profile info
async function fetchProfile() {
  try {
    const { data } = await usersService.fetchProfile();
    form.value = {
      ...form.value, // keep defaults
      ...data,       // overwrite with backend values
    };
  } catch (err) {
    console.error("Failed to fetch profile:", err);
    errorMessage.value = "Could not load profile data.";
  }
}

// File Uploads
function handleCvUpload(e) {
  form.value.cv = e.target.files[0] ?? null;
}
function handleCoverLetterUpload(e) {
  form.value.cover_letter = e.target.files[0] ?? null;
}


// Form Validation
const isFormValid = computed(
  () =>
    form.value.name &&
    form.value.email &&
    form.value.phone &&
    form.value.role &&
    form.value.status
);

// Submit
async function submitForm() {
  loading.value = true;
  successMessage.value = "";
  errorMessage.value = "";
  validationErrors.value = {};

  const phonePattern = /^(?:254\d{9}|0[17]\d{8})$/;

  if (!phonePattern.test(form.value.phone)) {
    errorMessage.value =
      "Invalid phone number. Use format 254XXXXXXXXX (12 digits) or 07XXXXXXXX / 01XXXXXXXX (10 digits).";
    loading.value = false;
    return;
  }

  try {
    const formData = new FormData();
    Object.keys(form.value).forEach((key) => {
      if (form.value[key] !== null) formData.append(key, form.value[key]);
    });

    if (editMode.value) {
      await usersService.update(form.value.id, formData);
      successMessage.value = "Profile updated successfully!";
    } else {
      await usersService.updateProfile(formData); // use your profile-setup API
      successMessage.value = "Profile saved successfully!";
    }
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors;
    } else {
      errorMessage.value = "Failed to submit profile.";
    }
  } finally {
    loading.value = false;
  }
}

// Load options + profile
onMounted(async () => {
  await auth.refreshUser();
  try {
    const [industries, education, counties] = await Promise.all([
      OptionsService.getIndustries(),
      OptionsService.getEducationLevels(),
      OptionsService.getCounties(),
    ]);
    industryOptions.value = industries.data;
    educationOptions.value = education.data;
    countyOptions.value = counties.data;

    // Fetch user profile after options
    await fetchProfile();
  } catch (err) {
    console.error(err);
  }
});
</script>

