<template>
  <main class="flex-1 p-8 overflow-y-auto bg-white dark:bg-gray-800 pt-5">
    <div>
      <!-- Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-600">
        <nav aria-label="Tabs" class="-mb-px flex space-x-8">
          <a
            href="#"
            @click.prevent="activeTab = 'profile'"
            :class="tabClass('profile')"
          >
            <i class="material-icons mr-2 !text-base">account_circle</i> Profile
            Edit
          </a>
          <a
            href="#"
            @click.prevent="activeTab = 'uploads'"
            :class="tabClass('uploads')"
          >
            <i class="material-icons mr-2 !text-base">cloud_upload</i> My
            Uploads
          </a>
          <a
            href="#"
            @click.prevent="activeTab = 'password'"
            :class="tabClass('password')"
          >
            <i class="material-icons mr-2 !text-base">lock</i> Change Password
          </a>
        </nav>
      </div>

      <!-- Loader for initial data -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg
          class="animate-spin h-10 w-10 text-primary"
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
          />
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
          />
        </svg>
      </div>

      <div v-else>
        <!-- PROFILE TAB -->
        <div v-if="activeTab === 'profile'" class="mt-8">
          <div class="bg-card-light dark:bg-card-dark p-8 ">
            <!-- Alerts -->
            <div
              v-if="profileError"
              class="mb-4 p-3 rounded bg-red-100 text-red-700"
            >
              {{ profileError }}
            </div>
            <div
              v-if="profileSuccess"
              class="mb-4 p-3 rounded bg-green-100 text-green-700"
            >
              {{ profileSuccess }}
            </div>

            <h2
              class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
            >
              Edit Profile
            </h2>

            <form @submit.prevent="submitProfile" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                  <label class="block text-sm font-medium">Full Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm p-2"
                  />
                  <p
                    v-if="validationErrors.name"
                    class="text-red-600 text-sm mt-1"
                  >
                    {{ validationErrors.name[0] }}
                  </p>
                </div>

                <!-- Email (readonly) -->
                <div>
                  <label class="block text-sm font-medium">Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    readonly
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-slate-700 shadow-sm p-2"
                  />
                </div>

                <!-- Phone -->
                <div>
                  <label class="block text-sm font-medium">Phone</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm p-2"
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
                  <label class="block text-sm font-medium">Industry</label>
                  <select
                    v-model="form.industry_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 p-2"
                  >
                    <option value="" disabled>Select industry</option>
                    <option
                      v-for="opt in industryOptions"
                      :key="opt.id"
                      :value="opt.id"
                    >
                      {{ opt.name }}
                    </option>
                  </select>
                  <p
                    v-if="validationErrors.industry_id"
                    class="text-red-600 text-sm mt-1"
                  >
                    {{ validationErrors.industry_id[0] }}
                  </p>
                </div>

                <!-- Education -->
                <div>
                  <label class="block text-sm font-medium"
                    >Education Level</label
                  >
                  <select
                    v-model="form.education_level_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 p-2"
                  >
                    <option value="" disabled>Select education level</option>
                    <option
                      v-for="opt in educationOptions"
                      :key="opt.id"
                      :value="opt.id"
                    >
                      {{ opt.name }}
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
                  <label class="block text-sm font-medium">County</label>
                  <select
                    v-model="form.county_id"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 p-2"
                  >
                    <option value="" disabled>Select county</option>
                    <option
                      v-for="opt in countyOptions"
                      :key="opt.id"
                      :value="opt.id"
                    >
                      {{ opt.name }}
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

              <!-- Actions -->
              <div class="flex justify-end pt-4">
                <button
                  type="button"
                  @click="resetForm"
                  class="bg-gray-200 dark:bg-slate-600 py-2 px-4 rounded-lg mr-3"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="profileLoading"
                  class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600"
                >
                  <span v-if="profileLoading">Saving...</span>
                  <span v-else>Save Changes</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- UPLOADS TAB -->
        <div v-if="activeTab === 'uploads'" class="mt-8">
          <div class="bg-card-light dark:bg-card-dark p-8 ">
            <!-- Alerts -->
            <div
              v-if="uploadsError"
              class="mb-4 p-3 rounded bg-red-100 text-red-700"
            >
              {{ uploadsError }}
            </div>
            <div
              v-if="uploadsSuccess"
              class="mb-4 p-3 rounded bg-green-100 text-green-700"
            >
              {{ uploadsSuccess }}
            </div>

            <h2
              class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
            >
              My Uploads
            </h2>

            <!-- CV Upload -->
            <div class="mb-6">
              <label class="block font-semibold">CV (PDF)</label>
              <div
                class="mt-4 p-6 border-2 border-dashed rounded-lg text-center"
              >
                <label
                  class="cursor-pointer text-primary hover:text-orange-500"
                >
                  <span>Upload a file</span>
                  <input
                    type="file"
                    class="sr-only"
                    accept=".pdf,.doc,.docx"
                    @change="(e) => handleFileChange(e, 'cv')"
                  />
                </label>
                <p class="text-xs mt-1">Max 5MB (PDF/DOC/DOCX)</p>
              </div>
              <!-- Show attached file name -->
              <p v-if="form.cv" class="text-sm text-gray-600 mt-2">
                Selected: {{ form.cv.name }}
              </p>
              <a
                v-if="form.cv_url"
                :href="form.cv_url"
                target="_blank"
                class="mt-3 inline-flex items-center text-sm font-medium text-primary hover:underline"
              >
                <span class="material-icons-sharp text-base mr-1"
                  >visibility</span
                >
                View current CV
              </a>
            </div>

            <!-- Cover Letter Upload -->
            <div class="mb-6">
              <label class="block font-semibold">Cover Letter</label>
              <div
                class="mt-4 p-6 border-2 border-dashed rounded-lg text-center"
              >
                <label
                  class="cursor-pointer text-primary hover:text-orange-500"
                >
                  <span>Upload a file</span>
                  <input
                    type="file"
                    class="sr-only"
                    accept=".pdf,.doc,.docx"
                    @change="(e) => handleFileChange(e, 'cover_letter')"
                  />
                </label>
                <p class="text-xs mt-1">Max 5MB (PDF/DOC/DOCX)</p>
              </div>
              <!-- Show attached file name -->
              <p v-if="form.cover_letter" class="text-sm text-gray-600 mt-2">
                Selected: {{ form.cover_letter.name }}
              </p>
              <a
                v-if="form.cover_letter_url"
                :href="form.cover_letter_url"
                target="_blank"
                class="mt-3 inline-flex items-center text-sm font-medium text-primary hover:underline"
              >
                <span class="material-icons-sharp text-base mr-1"
                  >visibility</span
                >
                View current Cover Letter
              </a>
            </div>

        
            <!-- Actions -->
            <div class="flex justify-end pt-4">
              <button
                type="button"
                @click="submitUploads"
                :disabled="uploadsLoading"
                class="px-6 py-3 rounded-md shadow-sm text-white bg-primary hover:bg-orange-600"
              >
                <span v-if="uploadsLoading">Uploading...</span>
                <span v-else>Update Files</span>
              </button>
            </div>
          </div>
        </div>

        <!-- PASSWORD TAB -->
        <div v-if="activeTab === 'password'" class="mt-8">
          <div class="bg-card-light dark:bg-card-dark p-8 ">
            <!-- Alerts -->
            <div
              v-if="passwordError"
              class="mb-4 p-3 rounded bg-red-100 text-red-700"
            >
              {{ passwordError }}
            </div>
            <div
              v-if="passwordSuccess"
              class="mb-4 p-3 rounded bg-green-100 text-green-700"
            >
              {{ passwordSuccess }}
            </div>

            <h2
              class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
            >
              Change Password
            </h2>

            <form @submit.prevent="submitPassword" class="space-y-6">
              <div>
                <label class="block text-sm font-medium"
                  >Current Password</label
                >
                <input
                  v-model="password.current"
                  type="password"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 p-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium">New Password</label>
                <input
                  v-model="password.new"
                  type="password"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 p-2"
                />
              </div>
              <div>
                <label class="block text-sm font-medium"
                  >Confirm Password</label
                >
                <input
                  v-model="password.confirm"
                  type="password"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 p-2"
                />
              </div>

              <div class="flex justify-end">
                <button
                  type="submit"
                  class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600"
                >
                  Update Password
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import usersService from "@/services/profileService";
import OptionsService from "@/services/optionsService";

