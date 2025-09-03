<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Tabs -->
    <div class="flex gap-1 border-b border-gray-300">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="activeTab = tab"
        class="px-5 py-2 font-medium text-sm transition-colors duration-200 rounded-t-lg border-b-2"
        :class="{
          'border-[##fd624e] text-[#fd624e] bg-white': activeTab === tab,
          'border-transparent hover:text-[#fd624e] hover:bg-gray-50':
            activeTab !== tab,
        }"
      >
        {{ tab }}
      </button>
    </div>

    <!-- Search Header -->
    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
    >
      <h2 class="text-2xl font-bold text-gray-800">Promotion Requests</h2>
      <div class="flex flex-col sm:flex-row gap-3">
        <input
          v-model="search"
          placeholder="Search requests..."
          class="w-full sm:w-72 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
    </div>

    <!-- Loading State -->
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
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
      <div
        v-if="filteredCampaigns.length === 0"
        class="flex flex-col items-center justify-center h-full py-20 text-center text-gray-500"
      >
        <p class="text-lg font-medium" style="text-align: center;">No campaigns found</p>
        <p class="text-sm text-gray-400">
          Try adjusting your filters or search keywords.
        </p>
      </div>

      <div
        v-for="(campaign, index) in paginatedCampaigns"
        :key="index"
        class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col"
      >
        <div class="p-6 flex-1 flex flex-col">
          <router-link
            :to="`/campaigns/${encodeId(campaign.campaign_id)}`"
            class="text-xl font-semibold text-[#e12459] hover:text-[#c11d4d] hover:underline mb-2 transition-colors duration-200"
          >
            {{ truncateWords(campaign.campaign_title, 30) }}
          </router-link>
          <p class="text-sm text-gray-500 mb-4 line-clamp-4">
            {{ campaign.note || "No description provided." }}
          </p>

          <div class="text-sm text-gray-700 space-y-2 mb-4">
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
        </div>

        <!-- Card Footer -->
        <!-- Card Footer -->
        <div
          class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between"
        >
          <span
            class="inline-flex items-center gap-1 text-xs px-3 py-1 rounded-full font-semibold capitalize"
            :class="{
              'bg-green-100 text-green-700': campaign.status === 'approved',
              'bg-yellow-100 text-yellow-700': campaign.status === 'pending',
              'bg-red-100 text-red-700': campaign.status === 'rejected',
            }"
          >
            {{ campaign.status }}
          </span>

          <div class="flex gap-2">
            <button
              class="px-4 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 transition-colors duration-200"
              @click="viewBid(campaign)"
            >
              View Creator Profile
            </button>

            <!-- If pending: Show Approve & Reject buttons -->
            <template v-if="campaign.status === 'pending'">
              <!-- Approve Button -->
              <button
                @click="approveCampaign(campaign.id)"
                class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-green-500 text-white hover:bg-green-600 transition-colors duration-200"
              >
                <font-awesome-icon
                  :icon="['fas', 'check-circle']"
                  class="text-sm"
                />
                Approve
              </button>

              <!-- Reject Button -->
              <button
                @click="rejectCampaign(campaign.id)"
                class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors duration-200"
              >
                <font-awesome-icon
                  :icon="['fas', 'times-circle']"
                  class="text-sm"
                />
                Reject
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <!-- Load More Button -->
    <div class="flex justify-center pt-4">
      <button
        v-if="visibleCount < filteredCampaigns.length"
        @click="loadMore"
        class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-200"
      >
        Load More
      </button>
    </div>

    <!-- Full-Screen Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex flex-col bg-white overflow-y-auto"
    >
      <!-- Modal Header -->
      <div
        class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-pink-500 to-red-500 border-b border-gray-200"
      >
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
          <font-awesome-icon :icon="['fa', 'user']" />
          Creator Profile & Bid Details
        </h3>
        <button
          @click="closeModal"
          class="text-white hover:text-gray-200 text-lg"
        >
          ✕
        </button>
      </div>

      <!-- Modal Content -->
      <div class="flex-1 p-6 space-y-8">
        <section
          class="bg-white border border-gray-200 rounded-xl p-6 shadow-md"
        >
          <!-- Profile Header -->
          <div
            class="flex flex-col md:flex-row items-center md:items-start gap-6 border-b pb-4"
          >
            <img
              :src="
                selectedBid.promoter_photo
                  ? `https://demo.ngumzo.com/storage/${selectedBid.promoter_photo}`
                  : '/profile.jpg'
              "
              @error="$event.target.src = '/profile.jpg'"
              class="w-24 h-24 rounded-full border-4 border-pink-100 object-cover shadow-sm"
            />
            <div class="flex-1 text-center md:text-left">
              <h4 class="text-lg font-bold text-gray-800">
                {{ selectedBid.promoter_name || "Unknown Creator" }}
              </h4>
              <p class="text-sm text-gray-500 italic">
                {{ selectedBid.bio || "No bio provided." }}
              </p>

              <!-- Contact Info -->
              <div class="mt-3">
                <h5
                  class="text-gray-700 font-semibold mb-2 flex items-center gap-1"
                >
                  <font-awesome-icon :icon="['fa', 'address-book']" /> Contacts
                </h5>

                <div
                  class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-700"
                >
                  <!-- Phone -->
                  <div class="flex items-center gap-2">
                    <font-awesome-icon
                      :icon="['fa', 'phone']"
                      class="text-red-500 w-4 h-4"
                    />
                    <span>{{
                      selectedBid.promoter_phone || "No phone provided"
                    }}</span>
                  </div>

                  <!-- Email -->
                  <div class="flex items-center gap-2">
                    <font-awesome-icon
                      :icon="['fa', 'envelope']"
                      class="text-blue-500 w-4 h-4"
                    />
                    <span>{{
                      selectedBid.promoter_email || "No email provided"
                    }}</span>
                  </div>
                </div>
                <span class="block mt-2 text-sm font-medium text-yellow-600">
                  ⭐ Rating: {{ selectedBid.rating || "No rating" }}
                </span>
              </div>
            </div>
          </div>

          <!-- Personal Info -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h5
                class="text-gray-700 font-semibold mb-2 flex items-center gap-1"
              >
                <font-awesome-icon :icon="['fa', 'info-circle']" /> Personal
                Info
              </h5>
              <ul class="space-y-2 text-sm text-gray-700">
                <li>
                  <strong>Location:</strong>
                  {{ selectedBid.location || "No location" }}
                </li>
                <li>
                  <strong>Niche:</strong>
                  {{ selectedBid.niche || "Not specified" }}
                </li>
                <li>
                  <strong>Date of Birth:</strong>
                  {{
                    selectedBid.dob
                      ? selectedBid.dob +
                        ` (${calculateAge(selectedBid.dob)} years old)`
                      : "Not provided"
                  }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Social Media -->
          <div class="mt-6">
            <h5
              class="text-gray-700 font-semibold mb-3 flex items-center gap-1"
            >
              <font-awesome-icon :icon="['fa', 'share-alt']" /> Social Links
            </h5>
            <div class="flex flex-wrap gap-4 text-lg">
              <a
                v-if="selectedBid.instagram"
                :href="selectedBid.instagram"
                target="_blank"
                class="text-pink-500 hover:scale-110 transition"
              >
                <font-awesome-icon :icon="['fab', 'instagram']" />
              </a>
              <a
                v-if="selectedBid.facebook"
                :href="selectedBid.facebook"
                target="_blank"
                class="text-blue-600 hover:scale-110 transition"
              >
                <font-awesome-icon :icon="['fab', 'facebook']" />
              </a>
              <a
                v-if="selectedBid.twitter"
                :href="selectedBid.twitter"
                target="_blank"
                class="text-sky-500 hover:scale-110 transition"
              >
                <font-awesome-icon :icon="['fab', 'twitter']" />
              </a>
              <a
                v-if="selectedBid.linkedin"
                :href="selectedBid.linkedin"
                target="_blank"
                class="text-blue-700 hover:scale-110 transition"
              >
                <font-awesome-icon :icon="['fab', 'linkedin']" />
              </a>
              <a
                v-if="selectedBid.youtube"
                :href="selectedBid.youtube"
                target="_blank"
                class="text-red-700 hover:scale-110 transition"
              >
                <font-awesome-icon :icon="['fab', 'youtube']" />
              </a>
              <a
                v-if="selectedBid.tiktok"
                :href="selectedBid.tiktok"
                target="_blank"
                class="text-black hover:scale-110 transition"
              >
                <font-awesome-icon :icon="['fab', 'tiktok']" />
              </a>
            </div>
          </div>

          <!-- Bid Details -->
          <div class="mt-6">
            <h5 class="text-gray-700 font-semibold mb-2">Engagement Details</h5>
            <ul class="space-y-2 text-sm text-gray-700">
              <li>
                <strong>Proposed Budget:</strong>
                {{
                  typeof selectedBid.amount === "number"
                    ? new Intl.NumberFormat("en-KE", {
                        style: "currency",
                        currency: "KES",
                      }).format(selectedBid.amount)
                    : selectedBid.amount || "Not provided"
                }}
              </li>
              <li>
                <strong>Proposal Note:</strong>
                {{ selectedBid.note || "No note provided" }}
              </li>
              <li>
                <strong>Status:</strong>
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-green-100 text-green-700':
                      selectedBid.status === 'approved',
                    'bg-yellow-100 text-yellow-700':
                      selectedBid.status === 'pending',
                    'bg-red-100 text-red-700':
                      selectedBid.status === 'rejected',
                  }"
                >
                  {{ selectedBid.status || "Unknown" }}
                </span>
              </li>
            </ul>
          </div>

          <!-- Previous Campaigns Carousel -->
          <!-- Previous Campaigns Carousel -->
          <div class="relative overflow-hidden">
            <!-- Controls -->
            <div class="flex justify-end gap-2 mb-2">
              <button
                v-if="isPlaying"
                @click="pauseCarousel"
                class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300"
              >
                ⏸ Pause
              </button>
              <button
                v-else
                @click="resumeCarousel"
                class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300"
              >
                ▶ Play
              </button>
            </div>

            <!-- Slides -->
            <div
              class="flex transition-transform duration-500 ease-in-out"
              :style="{
                transform: `translateX(-${currentSlide * slideWidth}%)`,
              }"
            >
              <div
                v-for="(campaign, index) in selectedBid.previous_campaigns"
                :key="index"
                class="flex-shrink-0 p-2"
                :class="{
                  'w-full': windowWidth < 768, // mobile: 1 card
                  'w-1/3': windowWidth >= 768, // desktop: 3 cards
                }"
              >
                <div class="rounded shadow bg-white">
                  <img
                    :src="campaign.image || '/campaign-placeholder.jpg'"
                    alt="Previous Campaign Image"
                    class="object-cover w-full h-32 rounded-t"
                  />
                  <div class="p-3">
                    <h5 class="font-semibold">{{ campaign.title }}</h5>
                    <p class="text-sm text-gray-600">{{ campaign.brand }}</p>
                    <p class="mt-1 text-sm">{{ campaign.description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Modal Footer -->
      <div
        class="flex justify-end gap-2 px-6 py-4 bg-gray-100 border-t border-gray-200"
      >
        <button
          class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500"
          @click="closeModal"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed, watch, onUnmounted } from "vue";
import bidService from "@/services/bidService";
import { useAuthStore } from "@/stores/auth";
import { encodeId, truncateWords } from "@/utils/helpers";
import { useToast } from "vue-toast-notification";

const loading = ref(true);
const visibleCount = ref(6);

const auth = useAuthStore();
const campaigns = ref([]);
const search = ref("");
const currentPage = ref(1);
const perPage = ref(6);

const selectedBid = ref({});
const showModal = ref(false);
const isEditMode = ref(false);
const $toast = useToast();

function calculateAge(dob) {
  const birthDate = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();
  if (
    monthDiff < 0 ||
    (monthDiff === 0 && today.getDate() < birthDate.getDate())
  ) {
    age--;
  }
  return age;
}
const tabs = ["ALL", "PENDING", "APPROVED", "REJECTED"];
const activeTab = ref("ALL");

const filteredCampaigns = computed(() => {
  let list = campaigns.value;

  // Filter by tab selection
  if (activeTab.value !== "ALL") {
    list = list.filter(
      (c) => c.status?.toLowerCase() === activeTab.value.toLowerCase()
    );
  }

  // Search filter
  if (search.value) {
    list = list.filter((c) =>
      [c.campaign_title, c.promoter_name, c.locations].some((f) =>
        f?.toLowerCase().includes(search.value.toLowerCase())
      )
    );
  }

  return list;
});

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

// const filteredCampaigns = computed(() => {
//   if (!search.value) return campaigns.value;
//   return campaigns.value.filter((c) =>
//     [c.campaign_title, c.promoter_name, c.locations].some((f) =>
//       f?.toLowerCase().includes(search.value.toLowerCase())
//     )
//   );
// });

const paginatedCampaigns = computed(() => {
  return filteredCampaigns.value.slice(0, visibleCount.value);
});

function loadMore() {
  visibleCount.value += 6; // load 6 more
}

const totalPages = computed(() =>
  Math.ceil(filteredCampaigns.value.length / perPage.value)
);

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}
const currentSlide = ref(0);
const windowWidth = ref(window.innerWidth);
let slideInterval = null;

