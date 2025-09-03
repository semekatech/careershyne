<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Search Header -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
    >
      <h2 class="text-2xl font-bold text-gray-800">Promotion Requests</h2>
      <input
        v-model="search"
        placeholder="Search requests..."
        class="w-full sm:w-96 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <!-- Campaign Cards -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <svg
        class="animate-spin h-10 w-10 text-blue-600"
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
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8v8z"
        ></path>
      </svg>
    </div>

    <!-- Campaign Cards -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="(campaign, index) in paginatedCampaigns"
        :key="index"
        class="bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md transition-all p-6 flex flex-col justify-between"
      >
        <!-- Campaign Title -->
        <router-link
          :to="`/campaigns/${encodeId(campaign.campaign_id)}`"
          class="text-lg font-semibold text-[#e12459] hover:underline mb-2"
        >
          {{ truncateWords(campaign.campaign_title, 30) }}
        </router-link>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-5">
          {{ campaign.note || "No description provided." }}
        </p>
        <!-- Bidder Info -->
        <!-- <div class="text-sm text-gray-700 space-y-1 mb-3">
          <p>
            <span class="font-semibold text-gray-600">Name:</span>
            {{ campaign.promoter_name || "N/A" }}
          </p>
          <p>
            <span class="font-semibold text-gray-600">Email:</span>
            {{ campaign.promoter_email || "-" }}
          </p>
          <p>
            <span class="font-semibold text-gray-600">Phone:</span>
            {{ campaign.promoter_phone || "-" }}
          </p>
        </div> -->

        <!-- Social Media Links -->
        <!-- <div
          class="flex flex-wrap items-center gap-3 mb-4 text-[18px] text-gray-500"
          v-if="
            campaign.instagram ||
            campaign.twitter ||
            campaign.facebook ||
            campaign.youtube ||
            campaign.tiktok
          "
        >
          <a
            v-if="campaign.instagram"
            :href="campaign.instagram"
            target="_blank"
            rel="noopener"
            title="Instagram"
          >
            <font-awesome-icon
              :icon="['fab', 'instagram']"
              class="hover:text-pink-600"
            />
          </a>
          <a
            v-if="campaign.twitter"
            :href="campaign.twitter"
            target="_blank"
            rel="noopener"
            title="Twitter"
          >
            <font-awesome-icon
              :icon="['fab', 'twitter']"
              class="hover:text-blue-400"
            />
          </a>
          <a
            v-if="campaign.instagram"
            :href="campaign.facebook"
            target="_blank"
            rel="noopener"
            title="Facebook"
          >
            <font-awesome-icon
              :icon="['fab', 'facebook']"
              class="hover:text-blue-600"
            />
          </a>
          <a
            v-if="campaign.youtube"
            :href="campaign.youtube"
            target="_blank"
            rel="noopener"
            title="YouTube"
          >
            <font-awesome-icon
              :icon="['fab', 'youtube']"
              class="hover:text-red-600"
            />
          </a>
          <a
            v-if="campaign.tiktok"
            :href="campaign.tiktok"
            target="_blank"
            rel="noopener"
            title="TikTok"
          >
            <font-awesome-icon
              :icon="['fab', 'tiktok']"
              class="hover:text-black"
            />
          </a>
        </div> -->

        <!-- Budget & Date -->
        <div class="text-sm text-gray-700 mb-4 space-y-1">
          <p>
            <span class="font-semibold text-gray-600">Creator Budget:</span>
            {{
              typeof campaign.amount === "number"
                ? new Intl.NumberFormat("en-KE", {
                    style: "currency",
                    currency: "KES",
                  }).format(campaign.amount)
                : campaign.amount
            }}
          </p>
          <p>
            <span class="font-semibold text-gray-600">Date Submitted:</span>
            {{ new Date(campaign.created_at).toLocaleDateString() }}
          </p>
        </div>

        <!-- Status & Actions -->
        <div
          class="flex justify-between items-center pt-3 mt-auto border-t border-gray-100"
        >
          <span
            class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full font-semibold"
            :class="{
              'bg-green-100 text-green-700': campaign.status === 'approved',
              'bg-yellow-100 text-yellow-700': campaign.status === 'pending',
              'bg-red-100 text-red-700': campaign.status === 'rejected',
            }"
          >
            {{
              campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)
            }}
          </span>

          <div class="flex gap-2">
            <!-- <button
              v-if="campaign.status === 'pending'"
              class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition"
              @click="editBid(campaign)"
            >
              Edit
            </button> -->
            <button
              class="px-3 py-1 text-xs bg-gray-600 text-white rounded hover:bg-gray-700 transition"
              @click="viewBid(campaign)"
            >
              View Details
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center pt-4">
      <button
        @click="prevPage"
        :disabled="currentPage <= 1"
        class="text-sm px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
      >
        Previous
      </button>
      <span class="text-sm text-gray-600"
        >{{ currentPage }} / {{ totalPages }}</span
      >
      <button
        @click="nextPage"
        :disabled="currentPage >= totalPages"
        class="text-sm px-4 py-2 bg-gray-200 rounded disabled:opacity-50"
      >
        Next
      </button>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white w-full max-w-md mx-auto rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
          {{ isEditMode ? "Edit Bid" : "Bid Details" }}
        </h3>
        <div class="space-y-4 text-sm">
          <div>
            <label class="block font-medium mb-1">Budget</label>
            <template v-if="isEditMode">
              <input
                type="number"
                v-model="selectedBid.amount"
                class="w-full border px-3 py-2 rounded"
              />
            </template>
            <template v-else>
              <p class="text-gray-800">{{ selectedBid.amount }}</p>
            </template>
          </div>
          <div>
            <label class="block font-medium mb-1">Details</label>
            <template v-if="isEditMode">
              <textarea
                v-model="selectedBid.note"
                rows="4"
                class="w-full border px-3 py-2 rounded"
              ></textarea>
            </template>
            <template v-else>
              <p class="text-gray-700 whitespace-pre-line">
                {{ selectedBid.note || "No additional details provided." }}
              </p>
            </template>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button
            class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500"
            @click="showModal = false"
          >
            Cancel
          </button>
          <button
            v-if="isEditMode"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
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
const loading = ref(true);

const auth = useAuthStore();
const campaigns = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(6);

const selectedBid = ref({});
const showModal = ref(false);
const isEditMode = ref(false);
const $toast = useToast();

const fetchRequests = async () => {
  loading.value = true;
  try {
    const { data } = await bidService.get();
    campaigns.value = data.bids;
  } catch (error) {
    console.error("Error fetching bids.", error);
  } finally {
    loading.value = false;
  }
};

const filteredCampaigns = computed(() => {
  if (!search.value) return campaigns.value;
  return campaigns.value.filter((c) =>
    [c.campaign_title, c.promoter_name, c.locations].some((f) =>
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