const activeTab = ref("profile");

// loaders & messages
const loading = ref(false);
const profileLoading = ref(false);
const uploadsLoading = ref(false);
const profileError = ref("");
const profileSuccess = ref("");
const uploadsError = ref("");
const uploadsSuccess = ref("");
const validationErrors = ref({});
const passwordError = ref("");
const passwordSuccess = ref("");

// form
const form = ref({
  id: null,
  name: "",
  email: "",
  phone: "",
  industry_id: "",
  education_level_id: "",
  county_id: "",
  cv: null,
  cover_letter: null,
  photo: null,
  cv_url: null,
  cover_letter_url: null,
  photo_url: null,
});

// dropdowns
const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

// password
const password = ref({ current: "", new: "", confirm: "" });

// Helpers
function tabClass(tab) {
  return [
    activeTab.value === tab
      ? "border-primary text-primary border-b-2"
      : "border-transparent text-text-secondary-light dark:text-text-secondary-dark hover:text-text-light dark:hover:text-text-dark hover:border-gray-300",
    "whitespace-nowrap py-4 px-1 font-medium text-sm flex items-center",
  ];
}

function handleFileChange(e, field) {
  const file = e.target.files?.[0] ?? null;
  form.value[field] = file;
}

async function fetchProfile() {
  loading.value = true;
  try {
    const { data } = await usersService.fetchProfile();
    form.value = {
      ...form.value,
      id: data.id,
      name: data.name,
      email: data.email,
      phone: data.phone,
      industry_id: Number(data.industry_id) || "",
      education_level_id: Number(data.education_level_id) || "",
      county_id: Number(data.county_id) || "",
      cv_url: data.cv,
      cover_letter_url: data.cover_letter,
      photo_url: data.photo_url,
    };
  } catch (err) {
    profileError.value = "Could not load profile data.";
  } finally {
    loading.value = false;
  }
}

