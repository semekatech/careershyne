<template>
  <div class="grid grid-cols-1 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div
        class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
      >
        <h2 class="text-lg font-semibold text-gray-800">My Requests</h2>
      </div>

      <div class="overflow-x-auto">
        <div class="p-4">
          <input
            v-model="search"
            placeholder="Search Request..."
            class="p-2 border rounded w-full max-w-md"
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
                Campaign
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Brand
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                My Bid
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Bid Date
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Status
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(campaign, index) in paginatedCampaigns" :key="index">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-blue-600">
                <router-link :to="`/campaigns/${encodeId(campaign.campaign_id)}`">
                  {{ truncateWords(campaign.campaign_title, 40) }}
                </router-link>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ campaign.campaign_brand }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">{{ campaign.amount }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ new Date(campaign.created_at).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    campaign.status === 'approved'
                      ? 'bg-green-100 text-green-800'
                      : campaign.status === 'rejected'
                      ? 'bg-red-100 text-red-800'
                      : 'bg-yellow-100 text-yellow-800',
                  ]"
                >
                  {{
                    campaign.status === "approved"
                      ? "Approved"
                      : campaign.status === "rejected"
                      ? "Rejected"
                      : "Pending"
                  }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap space-x-2">
                <button
                  v-if="campaign.status === 'pending'"
                  class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600"
                  @click="editBid(campaign)"
                >
                  Edit
                </button>
                <button
                  class="px-3 py-1 text-xs bg-gray-500 text-white rounded hover:bg-gray-600"
                  @click="viewBid(campaign)"
                >
                  View
                </button>
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

    <!-- View/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white p-6 rounded-2xl shadow-xl w-full max-w-md">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
          {{ isEditMode ? "Edit Bid" : "Bid Details" }}
        </h3>
        <hr class="mb-4 border-gray-300" />

        <div class="space-y-4 text-sm text-gray-700">
          <div>
            <label class="font-semibold block mb-1">Budget:</label>
            <template v-if="isEditMode">
              <input
                type="number"
                v-model="selectedBid.amount"
                class="w-full border rounded px-3 py-2"
              />
            </template>
            <template v-else>
              <span class="text-gray-900">{{ selectedBid.amount }}</span>
            </template>
          </div>

          <div>
            <label class="font-semibold block mb-1">Details:</label>
            <template v-if="isEditMode">
              <textarea
                v-model="selectedBid.note"
                rows="4"
                class="w-full border rounded px-3 py-2"
              ></textarea>
            </template>
            <template v-else>
              <p class="whitespace-pre-line text-gray-800">
                {{ selectedBid.note || "No additional details provided." }}
              </p>
            </template>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-2">
          <button
            class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500"
            @click="showModal = false"
          >
            Cancel
          </button>
          <button
            v-if="isEditMode"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            @click="submitEdit"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import bidService from "@/services/bidService";
import { useAuthStore } from "@/stores/auth";
import { encodeId, truncateWords } from "@/utils/helpers";
import { useToast } from "vue-toast-notification";
const auth = useAuthStore();
const campaigns = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);

const selectedBid = ref({});
const showModal = ref(false);
const isEditMode = ref(false);
const $toast = useToast();
const fetchRequests = async () => {
  try {
    const { data } = await bidService.get();
    campaigns.value = data.bids;
  } catch (error) {
    console.error("Error fetching bids.", error);
  }
};

const filteredCampaigns = computed(() => {
  if (!search.value) return campaigns.value;
  return campaigns.value.filter((c) =>
    [c.campaign_title, c.campaign_brand, c.locations].some((f) =>
      f?.toLowerCase().includes(search.value.toLowerCase())
    )
  );
});

const paginatedCampaigns = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredCampaigns.value.slice(start, start + perPage.value);
});

const totalPages = computed(() =>
  Math.ceil(filteredCampaigns.value.length / perPage.value)
);

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function viewBid(bid) {
  selectedBid.value = bid;
  isEditMode.value = false;
  showModal.value = true;
}

function editBid(bid) {
  selectedBid.value = { ...bid }; 
  isEditMode.value = true;
  showModal.value = true;
}

async function submitEdit() {
  try {
    const formData = new FormData();
    formData.append("amount", selectedBid.value.amount);
    formData.append("note", selectedBid.value.note);

    await bidService.update(selectedBid.value.id, formData);
    showModal.value = false;
      $toast.success("Bid Updated successfully!", { position: "top-right" });
    await fetchRequests();
  } catch (error) {
    console.error("Failed to update bid:", error);
    alert("Failed to save changes. Please try again.");
  }
}
onMounted(async () => {
  await fetchRequests();
  await auth.refreshUser();
});
</script>

<style scoped>
body {
  overflow-x: hidden;
}
</style>
