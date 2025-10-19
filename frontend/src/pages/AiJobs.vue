<template>
  <AiTheWelcome />
  <div class="relative flex flex-col min-h-screen overflow-x-hidden mt-5">
    <!-- 🏠 Hero Section -->
    <section
      class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-900 dark:to-slate-800 py-20 text-center relative"
    >
      <div
        class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5 dark:opacity-[0.03]"
      ></div>
      <div class="relative z-10 max-w-2xl mx-auto px-6">
        <h1
          class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white"
        >
          Find Your <span class="text-primary">Dream Job</span> Today
        </h1>
        <p class="mt-3 text-slate-600 dark:text-slate-400">
          Search through thousands of job listings from top companies.
        </p>
        <div class="relative mt-6">
          <input
            v-model="search"
            placeholder="Job title, keywords, or company"
            class="w-full rounded-full border-2 border-transparent px-5 py-3 shadow focus:ring-4 focus:ring-primary/40 bg-white/70 dark:bg-slate-900/50 text-slate-900 dark:text-white backdrop-blur-sm focus:border-primary/50"
          />
          <button
            @click="reloadJobs"
            class="absolute right-3 top-1/2 -translate-y-1/2 bg-primary text-white px-6 py-2 rounded-full font-semibold shadow-md hover:bg-primary-focus transition-all duration-300"
          >
            Search
          </button>
        </div>
      </div>
    </section>

    <!-- 🧭 Filters -->
    <div
      class="flex flex-wrap items-center gap-3 p-2 rounded-full glassmorphic w-full lg:w-max mx-auto shadow-md mt-10 relative"
    >
      <!-- 🌍 Location -->
      <div class="relative">
        <button
          @click="toggleLocationDropdown"
          class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
        >
          <span
            class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
          >
            location_on
          </span>
          <p class="text-sm font-semibold leading-normal">
            {{ selectedLocation || "Location" }}
          </p>
        </button>

        <div
          v-if="showLocationDropdown"
          class="absolute z-20 mt-2 bg-white dark:bg-slate-800 shadow-lg rounded-lg w-56 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700"
        >
          <div
            v-for="county in counties"
            :key="county"
            @click="selectLocation(county)"
            class="px-4 py-2 text-sm cursor-pointer hover:bg-primary hover:text-white dark:hover:bg-primary/80"
          >
            {{ county }}
          </div>
        </div>
      </div>

      <!-- 🕒 Job Type -->
      <div class="relative">
        <button
          @click="toggleTypeDropdown"
          class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
        >
          <span
            class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
          >
            work
          </span>
          <p class="text-sm font-semibold leading-normal">
            {{ selectedType || "Job Type" }}
          </p>
        </button>

        <div
          v-if="showTypeDropdown"
          class="absolute z-20 mt-2 bg-white dark:bg-slate-800 shadow-lg rounded-lg w-48 border border-gray-200 dark:border-gray-700"
        >
          <div
            v-for="type in jobTypes"
            :key="type"
            @click="selectType(type)"
            class="px-4 py-2 text-sm cursor-pointer hover:bg-primary hover:text-white dark:hover:bg-primary/80"
          >
            {{ type }}
          </div>
        </div>
      </div>

      <!-- 🏭 Industry -->
      <div class="relative">
        <button
          @click="toggleIndustryDropdown"
          class="flex h-10 items-center justify-center gap-x-2 rounded-full bg-white/80 dark:bg-card-dark/80 px-4 shadow-sm hover:bg-primary hover:text-white dark:hover:bg-primary dark:text-white group transition-all duration-300 transform hover:scale-105"
        >
          <span
            class="material-icons-sharp text-text-light dark:text-text-dark group-hover:text-white transition-colors"
          >
            business
          </span>
          <p class="text-sm font-semibold leading-normal">
            {{ selectedIndustryLabel || "Industry" }}
          </p>
        </button>

        <div
          v-if="showIndustryDropdown"
          class="absolute z-20 mt-2 bg-white dark:bg-slate-800 shadow-lg rounded-lg w-64 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-700"
        >
          <div
            v-for="industry in industryOptions"
            :key="industry.id"
            @click="selectIndustry(industry)"
            class="px-4 py-2 text-sm cursor-pointer hover:bg-primary hover:text-white dark:hover:bg-primary/80"
          >
            {{ industry.name }}
          </div>
        </div>
      </div>

      <button
        @click="clearFilters"
        class="text-sm font-medium text-primary hover:underline ml-auto pr-4"
      >
        Clear Filters
      </button>
    </div>

    <!-- 🧱 Jobs Grid -->
    <main class="flex-1 px-4 sm:px-10 lg:px-20 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- 🔄 Shimmer Loading -->
        <template v-if="loading && jobs.length === 0">
          <div
            v-for="n in 6"
            :key="n"
            class="p-6 bg-white dark:bg-card-dark rounded-xl shadow-lg animate-pulse"
          >
            <div class="flex items-center gap-4 mb-4">
              <div
                class="w-14 h-14 bg-gray-300 dark:bg-gray-700 rounded-xl"
              ></div>
              <div class="flex-1 space-y-2">
                <div
                  class="h-4 bg-gray-300 dark:bg-gray-700 rounded w-3/4"
                ></div>
                <div
                  class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-1/2"
                ></div>
              </div>
            </div>
            <div class="space-y-2">
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
            </div>
            <div
              class="h-10 bg-gray-300 dark:bg-gray-700 rounded-lg mt-4"
            ></div>
          </div>
        </template>

        <!-- ❌ Error -->
        <div v-else-if="error" class="col-span-full text-center text-red-500">
          {{ error }}
        </div>

        <!-- ✅ Job Cards -->
        <div
          v-else
          v-for="job in jobs"
          :key="job.id"
          class="p-6 bg-white dark:bg-card-dark rounded-xl shadow-lg hover:shadow-2xl dark:hover:shadow-lg-dark hover:-translate-y-1 transition-all duration-300 group"
        >
          <div class="flex flex-col gap-4">
            <div class="flex items-center gap-4">
              <div
                class="w-14 h-14 rounded-xl border border-indigo-200 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900 flex items-center justify-center overflow-hidden"
              >
                <img
                  src="/favicon.jpg"
                  alt="Company Logo"
                  class="w-10 h-10 object-cover rounded-lg"
                />
              </div>
              <div>
                <p
                  class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-primary"
                >
                  {{ job.title }}
                </p>
                <p class="text-sm text-gray-500">{{ job.company }}</p>
              </div>
            </div>
            <p class="text-sm text-text-light dark:text-text-dark">
              {{ job.office ?? "Not specified" }}
            </p>
            <p
              class="text-sm text-text-light dark:text-text-dark line-clamp-3"
              v-html="job.description"
            ></p>
            <button
              class="mt-2 w-full bg-primary text-white rounded-lg h-11 px-4 font-bold hover:bg-primary-focus transition"
            >
              Apply Now
            </button>
          </div>
        </div>
      </div>

      <div
        v-if="loading && jobs.length > 0"
        class="flex flex-col items-center justify-center py-10 space-y-4 text-gray-500"
      >
        <div class="flex space-x-2">
          <div
            class="w-3 h-3 bg-primary rounded-full animate-bounce"
            style="animation-delay: 0s"
          ></div>
          <div
            class="w-3 h-3 bg-primary rounded-full animate-bounce"
            style="animation-delay: 0.15s"
          ></div>
          <div
            class="w-3 h-3 bg-primary rounded-full animate-bounce"
            style="animation-delay: 0.3s"
          ></div>
        </div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
          Loading more jobs...
        </p>
      </div>

      <div v-if="!hasMore && !loading" class="text-center text-gray-400 mt-6">
        You’ve reached the end.
      </div>
    </main>
  </div>
  <AiFooterSection />
