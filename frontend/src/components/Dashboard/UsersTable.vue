<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Users Table Card -->
    <div
      class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden"
    >
      <!-- Header -->
      <div
        class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between sm:items-center gap-3 bg-orange-50"
      >
        <h2 class="text-xl font-semibold text-gray-800">Manage Users</h2>
        <div v-if="auth.user?.role != 'radio'">
          <button
            class="py-2 px-6 rounded-lg text-white font-semibold transition-all duration-300 hover:opacity-90 shadow-sm"
            :style="{ background: '#fd624e' }"
            @click="openModal"
          >
            <font-awesome-icon :icon="['fas', 'plus']" class="mr-2" />
            Add User
          </button>
        </div>
      </div>

      <!-- Search -->
      <!-- Search + Filters -->
      <div
        class="p-4 bg-white border-b border-gray-100 flex flex-col sm:flex-row flex-wrap gap-3 sm:items-center"
      >
        <input
          v-model="search"
          placeholder="🔍 Search users..."
          class="p-2.5 border rounded-lg w-full sm:w-1/3 focus:ring-2 focus:ring-orange-300 focus:outline-none transition"
        />

        <!-- Paid Filter -->
        <select
          v-model="filterPaid"
          class="p-2.5 border rounded-lg w-full sm:w-auto focus:ring-2 focus:ring-orange-300 focus:outline-none transition"
        >
          <option value="">All Payment Status</option>
          <option value="paid">Paid</option>
          <option value="unpaid">Unpaid</option>
        </select>

        <!-- Registered Filter -->
        <select
          v-model="filterRegistered"
          class="p-2.5 border rounded-lg w-full sm:w-auto focus:ring-2 focus:ring-orange-300 focus:outline-none transition"
        >
          <option value="">All Registration Status</option>
          <option value="registered">Fully Registered</option>
          <option value="unregistered">Not Registered</option>
        </select>
      </div>

      <!-- Users Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm divide-y divide-gray-200">
          <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
              <th class="px-6 py-3 text-left font-semibold">#</th>
              <th class="px-6 py-3 text-left font-semibold">Name</th>
              <th class="px-6 py-3 text-left font-semibold">Type</th>
              <th class="px-6 py-3 text-left font-semibold">Phone</th>
              <th class="px-6 py-3 text-left font-semibold">Date Joined</th>
              <th class="px-6 py-3 text-left font-semibold">Last Login</th>
              <th class="px-6 py-3 text-left font-semibold">
                Fully_Registered?
              </th>
              <th class="px-6 py-3 text-left font-semibold">Paid</th>
              <th class="px-6 py-3 text-left font-semibold">Status</th>
              <th
                v-if="auth.user?.role != 'radio'"
                class="px-6 py-3 text-center font-semibold"
              >
                Actions
              </th>
            </tr>
          </thead>

          <!-- 🔸 Shimmer Loader -->
          <tbody v-if="loadingUsers" class="bg-white divide-y divide-gray-100">
            <tr v-for="n in 6" :key="n" class="animate-pulse">
              <td v-for="i in 8" :key="i" class="px-6 py-4">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              </td>
            </tr>
          </tbody>

          <tbody v-else class="bg-white divide-y divide-gray-100">
            <tr
              v-for="(user, index) in paginatedUsers"
              :key="user.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-6 py-4">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 font-medium text-blue-600">
                {{ user.name }}
              </td>
              <td class="px-6 py-4">
                {{ user.user_type === "registered" ? "Indirect" : "Direct" }}
              </td>
              <td class="px-6 py-4">{{ formatPhone(user.phone) }}</td>
              <td class="px-6 py-4">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4">{{ formatDate(user.last_login_at) }}</td>
              <td class="px-6 py-4">
                <span
                  :class="
                    user.industry_id != null
                      ? 'bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full'
                      : 'bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded-full'
                  "
                >
                  {{ user.industry_id != null ? "Yes" : "No" }}
                </span>
              </td>

              <td class="px-6 py-4">
                <span
                  :class="
                    user.paid === 2
                      ? 'bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full'
                      : 'bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full'
                  "
                >
                  {{ user.paid === 2 ? "Yes" : "No" }}
                </span>
              </td>

              <td class="px-6 py-4">
                <span
                  :class="[
                    'px-3 py-1 rounded-full text-xs font-semibold capitalize',
                    user.status === 'active'
                      ? 'bg-green-100 text-green-700 border border-green-200'
                      : 'bg-red-100 text-red-700 border border-red-200',
                  ]"
                >
                  {{ user.status }}
                </span>
              </td>
              <td
                v-if="auth.user?.role != 'radio'"
                class="px-6 py-4 text-center flex justify-center gap-2"
              >
                <button
                  @click="editUser(user)"
                  class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs transition shadow-sm"
                >
                  Edit
                </button>
                <button
                  @click="impersonateUser(user)"
                  class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-xs transition shadow-sm"
                >
                  Login
                </button>
                <button
                  @click="impersonateUser(user)"
                  class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white rounded-md text-xs transition shadow-sm"
                >
                  Activities
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div
          v-if="!loadingUsers"
          class="flex justify-between items-center p-4 border-t border-gray-100 text-sm text-gray-600"
        >
          <button
            @click="prevPage"
            :disabled="currentPage <= 1"
            class="px-3 py-1 rounded border bg-gray-50 hover:bg-gray-100 disabled:opacity-50"
          >
            ← Previous
          </button>
          <span>Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            @click="nextPage"
            :disabled="currentPage >= totalPages"
            class="px-3 py-1 rounded border bg-gray-50 hover:bg-gray-100 disabled:opacity-50"
          >
            Next →
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-3xl relative animate-fadeIn"
      >
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 transition"
        >
          ✕
        </button>
        <h3 class="text-2xl font-semibold mb-6 text-gray-800 border-b pb-2">
          {{ editMode ? "Edit User" : "Add New User" }}
        </h3>

        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Full Name</label
              >
              <input
                v-model="form.name"
                type="text"
                placeholder="John Doe"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all outline-none"
              />
              <p v-if="validationErrors.name" class="text-red-600 text-xs mt-1">
                {{ validationErrors.name[0] }}
              </p>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Email</label
              >
              <input
                v-model="form.email"
                type="email"
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all outline-none"
              />
              <p
                v-if="validationErrors.email"
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.email[0] }}
              </p>
            </div>

            <!-- Phone -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Phone</label
              >
              <input
                v-model="form.phone"
                type="tel"
                placeholder="+254700000000"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition-all outline-none"
              />
              <p
                v-if="validationErrors.phone"
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.phone[0] }}
              </p>
            </div>

            <!-- Role -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Role</label
              >
              <select
                v-model="form.role"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
              >
                <option disabled value="">Select role</option>
                <option :value="1109">Admin</option>
                <option :value="1098">User</option>
                <option value="radio">Radio</option>
              </select>
              <p v-if="validationErrors.role" class="text-red-600 text-xs mt-1">
                {{ validationErrors.role[0] }}
              </p>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Status</label
              >
              <select
                v-model="form.status"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <p
                v-if="validationErrors.status"
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.status[0] }}
              </p>
            </div>

            <!-- Industry -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Industry</label
              >
              <select
                v-model="form.industry_id"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
              >
                <option disabled value="">Select industry</option>
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
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.industry_id[0] }}
              </p>
            </div>

            <!-- Education Level -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Education Level</label
              >
              <select
                v-model="form.education_level_id"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
              >
                <option disabled value="">Select education level</option>
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
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.education_level_id[0] }}
              </p>
            </div>

            <!-- County -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >County</label
              >
              <select
                v-model="form.county_id"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none"
              >
                <option disabled value="">Select county</option>
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
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.county_id[0] }}
              </p>
            </div>

            <!-- CV Upload -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Attach CV</label
              >
              <label
                class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 transition"
              >
                <span class="text-gray-500 text-sm">
                  Click to upload or drag and drop (PDF)
                </span>
                <input
                  type="file"
                  @change="handleCvUpload"
                  accept=".pdf"
                  class="hidden"
                />
              </label>
              <p v-if="form.cv" class="text-sm text-gray-600 mt-1">
                📎 {{ form.cv.name }}
              </p>
              <p v-if="validationErrors.cv" class="text-red-600 text-xs mt-1">
                {{ validationErrors.cv[0] }}
              </p>
            </div>

            <!-- Cover Letter Upload -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1"
                >Attach Cover Letter (Optional)</label
              >
              <label
                class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-orange-400 transition"
              >
                <span class="text-gray-500 text-sm">
                  Click to upload or drag and drop (.pdf, .doc, .docx)
                </span>
                <input
                  type="file"
                  @change="handleCoverLetterUpload"
                  accept=".pdf,.doc,.docx"
                  class="hidden"
                />
              </label>
              <p v-if="form.cover_letter" class="text-sm text-gray-600 mt-1">
                📎 {{ form.cover_letter.name }}
              </p>
              <p
                v-if="validationErrors.cover_letter"
                class="text-red-600 text-xs mt-1"
              >
                {{ validationErrors.cover_letter[0] }}
              </p>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="w-full bg-orange-500 text-white py-3.5 font-semibold rounded-lg shadow hover:bg-orange-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed mt-2"
          >
            <span v-if="loading" class="animate-pulse">Submitting...</span>
            <span v-else>{{ editMode ? "Update User" : "Submit User" }}</span>
          </button>

          <!-- Feedback Messages -->
          <div
            v-if="errorMessage"
            class="text-red-600 text-sm text-center mt-2"
          >
            {{ errorMessage }}
          </div>
          <div
            v-if="successMessage"
            class="text-green-600 text-sm text-center mt-2"
          >
            {{ successMessage }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import usersService from "@/services/usersService";
import OptionsService from "@/services/optionsService";
import { useToast } from "vue-toast-notification";
import { useRoute, useRouter } from "vue-router";
const filterPaid = ref("");
const filterRegistered = ref("");

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

// 🔸 shimmer loading flag
const loadingUsers = ref(true);

const users = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(100);

const form = ref({
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
});

const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

// Formatting
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

// Fetch users
async function fetchUsers() {
  loadingUsers.value = true;
  try {
    const { data } = await usersService.get();
    users.value = data.users;
  } catch (err) {
    console.error(err);
  } finally {
    loadingUsers.value = false;
  }
}

// Filtered & Paginated
const filteredUsers = computed(() => {
  return users.value.filter((u) => {
    const matchesSearch = [u.name, u.email, u.phone, u.role].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    );

    const matchesPaid =
      !filterPaid.value ||
      (filterPaid.value === "paid" && u.paid === 2) ||
      (filterPaid.value === "unpaid" && u.paid !== 2);

    const matchesRegistered =
      !filterRegistered.value ||
      (filterRegistered.value === "registered" && u.industry_id != null) ||
      (filterRegistered.value === "unregistered" && u.industry_id == null);

    return matchesSearch && matchesPaid && matchesRegistered;
  });
});