const slideWidth = computed(() => (windowWidth.value < 768 ? 100 : 33.3333));

function startCarousel() {
  stopCarousel();
  slideInterval = setInterval(() => {
    const visibleCards = windowWidth.value < 768 ? 1 : 3;
    const maxIndex = selectedBid.value.previous_campaigns.length - visibleCards;
    currentSlide.value =
      currentSlide.value >= maxIndex ? 0 : currentSlide.value + 1;
  }, 3000);
}

function stopCarousel() {
  if (slideInterval) clearInterval(slideInterval);
}

function handleResize() {
  windowWidth.value = window.innerWidth;
}

onMounted(() => {
  window.addEventListener("resize", handleResize);
  startCarousel();
});

onUnmounted(() => {
  stopCarousel();
  window.removeEventListener("resize", handleResize);
});
const isPlaying = ref(true);

function pauseCarousel() {
  stopCarousel();
  isPlaying.value = false;
}

function resumeCarousel() {
  startCarousel();
  isPlaying.value = true;
}
async function approveCampaign(id) {
  try {
    await bidService.approve(id);
    await fetchRequests();
    $toast.success("Campaign approved successfully!", {
      position: "top-right",
    });
  } catch (error) {
    console.error("Error approving campaign:", error);
    $toast.error("Failed to approve campaign.", { position: "top-right" });
  }
}

