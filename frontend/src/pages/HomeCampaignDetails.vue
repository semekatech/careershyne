<template>
  <div class="min-h-screen flex flex-col">
    <main
      class="flex-grow bg-gradient-to-br from-rose-50 to-white dark:from-gray-900 dark:to-gray-800"
    >
      <TheWelcome />
      <!-- Loader shown while fetching -->
      <section class="py-14" v-if="loading">
        <div class="max-w-6xl mx-auto text-center text-rose-600 font-semibold">
          ⏳ Loading campaign...
        </div>
      </section>
      <section class="py-14" v-else>
        <div class="max-w-6xl mx-auto px-2 sm:px-4 lg:px-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
            <!-- Left: Images + Content -->
            <div>
              <h1
                class="block md:hidden text-2xl font-bold text-gray-900 dark:text-white mb-4"
                style="color: #fd624e; margin-top: -30px"
              >
                {{ campaign?.title }}
              </h1>
              <div
                class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm"
              >
                <img
                  :src="mainImage"
                  alt="Main Asset"
                  class="w-full h-[400px] object-cover"
                />
              </div>

              <div class="flex flex-wrap gap-3 mt-4">
                <img
                  v-for="(asset, idx) in campaign?.assets"
                  :key="idx"
                  :src="`https://demo.ngumzo.com/storage/${asset}`"
                  @click="
                    changeImage(`https://demo.ngumzo.com/storage/${asset}`)
                  "
                  class="w-20 h-20 object-cover rounded-md border cursor-pointer hover:opacity-80 transition ring-offset-2"
                  :class="{
                    'ring-2 ring-pink-500':
                      mainImage === `https://demo.ngumzo.com/storage/${asset}`,
                  }"
                />
              </div>

              <!-- Description -->
              <div
                class="mt-8 space-y-8 text-sm text-gray-700 dark:text-gray-300"
              >
                <p class="text-base leading-relaxed">{{ campaign?.about }}</p>

                <div style="margin-top: -1px">
                  <h3
                    class="text-xl font-semibold text-gray-800 dark:text-white mb-2"
                  >
                    Brief
                  </h3>
                  <div
                    v-html="campaign?.brief"
                    class="prose prose-sm max-w-none dark:prose-invert"
                  ></div>
                </div>

                <div style="margin-top: -20px">
                  <h3
                    class="text-xl font-semibold text-gray-800 dark:text-white"
                  >
                    Creative Guidance
                  </h3>
                  <div
                    v-html="campaign?.guidance"
                    class="prose prose-sm max-w-none dark:prose-invert"
                  ></div>
                </div>

                <div style="margin-top: -20px">
                  <h3
                    class="text-xl font-semibold text-gray-800 dark:text-white mb-2"
                  >
                    Influencer Eligibility
                  </h3>
                  <div
                    v-html="campaign?.eligibility"
                    class="prose prose-sm max-w-none dark:prose-invert"
                  ></div>
                </div>
              </div>
            </div>

            <!-- Right: Campaign Info + Bid -->
            <div
              class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-md border dark:border-gray-700"
            >
              <h1
                class="hidden md:block text-3xl font-extrabold text-rose-600 dark:text-white mb-4 leading-tight"
                style="color: #fd624e"
              >
                {{ campaign?.title }}
              </h1>
              <!-- <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
              Created on: {{ formatDate(campaign?.created_at) }}
              </p> -->

              <div class="mb-8 space-y-3 text-gray-700 dark:text-gray-300">
                <p class="text-lg font-semibold">
                  {{ campaign?.brand }}
                  <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                    >| {{ campaign?.website }}</span
                  >
                </p>
                <p class="text-sm">
                  <strong>From </strong>
                  {{ formatDate(campaign?.start_date) }} - <strong>To </strong>
                  {{ formatDate(campaign?.end_date) }}
                </p>
                <p class="text-sm">
                  <strong>Budget:</strong> {{ campaign?.budget }}<br />
                </p>
                <p class="text-sm">
                  <strong>Location:</strong> {{ campaign?.residence }}<br />
                </p>
                <p class="text-sm">
                  <strong>Interests:</strong> {{ campaign?.target_interests
                  }}<br />
                </p>
                <p class="text-sm">
                  <strong>Target Age:</strong> {{ campaign?.target_age }}<br />
                </p>

                <p class="text-sm">
                  <strong>Goal:</strong> {{ campaign?.primary_goal }}<br />
                </p>
              </div>

              <!-- Bid Form -->
              <div class="mt-8">
                <button
                  style="background: #fd624e"
                  v-if="user"
                  @click="showBidForm = !showBidForm"
                  class="w-full hover:bg-rose-700 text-white font-semibold py-2 px-4 rounded-lg transition"
                >
                  {{ showBidForm ? "✖️ Close Bid Form" : "Express Interest" }}
                </button>

                <router-link
                  v-else
                  :to="{ path: '/login', query: { redirect: route.fullPath } }"
                  style="background: #fd624e"
                  class="w-full block text-center hover:bg-rose-700 text-white font-semibold py-2 px-4 rounded-lg transition"
                >
                  Login to Bid
                </router-link>

                <transition name="fade">
                  <form
                    v-if="user && showBidForm"
                    @submit.prevent="submitBid"
                    class="mt-6 space-y-4 bg-rose-50 dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-700"
                  >
                    <div>
                      <label
                        for="bidAmount"
                        class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300"
                        >Enter Your Budget
                      </label>
                      <input
                        type="number"
                        id="bidAmount"
                        v-model="bidAmount"
                        class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600"
                        placeholder="Enter your bid amount"
                        required
                      />
                    </div>
                    <div>
                      <label
                        for="note"
                        class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300"
                        >Note</label
                      >
                      <textarea
                        id="note"
                        v-model="note"
                        rows="3"
                        class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600"
                        placeholder="Tell us why you're a great fit"
                      ></textarea>
                    </div>
                    <button
                      type="submit"
                      class="w-full hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded-lg transition"
                      style="background: #fd624e"
                    >
                      Submit Bid
                    </button>
                  </form>
                </transition>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <FooterSection />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import campaignService from "@/services/campaignService";
