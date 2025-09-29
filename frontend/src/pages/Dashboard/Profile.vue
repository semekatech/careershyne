<template>
  <main class="flex-1 p-8 overflow-y-auto bg-white dark:bg-gray-800 pt-20">
    <div>
      <!-- Tabs -->
      <div class="border-b border-gray-200 dark:border-gray-600">
        <nav aria-label="Tabs" class="-mb-px flex space-x-8">
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

      <!-- PROFILE TAB -->
      <div v-if="activeTab === 'profile'" class="mt-8">
        <div class="bg-card-light dark:bg-card-dark p-8 rounded-lg shadow-md">
          <h2
            class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
          >
            Edit Profile
          </h2>

          <form @submit.prevent="submitProfile" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label
                  class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >Full Name</label
                >
                <input
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm p-2"
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
                <label
                  class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >Email</label
                >
                <input
                  v-model="form.email"
                  type="email"
                  readonly
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-slate-700 shadow-sm sm:text-sm p-2"
                />
              </div>

              <!-- Phone -->
              <div>
                <label
                  class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >Phone</label
                >
                <input
                  v-model="form.phone"
                  type="tel"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm p-2"
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
                <label
                  class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >Industry</label
                >
                <select
                  v-model="form.industry_id"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm p-2"
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
                <label
                  class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >Education Level</label
                >
                <select
                  v-model="form.education_level_id"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm p-2"
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
                <label
                  class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >County</label
                >
                <select
                  v-model="form.county_id"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-background-light dark:bg-slate-800 shadow-sm sm:text-sm p-2"
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

            <div class="flex justify-end pt-4">
              <button
                type="button"
                @click="resetForm"
                class="bg-gray-200 dark:bg-slate-600 text-text-light dark:text-text-dark py-2 px-4 rounded-lg mr-3"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="profileLoading"
                class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors"
              >
                <span v-if="profileLoading">Saving...</span>
                <span v-else>Save Changes</span>
              </button>
            </div>

            <p v-if="profileError" class="text-red-600 text-sm mt-2">
              {{ profileError }}
            </p>
            <p v-if="profileSuccess" class="text-green-600 text-sm mt-2">
              {{ profileSuccess }}
            </p>
          </form>
        </div>
      </div>

      <!-- UPLOADS TAB -->
      <!-- UPLOADS TAB -->
      <div v-if="activeTab === 'uploads'" class="mt-8">
        <div
          class="w-full bg-card-light dark:bg-card-dark rounded-lg shadow-lg p-8 space-y-8"
        >
          <div class="space-y-6">
            <!-- CV Upload -->
            <div>
              <label
                class="block text-lg font-semibold text-text-light dark:text-text-dark"
                for="cv-upload-input"
              >
                CV (PDF)
              </label>
              <p class="text-sm text-subtext-light dark:text-subtext-dark mt-1">
                Your main curriculum vitae.
              </p>
              <div
                class="mt-4 p-6 border-2 border-dashed border-border-light dark:border-border-dark rounded-lg text-center"
              >
                <div
                  class="flex text-sm text-subtext-light dark:text-subtext-dark justify-center items-center"
                >
                  <label
                    class="relative cursor-pointer bg-card-light dark:bg-card-dark rounded-md font-medium text-primary hover:text-orange-500"
                    for="cv-upload-input"
                  >
                    <span>Upload a file</span>
                    <input
                      id="cv-upload-input"
                      type="file"
                      class="sr-only"
                      accept="application/pdf"
                      @change="(e) => handleFileChange(e, 'cv')"
                    />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p
                  class="text-xs text-subtext-light dark:text-subtext-dark mt-1"
                >
                  PDF up to 2MB
                </p>
              </div>
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
            <div>
              <label
                class="block text-lg font-semibold text-text-light dark:text-text-dark"
                for="cover-letter-input"
              >
                Cover Letter (PDF)
              </label>
              <p class="text-sm text-subtext-light dark:text-subtext-dark mt-1">
                A letter to introduce yourself.
              </p>
              <div
                class="mt-4 p-6 border-2 border-dashed border-border-light dark:border-border-dark rounded-lg text-center"
              >
                <div
                  class="flex text-sm text-subtext-light dark:text-subtext-dark justify-center items-center"
                >
                  <label
                    class="relative cursor-pointer bg-card-light dark:bg-card-dark rounded-md font-medium text-primary hover:text-orange-500"
                    for="cover-letter-input"
                  >
                    <span>Upload a file</span>
                    <input
                      id="cover-letter-input"
                      type="file"
                      class="sr-only"
                      accept=".pdf,.doc,.docx"
                      @change="(e) => handleFileChange(e, 'cover_letter')"
                    />
                  </label>
                  <p class="pl-1">or drag and drop</p>
                </div>
                <p
                  class="text-xs text-subtext-light dark:text-subtext-dark mt-1"
                >
                  PDF up to 2MB
                </p>
              </div>
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
          </div>

          <!-- Actions -->
          <div class="flex justify-end pt-4">
            <button
              type="button"
              @click="submitUploads"
              :disabled="uploadsLoading"
              class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-orange-600"
            >
              <span v-if="uploadsLoading">Uploading...</span>
              <span v-else>Update Files</span>
            </button>
          </div>

          <!-- Messages -->
          <p v-if="uploadsError" class="text-red-600 text-sm mt-2">
            {{ uploadsError }}
          </p>
          <p v-if="uploadsSuccess" class="text-green-600 text-sm mt-2">
            {{ uploadsSuccess }}
          </p>
        </div>
      </div>

      <!-- PASSWORD TAB -->
      <div v-if="activeTab === 'password'" class="mt-8">
        <div class="bg-card-light dark:bg-card-dark p-8 rounded-lg shadow-md">
          <h2
            class="text-2xl font-bold mb-6 text-text-light dark:text-text-dark"
          >
            Change Password
          </h2>

          <form @submit.prevent="submitPassword" class="space-y-6">
            <div>
              <label
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >Current Password</label
              >
              <input
                v-model="password.current"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 p-2"
              />
            </div>
            <div>
              <label
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                >New Password</label
              >
              <input
                v-model="password.new"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 p-2"
              />
            </div>
            <div>
              <label
                class="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
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
                class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors"
              >
                Update Password
              </button>
            </div>
            <p v-if="passwordError" class="text-red-600 text-sm mt-2">
              {{ passwordError }}
            </p>
            <p v-if="passwordSuccess" class="text-green-600 text-sm mt-2">
              {{ passwordSuccess }}
            </p>
          </form>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import usersService from "@/services/profileService"; // your service
