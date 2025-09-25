<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
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

      <!-- Users Table -->
      <div class="overflow-x-auto">
        <div class="p-4">
          <input
            v-model="search"
            placeholder="Search users..."
            class="p-2 border rounded"
          />
        </div>

        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Phone</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th v-if="auth.user?.role != 'radio'" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(user, index) in paginatedUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-6 py-4 whitespace-nowrap" style="color: dodgerblue">{{ user.fullname }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.phone }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.role }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ user.status }}
                </span>
              </td>
              <td v-if="auth.user?.role != 'radio'" class="px-6 py-4 whitespace-nowrap text-center flex justify-center gap-2">
                <button @click="editUser(user)" class="px-2 py-1 bg-blue-600 text-white rounded text-xs">Edit</button>
                <button @click="deleteUser(user.id)" class="px-2 py-1 bg-red-600 text-white rounded text-xs">Delete</button>
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

    <!-- 🔹 Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
        <!-- Close Button -->
        <button @click="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">✕</button>

        <h3 class="text-xl font-semibold mb-4">{{ editMode ? 'Edit User' : 'Add New User' }}</h3>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-5">
          <div>
            <label class="block text-gray-700 font-medium">Full Name</label>
            <input v-model="form.fullname" type="text" placeholder="John Doe" required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none" />
          </div>

          <div>
            <label class="block text-gray-700 font-medium">Email</label>
            <input v-model="form.email" type="email" placeholder="you@example.com" required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none" />
          </div>

          <div>
            <label class="block text-gray-700 font-medium">Phone</label>
            <input v-model="form.phone" type="tel" placeholder="+254700000000" required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none" />
          </div>

          <div>
            <label class="block text-gray-700 font-medium">Role</label>
            <select v-model="form.role" class="w-full px-3 py-2 border rounded-lg">
              <option value="admin">Admin</option>
              <option value="user">User</option>
              <option value="radio">Radio</option>
            </select>
          </div>

          <div>
            <label class="block text-gray-700 font-medium">Status</label>
            <select v-model="form.status" class="w-full px-3 py-2 border rounded-lg">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <button type="submit" :disabled="loading || !isFormValid"
            class="w-full bg-[#ff9c30] text-white py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-600">
            <span v-if="loading">Submitting...</span>
            <span v-else>{{ editMode ? 'Update User' : 'Submit User' }}</span>
          </button>

          <transition name="fade">
            <p v-if="successMessage" class="text-green-600 font-medium mt-3">{{ successMessage }}</p>
          </transition>
          <transition name="fade">
            <p v-if="errorMessage" class="text-red-600 font-medium mt-3">{{ errorMessage }}</p>
          </transition>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import usersService from '@/services/usersService';
import { useToast } from 'vue-toast-notification';

const auth = useAuthStore();
const $toast = useToast();

const showModal = ref(false);
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const editMode = ref(false);

const users = ref([]);
const search = ref('');
const currentPage = ref(1);
const perPage = ref(10);

const form = ref({
  fullname: '',
  email: '',
  phone: '',
  role: 'user',
  status: 'active',
});

// Fetch users
const fetchUsers = async () => {
  try {
    const { data } = await usersService.get();
    users.value = data.users;
  } catch (err) {
    console.error(err);
  }
};

const filteredUsers = computed(() => {
  if (!search.value) return users.value;
  return users.value.filter(u =>
    [u.fullname, u.email, u.phone, u.role].some(f => f?.toLowerCase().includes(search.value.toLowerCase()))
  );
});

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredUsers.value.slice(start, start + perPage.value);
});

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / perPage.value));

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function openModal() {
  form.value = {
    fullname: '',
    email: '',
    phone: '',
    role: 'user',
    status: 'active',
  };
  editMode.value = false;
  showModal.value = true;
}

function editUser(user) {
  form.value = { ...user };
  editMode.value = true;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
}

const isFormValid = computed(() => {
  return form.value.fullname && form.value.email && form.value.phone && form.value.role && form.value.status;
});

async function submitForm() {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  try {
    if (editMode.value) {
      await usersService.update(form.value.id, form.value);
      successMessage.value = 'User updated successfully!';
    } else {
      await usersService.store(form.value);
      successMessage.value = 'User added successfully!';
    }
    await fetchUsers();
    setTimeout(() => closeModal(), 1000);
  } catch (err) {
    console.error(err);
    errorMessage.value = 'Failed to submit user.';
  } finally {
    loading.value = false;
  }
}

async function deleteUser(userId) {
  if (!confirm('Are you sure you want to delete this user?')) return;
  try {
    await usersService.delete(userId);
    await fetchUsers();
    $toast.success('User deleted successfully');
  } catch (err) {
    console.error(err);
    $toast.error('Failed to delete user');
  }
}

onMounted(async () => {
  await fetchUsers();
  await auth.refreshUser();
});
</script>

<style scoped>
body {
  overflow-x: hidden;
}
</style>