async function loadOptions() {
  try {
    const [industries, education, counties] = await Promise.all([
      OptionsService.getIndustries(),
      OptionsService.getEducationLevels(),
      OptionsService.getCounties(),
    ]);
    industryOptions.value = industries.data ?? industries;
    educationOptions.value = education.data ?? education;
    countyOptions.value = counties.data ?? counties;
  } catch (err) {
    console.error("loadOptions error:", err);
  }
}

// Submit Profile
async function submitProfile() {
  profileLoading.value = true;
  profileError.value = "";
  profileSuccess.value = "";
  validationErrors.value = {};
  try {
    const fd = new FormData();
    fd.append("name", form.value.name ?? "");
    fd.append("phone", form.value.phone ?? "");
    fd.append("industry_id", form.value.industry_id ?? "");
    fd.append("education_level_id", form.value.education_level_id ?? "");
    fd.append("county_id", form.value.county_id ?? "");
    await usersService.updateProfile(fd);
    await fetchProfile();
    profileSuccess.value = "Profile updated successfully.";
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    } else {
      profileError.value = "Failed to update profile.";
    }
  } finally {
    profileLoading.value = false;
  }
}

// Submit Uploads
async function submitUploads() {
  uploadsLoading.value = true;
  uploadsError.value = "";
  uploadsSuccess.value = "";
  try {
    const fd = new FormData();
    if (form.value.cv) fd.append("cv", form.value.cv);
    if (form.value.cover_letter)
      fd.append("cover_letter", form.value.cover_letter);

    await usersService.updateProfile(fd);
    await fetchProfile();
    uploadsSuccess.value = "Files updated successfully.";
    form.value.cv = form.value.cover_letter = form.value.photo = null;
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    } else {
      uploadsError.value = "Failed to upload files.";
    }
  } finally {
    uploadsLoading.value = false;
  }
}

// Submit Password
async function submitPassword() {
  passwordError.value = passwordSuccess.value = "";
  if (
    !password.value.current ||
    !password.value.new ||
    password.value.new !== password.value.confirm
  ) {
    passwordError.value = "Please ensure passwords are filled and match.";
    return;
  }
  try {
    // Example placeholder
    passwordSuccess.value = "Password updated (example).";
    password.value.current = password.value.new = password.value.confirm = "";
  } catch (err) {
    passwordError.value = "Failed to update password.";
  }
}

function resetForm() {
  fetchProfile();
  validationErrors.value = {};
  profileError.value = "";
  profileSuccess.value = "";
}

onMounted(async () => {
  loading.value = true;
  await loadOptions();
  await fetchProfile();
  loading.value = false;
});
</script>