import OptionsService from "@/services/optionsService"; // your options service

// tabs
const activeTab = ref("profile");

// loading & messages
const profileLoading = ref(false);
const uploadsLoading = ref(false);
const loading = ref(false); // initial fetch loader
const profileError = ref("");
const profileSuccess = ref("");
const uploadsError = ref("");
const uploadsSuccess = ref("");
const validationErrors = ref({});

// form: keep file fields separate from urls
const form = ref({
  id: null,
  name: "",
  email: "",
  phone: "",
  industry_id: "",
  education_level_id: "",
  county_id: "",
  // files (File objects)
  cv: null,
  cover_letter: null,
  photo: null,
  // urls returned by backend
  cv_url: null,
  cover_letter_url: null,
  photo_url: null,
});

// dropdown options
const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

// password state
const password = ref({ current: "", new: "", confirm: "" });
const passwordError = ref("");
const passwordSuccess = ref("");

// Helpers
function handleFileChange(e, field) {
  const file = e.target.files?.[0] ?? null;
  form.value[field] = file;
}

// fetch profile & map backend keys -> front fields
async function fetchProfile() {
  loading.value = true;
  try {
    const { data } = await usersService.fetchProfile();
    // data has keys: id,name,email,phone,industry_id,education_level_id,county_id,cv,cover_letter,photo_url
    form.value = {
      ...form.value,
      id: data.id ?? form.value.id,
      name: data.name ?? form.value.name,
      email: data.email ?? form.value.email,
      phone: data.phone ?? form.value.phone,
      industry_id:
        data.industry_id !== undefined
          ? Number(data.industry_id)
          : form.value.industry_id,
      education_level_id:
        data.education_level_id !== undefined
          ? Number(data.education_level_id)
          : form.value.education_level_id,
      county_id:
        data.county_id !== undefined
          ? Number(data.county_id)
          : form.value.county_id,
      cv_url: data.cv ?? form.value.cv_url,
      cover_letter_url: data.cover_letter ?? form.value.cover_letter_url,
      photo_url: data.photo_url ?? form.value.photo_url,
    };
  } catch (err) {
    console.error("fetchProfile error:", err);
    profileError.value = "Could not load profile data.";
  } finally {
    loading.value = false;
  }
}