const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredUsers.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredUsers.value.length / perPage.value)
);

// Pagination
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// Modal
function openModal() {
  form.value = {
    name: "",
    email: "",
    phone: "",
    role: 1098,
    status: "active",
    industry_id: "",
    education_level_id: "",
    county_id: "",
    cv: null,
    cover_letter: null,
  };
  editMode.value = false;
  validationErrors.value = {};
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
}
function editUser(user) {
  form.value = { ...user };
  editMode.value = true;
  showModal.value = true;
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
      successMessage.value = "User updated successfully!";
    } else {
      await usersService.store(formData);
      successMessage.value = "User added successfully!";
    }

    await fetchUsers();
    setTimeout(() => closeModal(), 1000);
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors;
    } else {
      errorMessage.value = "Failed to submit user.";
    }
  } finally {
    loading.value = false;
  }
}
function formatPhone(phone) {
  if (!phone) return "-";
  phone = phone.toString().replace(/\D/g, "");
  if (phone.length >= 9) {
    phone = "254" + phone.slice(-9);
  }
  return phone;
}

async function impersonateUser(user) {
  if (!confirm(`Login as ${user.name}?`)) return;

  try {
    const { data } = await usersService.impersonate(user.id);
    auth.setToken(data.access_token);
    localStorage.setItem("token", data.access_token);
    auth.setUser(data.user);
    await auth.refreshUser();
    router.push(
      data.redirect === "profile-setup" ? "/profile-setup" : "/dashboard"
    );
    $toast.success(`You are now logged in as ${data.user.name}`);
  } catch (err) {
    console.error(err);
    $toast.error("Failed to impersonate user");
  }
}

// Delete User
async function deleteUser(userId) {
  if (!confirm("Are you sure you want to delete this user?")) return;
  try {
    await usersService.delete(userId);
    await fetchUsers();
    $toast.success("User deleted successfully");
  } catch (err) {
    console.error(err);
    $toast.error("Failed to delete user");
  }
}

// Load options
onMounted(async () => {
  await fetchUsers();
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
  } catch (err) {
    console.error(err);
  }
});
</script>

<style scoped>
body {
  overflow-x: hidden;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-in-out;
}
</style>