</template>
<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import JobService from "@/services/jobService";
import OptionsService from "@/services/optionsService";
import AiTheWelcome from "@/components/AiTheWelcome.vue";
import AiFooterSection from "@/components/AiFooter.vue";

const jobs = ref([]);
const page = ref(1);
const hasMore = ref(true);
const loading = ref(false);
const error = ref("");
const search = ref("");

// Filters
const selectedLocation = ref("");
const selectedType = ref("");
const selectedIndustry = ref(""); // store industry ID here
const selectedIndustryLabel = ref(""); // store industry name for display

const showLocationDropdown = ref(false);
const showTypeDropdown = ref(false);
const showIndustryDropdown = ref(false);

const counties = [
  "Nairobi",
  "Mombasa",
  "Kisumu",
  "Nakuru",
  "Kiambu",
  "Machakos",
];
const jobTypes = ["Full-time", "Part-time", "Remote", "Contract", "Internship"];
const industryOptions = ref([]);

// Dropdowns
const toggleLocationDropdown = () => {
  showLocationDropdown.value = !showLocationDropdown.value;
  showTypeDropdown.value = false;
  showIndustryDropdown.value = false;
};
const toggleTypeDropdown = () => {
  showTypeDropdown.value = !showTypeDropdown.value;
  showLocationDropdown.value = false;
  showIndustryDropdown.value = false;
};
const toggleIndustryDropdown = () => {
  showIndustryDropdown.value = !showIndustryDropdown.value;
  showLocationDropdown.value = false;
  showTypeDropdown.value = false;
};

