<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Add Campaign Button -->

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div
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
            <!-- {{ auth.user?.limit }} -->
          </button>
        </div>
      </div>

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
                Title
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Brand
              </th>
              <!-- <th
        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
      >
        Location
      </th> -->
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Compensation
              </th>
              <!-- <th
        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
      >
        Budget
      </th> -->
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Start
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                End
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Status
              </th>
              <th
                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase"
              >
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(order, index) in paginatedorders" :key="order.id">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" style="color: dodgerblue">
                {{ order.fullname }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.phone }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.orderID }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.type }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ order.amount }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    order.status === 'completed'
                      ? 'bg-green-100 text-green-800'
                      : order.status === 'pending'
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-red-100 text-red-800',
                  ]"
                >
                  {{ order.status }}
                </span>
              </td>
              <td
                class="px-6 py-4 whitespace-nowrap text-center flex justify-center gap-2"
              >
                <button
                  @click="editOrder(order)"
                  class="px-2 py-1 bg-blue-500 text-gray-100 rounded text-xs flex items-center gap-1"
                >
                  <Pencil :size="14" /> Edit
                </button>

                <a
                  :href="order.cv_path"
                  target="_blank"
                  class="px-2 py-1 bg-gray-600 text-gray-100 rounded text-xs flex items-center gap-1"
                >
                  <Download :size="14" /> CV
                </a>
              </td>
            </tr>
          </tbody>
        </table>

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
  </div>
</template>


<script setup>
// Vue and core imports
import { ref, watch, onMounted, computed } from "vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import orderService from "@/services/orderService";
import { useToast } from "vue-toast-notification";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import { useAuthStore } from "@/stores/auth";
import { encodeId, truncateWords, toYMD } from "@/utils/helpers";

const auth = useAuthStore();
const $toast = useToast();

// Reactive States
const orders = ref([]);
const showModal = ref(false);
const showEditModal = ref(false);
const selectedCampaign = ref(null);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);
const existingAssets = ref([]);
const newAssets = ref([]);

const today = new Date();
const sevenDaysLater = new Date();
sevenDaysLater.setDate(today.getDate() + 7);

const fetchOrders = async () => {
  try {
    const { data } = await orderService.get();
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
</script>


<style scoped>
body {
  overflow-x: hidden;
}
</style>