import TheWelcome from "@/components/TheWelcome.vue";
import { useToast } from "vue-toast-notification";
import FooterSection from "@/components/FooterSection.vue";
import { useAuthStore } from "@/stores/auth";
import bidService from "@/services/bidService";
const $toast = useToast();
const route = useRoute();
const campaign = ref(null);
const mainImage = ref("");
const showBidForm = ref(false);
const bidAmount = ref("");
const note = ref("");
const loading = ref(true);
const auth = useAuthStore();
const { user } = auth;

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString();
}

function changeImage(src) {
  mainImage.value = src;
}

async function submitBid() {
  try {
    if (!bidAmount.value) {
      $toast.warning("Please enter a bid amount.");
      return;
    }

    await bidService.submitBid({
      campaign_id: campaign.value.id,
      amount: bidAmount.value,
      note: note.value,
    });

    $toast.success("✅ Interest Request submitted successfully.", {
      position: "top-right",
    });

    showBidForm.value = false;
    bidAmount.value = "";
    note.value = "";
  } catch (error) {
    console.error("Bid submission failed:", error);

    if (error.response && error.response.status === 409) {
      $toast.warning(error.response.data.message, {
        position: "top-right",
      });
    } else if (error.response && error.response.status === 403) {
      $toast.error(error.response.data.message, {
        position: "top-right",
      });
    } else {
      $toast.error("❌ Failed to submit bid. Please try again.", {
        position: "top-right",
      });
    }
  }
}

onMounted(async () => {
  const id = route.params.id;
  const res = await campaignService.fetch(id);
  campaign.value = res.data;
  if (res.data.assets.length > 0) {
    mainImage.value = `https://demo.ngumzo.com/storage/${res.data.assets[0]}`;
  }
  loading.value = false;
});
</script>

<style scoped>
.prose {
  max-width: 100%;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
