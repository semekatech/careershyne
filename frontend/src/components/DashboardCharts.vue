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
          Recent Campaign Requests
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
                Title
              </th>

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
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(campaign, index) in paginatedCampaigns" :key="index">
              <td class="px-6 py-4 whitespace-nowrap">
                {{ (currentPage - 1) * perPage + index + 1 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap" style="color: dodgerblue">
                <router-link :to="`/campaigns/${encodeId(campaign.id)}`">
                  {{ truncateWords(campaign.title, 40) }}
                </router-link>
              </td>

              <td class="px-6 py-4 whitespace-nowrap">
                {{ new Date(campaign.start_date).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                {{ new Date(campaign.end_date).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 py-1 rounded text-xs font-semibold',
                    campaign.status == 1
                      ? 'bg-green-100 text-green-800'
                      : campaign.status == 0
                      ? 'bg-red-100 text-red-800'
                      : 'bg-yellow-100 text-yellow-800',
                  ]"
                >
                  {{
                    campaign.status == 1
                      ? "Active"
                      : campaign.status == 0
                      ? "Inactive"
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
        style="background: #fd624e"
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
          <h2 class="text-lg font-semibold">Welcome to Your Dashboard</h2>
        </div>
      </div>

      <div class="px-6 py-5 text-gray-700 space-y-4">
        <p>
          Hi
          <span class="font-semibold text-gray-800">{{ auth.user?.name }}</span
          >, your dashboard is all set up!
        </p>
        <p  v-if="auth.user?.limit > 0">
          You can now post up to
          <span class="font-semibold text-pink-600">{{
            auth.user?.limit
          }}</span>
          campaign<span v-if="auth.user?.limit !== 1">s</span>.
        </p>

        <p class="text-sm text-gray-500" v-if="auth.user?.limit > 0">
          Ready to connect with your next promoter?
        </p>

        <!-- If limit is 0, show exhausted message -->
        <div
          v-if="auth.user?.limit === 0"
          class="p-4 mt-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 rounded"
        >
          <p class="font-semibold">
            Looks like you've exhausted your campaign postings.
          </p>
          <p>
            Want to post more?
            <router-link
              to="/upgrade"
              class="text-pink-600 underline hover:text-pink-800"
              >Upgrade your plan</router-link
            >
            to unlock more campaign slots.
          </p>
        </div>

        <!-- Get Started Button -->
        <div v-else>
          <router-link
            to="/manage-campaigns" style="background:#fd624e"
            class="inline-flex items-center justify-center w-full px-5 py-2.5 mt-2  hover:bg-pink-700 text-white font-medium rounded-lg transition duration-200"
          >
            Get Started
            <svg
              class="w-4 h-4 ml-2"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M17 8l4 4m0 0l-4 4m4-4H3"
              />
            </svg>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template> 

<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useRoute } from "vue-router";
import { encodeId, truncateWords } from "@/utils/helpers";
import campaignService from "@/services/campaignService";
const route = useRoute();
const router = useRouter();
const pageTitle = computed(() => route.meta.title || "Dashboard");

const campaigns = ref([]);
const auth = useAuthStore();
const search = ref("");
const currentPage = ref(1);
const perPage = ref(10);



const fetchCampaigns = async () => {
  try {
    const { data } = await campaignService.get();
    campaigns.value = data.campaigns;
    console.log("Fetched campaigns", campaigns.value);
  } catch (error) {
    console.error("Error fetching campaigns.", error);
  }
};

onMounted(async () => {
  await fetchCampaigns();
  await auth.refreshUser();
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