async function rejectCampaign(id) {
  try {
    await bidService.reject(id);
    await fetchRequests();
    $toast.success("Campaign rejected successfully!", {
      position: "top-right",
    });
  } catch (error) {
    console.error("Error rejecting campaign:", error);
    $toast.error("Failed to reject campaign.", { position: "top-right" });
  }
}

function viewBid(bid) {
  selectedBid.value = {
    ...bid,
    previous_campaigns:
      bid.previous_campaigns && bid.previous_campaigns.length
        ? bid.previous_campaigns
        : [
            {
              title: "Summer Sale 2024",
              brand: "Brand A",
              image: "https://example.com/campaign1.jpg",
              description:
                "Promoted the summer discount offers on social media.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
            {
              title: "New Product Launch",
              brand: "Brand B",
              image: null,
              description: "Collaborated to launch the latest product line.",
            },
          ],
  };
  isEditMode.value = false;
  showModal.value = true;
}

function editBid(bid) {
  selectedBid.value = { ...bid };
  isEditMode.value = true;
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  selectedBid.value = {};
  isEditMode.value = false;
}

async function submitEdit() {
  try {
    const formData = new FormData();
    formData.append("amount", selectedBid.value.amount);
    formData.append("note", selectedBid.value.note);

    await bidService.update(selectedBid.value.id, formData);
    closeModal();
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
/* global.css or main.css */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
/* Add hover and active styles */
button {
  cursor: pointer;
}
</style>
