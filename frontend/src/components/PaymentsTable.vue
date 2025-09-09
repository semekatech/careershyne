<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Header -->
      <!-- <div
        class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
      >
        <h2 class="text-lg font-semibold text-gray-800">Recent orders</h2>
        <div>
          <button
            class="py-2 px-6 text-white rounded font-medium transition duration-500"
            :style="{ background: '#fd624e' }"
            @click="showModal = true"
          >
            <font-awesome-icon :icon="['fas', 'plus']" /> Add Order
          </button>
        </div>
      </div> -->

      <!-- Orders Table -->
      <div class="overflow-x-auto">
        <div class="p-4">
          <input
            v-model="search"
            placeholder="Search orders..."
            class="p-2 border rounded"
          />
        </div>

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
                OrderID
              </th>

              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Amount
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Amount
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Phone Number
              </th>
              <th
                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase"
              >
                Date
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(order, index) in paginatedorders" :key="order.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" style="color: dodgerblue">
                {{ order.plan_id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.amount }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.phonenumber }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.transaction_date }}
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

    <!-- 🔹 Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-2 right-2 text-gray-600 hover:text-gray-900"
        >
          ✕
        </button>

        <h3 class="text-xl font-semibold mb-4">Add New Order</h3>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-5">
          <!-- Full Name -->
          <div>
            <label class="block text-gray-700 font-medium">Full Name</label>
            <input
              v-model="form.fullname"
              type="text"
              placeholder="John Doe"
              required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-gray-700 font-medium">Email</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="you@example.com"
              required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
            />
          </div>

          <!-- Phone -->
          <div>
            <label class="block text-gray-700 font-medium">Phone</label>
            <input
              v-model="form.phone"
              type="tel"
              placeholder="+254700000000"
              required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
            />
          </div>
          <!-- Amount -->
          <div>
            <label class="block text-gray-700 font-medium">Amount</label>
            <input
              v-model="form.amount"
              type="number"
              placeholder="Amount"
              required
              class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
            />
          </div>

          <label class="block text-gray-700 font-medium">Type</label>
          <select
            v-model="form.type"
            class="w-full px-3 py-2 border rounded-lg"
          >
            <option value="cv">CV Customization</option>
            <option value="cvscratch">CV Writing</option>
            <option value="coverletter">Cover Letter</option>
          </select>

          <!-- CV Upload -->
          <div>
            <label class="block text-gray-700 font-medium">Upload CV</label>
            <input
              type="file"
              @change="handleFileUpload"
              accept=".pdf,.doc,.docx"
              class="w-full mt-1 px-4 py-2 border rounded-lg cursor-pointer"
            />
            <p v-if="form.cv" class="mt-1 text-sm text-gray-600">
              Selected: <span class="font-medium">{{ form.cv.name }}</span>
            </p>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="w-full bg-[#ff9c30] text-white py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-600"
          >
            <span v-if="loading">Submitting...</span>
            <span v-else>Submit Order</span>
          </button>

          <!-- Feedback -->
          <transition name="fade">
            <p v-if="successMessage" class="text-green-600 font-medium mt-3">
              {{ successMessage }}
            </p>
          </transition>
          <transition name="fade">
            <p v-if="errorMessage" class="text-red-600 font-medium mt-3">
              {{ errorMessage }}
            </p>
          </transition>
        </form>
      </div>
    </div>
  </div>
</template>


<script setup>
// Vue and core imports
import { ref, watch, onMounted, computed } from "vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import orderPayments from "@/services/orderPayment";
import { useToast } from "vue-toast-notification";
import "@vueform/multiselect/themes/default.css";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const $toast = useToast();
const showModal = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");
// Reactive States
const orders = ref([]);

const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

const today = new Date();
const sevenDaysLater = new Date();
sevenDaysLater.setDate(today.getDate() + 7);

const fetchOrders = async () => {
  try {
    const { data } = await orderPayments.get();
    orders.value = data.orders;
  } catch (error) {
    console.error("Error fetching orders.", error);
  }
};
function closeEditModal() {
  const modal = document.getElementById("editModal");
  modal.classList.remove("flex");
  modal.classList.add("hidden");
}

const filteredorders = computed(() => {
  if (!search.value) return orders.value;
  return orders.value.filter((c) =>
    [c.title, c.brand, c.locations].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

const paginatedorders = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredorders.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredorders.value.length / perPage.value)
);
function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

onMounted(async () => {
  await fetchOrders();
  await auth.refreshUser();
});
// Form validation
const isFormValid = computed(() => {
  return (
    form.value.fullname &&
    form.value.email &&
    form.value.phone &&
    form.value.type &&
    form.value.amount
  );
});
const form = ref({
  fullname: "",
  email: "",
  phone: "",
  amount: "",
  type: "",
  cv: null,
});
// Submit form
async function submitForm() {
  loading.value = true;
  successMessage.value = "";
  errorMessage.value = "";

  try {
    const fd = new FormData();
    fd.append("fullname", form.value.fullname);
    fd.append("amount", form.value.amount);
    fd.append("email", form.value.email);
    fd.append("phone", form.value.phone);
    fd.append("type", form.value.type);

    if (form.value.cv) {
      fd.append("cv", form.value.cv);
    }
    await orderService.store(fd);

    successMessage.value = "Order submitted successfully!";
    await fetchOrders();
    setTimeout(() => closeModal(), 1000);
  } catch (err) {
    console.error(err);
    errorMessage.value = "Failed to submit order.";
  } finally {
    loading.value = false;
  }
}
</script>


<style scoped>
body {
  overflow-x: hidden;
}
</style>
