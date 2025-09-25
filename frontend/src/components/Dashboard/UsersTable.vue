<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Users Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Header -->
      <div
        class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
      >
        <h2 class="text-lg font-semibold text-gray-800">Manage Users</h2>
        <div v-if="auth.user?.role != 'radio'">
          <button
            class="py-2 px-6 text-white rounded font-medium transition duration-500"
            :style="{ background: '#fd624e' }"
            @click="openModal"
          >
            <font-awesome-icon :icon="['fas', 'plus']" /> Add User
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="p-4">
        <input
          v-model="search"
          placeholder="Search users..."
          class="p-2 border rounded w-full md:w-1/2"
        />
      </div>

      <!-- Users Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                #
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Name
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Email
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Phone
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Date Joined
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Status
              </th>
              <th
                v-if="auth.user?.role != 'radio'"
                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase"
              >
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(user, index) in paginatedUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-blue-500">
                {{ user.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    user.status === 'active'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800',
                  ]"
                >
                  {{ user.status }}
                </span>
              </td>
              <td
                v-if="auth.user?.role != 'radio'"
                class="px-6 py-4 whitespace-nowrap text-center flex justify-center gap-2"
              >
                <button
                  @click="editUser(user)"
                  class="px-2 py-1 bg-blue-600 text-white rounded text-xs"
                >
                  Edit
                </button>
                <button
                  @click="deleteUser(user.id)"
                  class="px-2 py-1 bg-red-600 text-white rounded text-xs"
                >
                  Disable
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-between p-4">
          <button @click="prevPage" :disabled="currentPage <= 1">
            Previous
          </button>
          <span>{{ currentPage }} / {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage >= totalPages">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative">
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-2 right-2 text-gray-600 hover:text-gray-900"
        >
          ✕
        </button>
        <h3 class="text-xl font-semibold mb-4">
          {{ editMode ? "Edit User" : "Add New User" }}
        </h3>

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
              <p v-if="validationErrors.name" class="text-red-600 text-sm mt-1">
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

            <!-- Role -->
            <div>
              <label class="block text-gray-700 font-medium">Role</label>
              <select
                v-model="form.role"
                class="w-full px-3 py-2 border rounded-lg"
              >
                <option :value="1109">Admin</option>
                <option :value="1098" selected>User</option>
                <option value="radio">Radio</option>
              </select>

              <p v-if="validationErrors.role" class="text-red-600 text-sm mt-1">
                {{ validationErrors.role[0] }}
              </p>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-gray-700 font-medium">Status</label>
              <select
                v-model="form.status"
                class="w-full px-3 py-2 border rounded-lg"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
              <p
                v-if="validationErrors.status"
                class="text-red-600 text-sm mt-1"
              >
                {{ validationErrors.status[0] }}
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

            <!-- CV Upload -->
            <div>
              <label class="block text-gray-700 font-medium">Attach CV</label>
              <input type="file" @change="handleCvUpload" accept=".pdf" />
              <p v-if="form.cv">{{ form.cv.name }}</p>
              <p v-if="validationErrors.cv" class="text-red-600 text-sm mt-1">
                {{ validationErrors.cv[0] }}
              </p>
            </div>

            <!-- Cover Letter Upload -->
            <div>
              <label class="block text-gray-700 font-medium"
                >Attach Cover Letter (Optional)</label
              >
              <input
                type="file"
                @change="handleCoverLetterUpload"
                accept=".pdf,.doc,.docx"
              />
              <p v-if="form.cover_letter">{{ form.cover_letter.name }}</p>
              <p
                v-if="validationErrors.cover_letter"
                class="text-red-600 text-sm mt-1"
              >
                {{ validationErrors.cover_letter[0] }}
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import usersService from "@/services/usersService";
import OptionsService from "@/services/optionsService";
import { useToast } from "vue-toast-notification";

const auth = useAuthStore();
const $toast = useToast();

const showModal = ref(false);
const editMode = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const validationErrors = ref({});

const users = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

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
  try {
    const { data } = await usersService.get();
    users.value = data.users;
  } catch (err) {
    console.error(err);
  }
}

// Filtered & Paginated
const filteredUsers = computed(() => {
  if (!search.value) return users.value;
  return users.value.filter((u) =>
    [u.name, u.email, u.phone, u.role].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
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
    loading.value = false; // stop loading
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
</style>