// load options (industries, education, counties)
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

// PROFILE submit (text fields only)
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

    const { data } = await usersService.updateProfile(fd);
    // backend should return updated profile shape; refresh
    await fetchProfile();
    profileSuccess.value = "Profile updated successfully.";
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    } else {
      profileError.value = "Failed to update profile.";
      console.error(err);
    }
  } finally {
    profileLoading.value = false;
  }
}

// UPLOADS submit (files only) – using same updateProfile API because it accepts multipart
async function submitUploads() {
  uploadsLoading.value = true;
  uploadsError.value = "";
  uploadsSuccess.value = "";
  validationErrors.value = {};

  try {
    const fd = new FormData();
    if (form.value.cv) fd.append("cv", form.value.cv);
    if (form.value.cover_letter)
      fd.append("cover_letter", form.value.cover_letter);
    if (form.value.photo) fd.append("photo", form.value.photo);

    const { data } = await usersService.updateProfile(fd);

    // map returned urls (backend should return cv, cover_letter, photo_url)
    form.value.cv_url = data.cv ?? data.cv_url ?? form.value.cv_url;
    form.value.cover_letter_url =
      data.cover_letter ?? data.cover_letter_url ?? form.value.cover_letter_url;
    form.value.photo_url = data.photo_url ?? form.value.photo_url;

    uploadsSuccess.value = "Files updated successfully.";
    // clear file inputs (so next selection is fresh)
    form.value.cv = null;
    form.value.cover_letter = null;
    form.value.photo = null;
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors || {};
    } else {
      uploadsError.value = "Failed to upload files.";
      console.error(err);
    }
  } finally {
    uploadsLoading.value = false;
  }
}

// simple password submit (call your password endpoint or usersService method if available)
async function submitPassword() {
  passwordError.value = "";
  passwordSuccess.value = "";
  if (
    !password.value.current ||
    !password.value.new ||
    password.value.new !== password.value.confirm
  ) {
    passwordError.value = "Please ensure passwords are filled and match.";
    return;
  }
  try {
    // replace with your real password change call if available
    // await usersService.changePassword({ current: password.value.current, password: password.value.new, password_confirmation: password.value.confirm });
    passwordSuccess.value = "Password updated (example).";
    password.value.current = password.value.new = password.value.confirm = "";
  } catch (err) {
    passwordError.value = "Failed to update password.";
    console.error(err);
  }
}

function resetForm() {
  // revert form to last fetched profile
  fetchProfile();
  validationErrors.value = {};
  profileError.value = "";
  profileSuccess.value = "";
}

// initial load
onMounted(async () => {
  loading.value = true;
  await loadOptions();
  await fetchProfile();
  loading.value = false;
});
</script>

<style scoped>
/* small helper so inputs match look */
input[type="file"] {
  display: block;
}
</style>
