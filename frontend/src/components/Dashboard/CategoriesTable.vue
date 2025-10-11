<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Categories Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">Manage Categories</h2>
        <div v-if="auth.user?.role != 'radio'">
          <button
            class="py-2 px-6 text-white rounded font-medium transition duration-500"
            :style="{ background: '#fd624e' }"
            @click="openModal"
          >
            <font-awesome-icon :icon="['fas', 'plus']" /> Add Category
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="p-4">
        <input
          v-model="search"
          placeholder="Search categories..."
          class="p-2 border rounded w-full md:w-1/2"
        />
      </div>

      <!-- Categories Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                Category Name
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                Jobs
              </th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                Subscribers
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
            <tr v-for="(category, index) in paginatedCategories" :key="category.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-blue-500">
                {{ category.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ category.jobs_count ?? 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ category.subscribers_count ?? 0 }}
              </td>
              <td
                v-if="auth.user?.role != 'radio'"
                class="px-6 py-4 whitespace-nowrap text-center flex justify-center gap-2"
              >
                <button
                  @click="editCategory(category)"
                  class="px-2 py-1 bg-blue-600 text-white rounded text-xs"
                >
                  Edit
                </button>
                <button
                  @click="deleteCategory(category.id)"
                  class="px-2 py-1 bg-red-600 text-white rounded text-xs"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex justify-between p-4">
          <button @click="prevPage" :disabled="currentPage <= 1">Previous</button>
          <span>{{ currentPage }} / {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage >= totalPages">Next</button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-2 right-2 text-gray-600 hover:text-gray-900"
        >
          ✕
        </button>
        <h3 class="text-xl font-semibold mb-4">
          {{ editMode ? "Edit Category" : "Add New Category" }}
        </h3>

        <form @submit.prevent="submitForm" class="space-y-5">
          <!-- Category Name -->
          <div>
            <label class="block text-gray-700 font-medium">Category Name</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="e.g. Software Development"
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
            />
            <p v-if="validationErrors.name" class="text-red-600 text-sm mt-1">
              {{ validationErrors.name[0] }}
            </p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || !form.name"
            class="w-full bg-[#ff9c30] text-white py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-600 mt-4"
          >
            <span v-if="loading">Submitting...</span>
            <span v-else>{{ editMode ? "Update Category" : "Submit Category" }}</span>
          </button>

          <!-- General Messages -->
          <p v-if="errorMessage" class="text-red-600 text-sm mt-2">{{ errorMessage }}</p>
          <p v-if="successMessage" class="text-green-600 text-sm mt-2">{{ successMessage }}</p>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import categoriesService from "@/services/categoriesService";
import { useToast } from "vue-toast-notification";

const auth = useAuthStore();
const $toast = useToast();

const showModal = ref(false);
const editMode = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
const validationErrors = ref({});

const categories = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

const form = ref({
  id: null,
  name: "",
});

// Fetch categories
async function fetchCategories() {
  try {
    const { data } = await categoriesService.getIndustries();
    categories.value = data;
  } catch (err) {
    console.error("Failed to fetch categories:", err);
  }
}



// Filtered & Paginated
const filteredCategories = computed(() => {
  if (!search.value) return categories.value;
  return categories.value.filter((c) =>
    c.name.toLowerCase().includes(search.value.toLowerCase())
  );
});

const paginatedCategories = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredCategories.value.slice(start, start + perPage.value);
});

const totalPages = computed(() => Math.ceil(filteredCategories.value.length / perPage.value));

// Pagination
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

// Modal Controls
function openModal() {
  form.value = { id: null, name: "" };
  editMode.value = false;
  validationErrors.value = {};
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
}

function editCategory(category) {
  form.value = { ...category };
  editMode.value = true;
  showModal.value = true;
}

// Submit Category
async function submitForm() {
  loading.value = true;
  successMessage.value = "";
  errorMessage.value = "";
  validationErrors.value = {};

  try {
    if (editMode.value) {
      await categoriesService.update(form.value.id, form.value);
      successMessage.value = "Category updated successfully!";
    } else {
      await categoriesService.store(form.value);
      successMessage.value = "Category added successfully!";
    }

    await fetchCategories();
    setTimeout(() => closeModal(), 1000);
  } catch (err) {
    if (err.response?.status === 422) {
      validationErrors.value = err.response.data.errors;
    } else {
      errorMessage.value = "Failed to submit category.";
    }
  } finally {
    loading.value = false;
  }
}

// Delete Category
async function deleteCategory(categoryId) {
  if (!confirm("Are you sure you want to delete this category?")) return;
  try {
    await categoriesService.delete(categoryId);
    await fetchCategories();
    $toast.success("Category deleted successfully");
  } catch (err) {
    console.error(err);
    $toast.error("Failed to delete category");
  }
}

onMounted(fetchCategories);
</script>

<style scoped>
body {
  overflow-x: hidden;
}
</style>
