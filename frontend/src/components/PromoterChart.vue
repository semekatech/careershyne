<template>
  <!-- Recent Bookings and Room Status -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 items-start">
    <!-- Recent Bookings -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2
          class="text-lg font-semibold text-gray-800"
          style="color: #fd624e; font-weight: bold"
        >
          Recent Requests
        </h2>
      </div>
      <div class="overflow-x-auto">
        <div class="p-4">
          <input
            v-model="search"
            placeholder="Search campaigns..."
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
               Budget
              </th>
                <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
               Date
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase"
              >
                Status
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(campaign, index) in paginatedCampaigns" :key="index">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" style="color: dodgerblue">
                <router-link :to="`/campaigns/${encodeId(campaign.campaign_id)}`">
                  {{ truncateWords(campaign.campaign_title, 4) }}
                </router-link>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ campaign.campaign_brand }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ campaign.amount }}
              </td>
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

    <!-- Room Status -->
    <div
      class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200"
    >
      <div
        class="flex items-center justify-between px-6 py-4 bg-pink-600 text-white"
        style="background: #e11d50"
      >
        <div class="flex items-center space-x-3">
          <svg
            class="w-6 h-6 text-white"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M13 16h-1v-4h-1m1-4h.01M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z"
            ></path>
          </svg>
          <h2 class="text-lg font-semibold">Notifications</h2>
        </div>
      </div>

      <div class="px-6 py-5 text-gray-700 space-y-4">
        <!-- <p>
          Hi
          <span class="font-semibold text-gray-800">{{ auth.user?.name }}</span
          >, your dashboard is all set up!
        </p> -->

        <div v-if="notifications.length">
          <ul class="space-y-4">
            <li
              v-for="note in notifications"
              :key="note.id"
              class="flex items-start gap-2 bg-gray-50 p-4 rounded-md shadow-sm border"
            >
              <!-- Check icon -->
              <svg
                class="w-5 h-5 mt-1 text-green-500 shrink-0"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M5 13l4 4L19 7"
                />
              </svg>

              <!-- Notification content -->
              <div class="flex-1">
                <div
                  v-html="note.message"
                  class="text-sm text-gray-800 leading-relaxed"
                ></div>
              </div>
            </li>
          </ul>
        </div>

        <p v-else class="text-sm text-gray-500">No new notifications.</p>

        <!-- If limit is 0, show exhausted message -->
      </div>
    </div>
  </div>
</template> 

<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useRoute } from "vue-router";
const route = useRoute();
const router = useRouter();
const notifications = ref([]);
const pageTitle = computed(() => route.meta.title || "Dashboard");
import bidService from "@/services/bidService";
import notificationService from "@/services/notificationsService";
const campaigns = ref([]);
const auth = useAuthStore();
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);
import Hashids from "hashids";
import { Buffer } from "buffer";
function base64Encode(id) {
  return Buffer.from(String(id)).toString("base64");
}
const hashids = new Hashids("32esdf33esdfg4321", 6);
const truncateWords = (text, limit = 40) => {
  if (!text) return "";
  const words = text.split(" ");
  return words.length > limit ? words.slice(0, limit).join(" ") + "..." : text;
};

function encodeId(id) {
  return hashids.encode(id);
}

function decodeId(hash) {
  return hashids.decode(hash)[0];
}
const fetchRequests = async () => {
  try {
    const { data } = await bidService.get();
    campaigns.value = data.bids;
    console.log("Fetched bids", campaigns.value);
  } catch (error) {
    console.error("Error fetching bids.", error);
  }
};

const fetchNotifications = async () => {
  try {
    const { data } = await notificationService.get();
    notifications.value = data.notifications;
  } catch (error) {
    console.error("Failed to load notifications:", error);
  }
};
onMounted(async () => {
  await fetchRequests();
  await auth.refreshUser();
  await fetchNotifications();
});

const filteredCampaigns = computed(() => {
  if (!search.value) return campaigns.value;
  return campaigns.value.filter(
    (c) =>
      c.title.toLowerCase().includes(search.value.toLowerCase()) ||
      c.brand.toLowerCase().includes(search.value.toLowerCase()) ||
      c.locations.toLowerCase().includes(search.value.toLowerCase())
  );
});

// paginate afterwards
const paginatedCampaigns = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredCampaigns.value.slice(start, start + perPage.value);
});

// total page count
const totalPages = computed(() =>
  Math.ceil(filteredCampaigns.value.length / perPage.value)
);

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}
</script>