// Selection
const selectLocation = (county) => {
  selectedLocation.value = county;
  showLocationDropdown.value = false;
  reloadJobs();
};
const selectType = (type) => {
  selectedType.value = type;
  showTypeDropdown.value = false;
  reloadJobs();
};
const selectIndustry = (industry) => {
  selectedIndustry.value = industry.id; // ✅ store ID
  selectedIndustryLabel.value = industry.name; // ✅ store label for display
  showIndustryDropdown.value = false;
  reloadJobs();
};

// Clear filters
const clearFilters = () => {
  search.value = "";
  selectedLocation.value = "";
  selectedType.value = "";
  selectedIndustry.value = "";
  selectedIndustryLabel.value = "";
  reloadJobs();
};

// Load jobs
const loadJobs = async () => {
  if (loading.value || !hasMore.value) return;
  loading.value = true;

  try {
    const data = await JobService.getPublicJobs(
      page.value,
      search.value,
      selectedLocation.value,
      selectedType.value,
      selectedIndustry.value // ✅ passing industry ID
    );

    if (data?.data?.length) {
      jobs.value.push(...data.data);
      hasMore.value = data.current_page < data.last_page;
      page.value++;
    } else {
      hasMore.value = false;
    }
  } catch (err) {
    console.error(err);
    error.value = err.response?.data?.message || "Failed to load jobs.";
  } finally {
    loading.value = false;
  }
};

// Reload
const reloadJobs = async () => {
  page.value = 1;
  jobs.value = [];
  hasMore.value = true;
  await loadJobs();
};

// Infinite scroll
const handleScroll = () => {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300)
    loadJobs();
};

// Debounced search
let debounceTimeout;
watch(search, () => {
  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(() => reloadJobs(), 600);
});

onMounted(async () => {
  loadJobs();
  const industries = await OptionsService.getIndustries();
  industryOptions.value = industries.data || industries;
  window.addEventListener("scroll", handleScroll);
});
onUnmounted(() => window.removeEventListener("scroll", handleScroll));
</script>


<style scoped>
@keyframes pulse {
  0%,
  100% {
    opacity: 0.6;
  }
  50% {
    opacity: 1;
  }
}
.animate-pulse {
  animation: pulse 1.6s ease-in-out infinite;
}
</style>
