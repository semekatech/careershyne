<template>
  <div>
    <div>
      <section>
        <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">
          Featured Jobs
        </h2>

        <!-- Loader -->
        <div v-if="loading" class="flex justify-center items-center py-20">
          <svg
            class="animate-spin h-8 w-8 text-primary"
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
              d="M4 12a8 8 0 018-8v8H4z"
            ></path>
          </svg>
        </div>

        <!-- Jobs List -->
        <div v-else class="space-y-4">
          <div
            v-for="job in jobs.slice(0, 5)"
            :key="job.id"
            class="bg-card-light dark:bg-card-dark p-4 rounded-lg shadow-md border border-border-light dark:border-border-dark"
          >
            <div
              class="flex flex-col sm:flex-row justify-between items-start mb-3"
            >
              <div>
                <h3 class="text-lg font-semibold text-primary mb-1">
                  {{ job.title }} - {{ job.county }}, {{ job.country }}
                </h3>
                <p class="text-subtext-light dark:text-subtext-dark mb-1">
                  {{ job.company }} - {{ job.type }}
                </p>
                <div
                  class="flex items-center text-sm text-subtext-light dark:text-subtext-dark space-x-3"
                >
                  <div class="flex items-center">
                    <span class="material-icons text-base mr-1"
                      >location_on</span
                    >
                    <span>{{ job.county }}, {{ job.country }}</span>
                  </div>
                  <div class="flex items-center">
                    <span class="material-icons text-base mr-1">event</span>
                    <span>Deadline: {{ formatDate(job.deadline) }}</span>
                  </div>
                </div>
              </div>

              <!-- OPEN DETAILS MODAL -->
              <button
                class="mt-3 sm:mt-0 px-4 py-2 bg-primary text-white font-semibold rounded-full shadow-md hover:bg-indigo-700 transition-colors flex items-center whitespace-nowrap"
                @click="openModal(job)"
              >
                View Details
                <span class="material-icons text-base ml-2">arrow_forward</span>
              </button>
            </div>

            <!-- Action Buttons -->
            <div
              class="border-t border-border-light dark:border-border-dark pt-3"
            >
              <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2">
                <button
                  @click="openEligibility(job)"
                  class="flex items-center justify-center py-2 px-3 border border-green-500 text-green-500 font-semibold rounded-lg hover:bg-green-50 dark:hover:bg-green-900 transition-colors text-center"
                >
                  <span class="material-icons text-lg mr-1">check_circle</span>
                  Check Eligibility
                </button>
                <button
                  @click="openCvRevamp(job)"
                  class="flex items-center justify-center py-2 px-3 border border-blue-500 text-blue-500 font-semibold rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors text-center"
                >
                  <span class="material-icons text-lg mr-1">description</span>
                  CV Revamp
                </button>
                <button
                  @click="$emit('generate-cover', job)"
                  class="flex items-center justify-center py-2 px-3 border border-yellow-500 text-yellow-500 font-semibold rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900 transition-colors text-center"
                >
                  <span class="material-icons text-lg mr-1">mail</span>
                  Cover Letter
                </button>
                <button
                  @click="$emit('generate-email', job)"
                  class="flex items-center justify-center py-2 px-3 border border-purple-500 text-purple-500 font-semibold rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900 transition-colors text-center"
                >
                  <span class="material-icons text-lg mr-1">drafts</span>
                  Email Template
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- DETAILS MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
      @click="closeModal"
    >
      <div
        class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col"
        @click.stop
      >
        <div
          class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10"
        >
          <h2 class="text-2xl font-semibold">{{ selectedJob?.title }}</h2>
          <button
            @click="closeModal"
            class="text-gray-700 hover:text-gray-900 text-2xl font-bold"
          >
            ✕
          </button>
        </div>

        <div
          class="p-4 md:p-6 overflow-x-hidden overflow-y-auto flex-1 w-full"
          v-if="selectedJob"
        >
          <p class="text-gray-600 mb-2">
            {{ selectedJob.company }} - {{ selectedJob.type }}
          </p>
          <p class="text-gray-500 mb-2">
            Location: {{ selectedJob.county }}, {{ selectedJob.country }}
          </p>
          <p class="text-gray-500 mb-2">
            Deadline: {{ formatDate(selectedJob.deadline) }}
          </p>
          <p class="text-gray-700 mb-2">
            <strong>Experience:</strong> {{ selectedJob.experience }} years
          </p>
          <p class="text-gray-700 mb-2">
            <strong>Education:</strong> {{ selectedJob.education }}
          </p>
          <p class="text-gray-700 mb-2">
            <strong>Salary:</strong> {{ selectedJob.salary }}
          </p>
          <p class="text-gray-700 mb-2">
            <strong>Field:</strong> {{ selectedJob.field }}
          </p>
          <p class="text-gray-700 mb-2"><strong>Description:</strong></p>
          <div
            v-html="selectedJob.description"
            class="prose mb-4 max-w-full"
          ></div>
          <p class="text-gray-700 mb-2">
            <strong>Application Instructions:</strong>
          </p>
          <div
            v-html="selectedJob.applicationInstructions"
            class="prose max-w-full"
          ></div>
        </div>
      </div>
    </div>

    <!-- ELIGIBILITY MODAL -->
    <!-- ELIGIBILITY MODAL -->
    <div
      v-if="showEligibilityModal"
      class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
      @click="closeEligibility"
    >
      <div
        class="bg-white w-full md:max-w-4xl h-[80vh] relative mx-2 md:mx-0 rounded-2xl shadow-xl flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div
          class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl"
        >
          <h2 class="text-2xl font-bold text-gray-800">Eligibility Check</h2>
          <button
            @click="closeEligibility"
            class="text-gray-500 hover:text-gray-800 text-2xl font-bold"
          >
            ✕
          </button>
        </div>

        <!-- Progress & Results -->
        <div class="flex-1 p-6 overflow-y-auto">
          <!-- Loading state -->
          <div
            v-if="!eligibilityResult || eligibilityProgress < 100"
            class="flex flex-col items-center justify-center h-full text-center"
          >
            <p class="mb-4 text-gray-600">
              Comparing your CV with <strong>{{ selectedJob?.title }}</strong
              >...
            </p>

            <!-- Linear Progress -->
            <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
              <div
                class="bg-green-500 h-4 rounded-full transition-all duration-500"
                :style="{ width: eligibilityProgress + '%' }"
              ></div>
            </div>
            <p class="mt-3 text-gray-700 font-medium">
              {{ eligibilityProgress }}%
            </p>
          </div>

          <!-- Results -->
          <div v-else class="space-y-8">
            <!-- Circular Match Percentage -->
            <div class="flex flex-col items-center">
              <div
                class="relative flex items-center justify-center w-32 h-32 rounded-full border-8 shadow-md"
                :class="
                  eligibilityResult.matchPercentage >= 70
                    ? 'border-green-500'
                    : eligibilityResult.matchPercentage >= 40
                    ? 'border-yellow-500'
                    : 'border-red-500'
                "
              >
                <span class="text-3xl font-bold text-gray-800">
                  {{ eligibilityResult.matchPercentage }}%
                </span>
              </div>
              <p class="mt-2 text-gray-600 font-medium">Match Score</p>
            </div>

            <!-- Matched Skills -->
            <div
              v-if="eligibilityResult.matchedSkills?.length"
              class="bg-green-50 border border-green-200 rounded-lg p-5 shadow-sm"
            >
              <h3 class="font-semibold text-green-700 mb-3 text-lg">
                ✅ Matched Skills
              </h3>
              <ul
                class="list-disc list-inside text-sm text-gray-700 grid grid-cols-1 sm:grid-cols-2 gap-x-6"
              >
                <li
                  v-for="(skill, index) in eligibilityResult.matchedSkills"
                  :key="index"
                >
                  {{ skill }}
                </li>
              </ul>
            </div>

            <!-- Missing Skills -->
            <div
              v-if="eligibilityResult.missingSkills?.length"
              class="bg-red-50 border border-red-200 rounded-lg p-5 shadow-sm"
            >
              <h3 class="font-semibold text-red-700 mb-3 text-lg">
                ❌ Missing Skills
              </h3>
              <ul
                class="list-disc list-inside text-sm text-red-600 grid grid-cols-1 sm:grid-cols-2 gap-x-6"
              >
                <li
                  v-for="(skill, index) in eligibilityResult.missingSkills"
                  :key="index"
                >
                  {{ skill }}
                </li>
              </ul>
            </div>

            <!-- Recommendations -->
            <div
              v-if="eligibilityResult.recommendations"
              class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm"
            >
              <h3 class="font-semibold text-blue-700 mb-3 text-lg">
                💡 Recommendations
              </h3>
              <p class="text-sm text-gray-700 leading-relaxed">
                {{ eligibilityResult.recommendations }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- CV REVAMP -->
    <!-- CV REVAMP MODAL -->
    <div
      v-if="showCvRevampModal"
      class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
      @click="closeCvRevamp"
    >
      <div
        class="bg-white w-full md:max-w-4xl h-[80vh] relative mx-2 md:mx-0 rounded-2xl shadow-xl flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div
          class="flex justify-between items-center p-5 border-b sticky top-0 bg-white z-10 rounded-t-2xl"
        >
          <h2 class="text-2xl font-bold text-gray-800">CV Revamp</h2>
          <button
            @click="closeCvRevamp"
            class="text-gray-500 hover:text-gray-800 text-2xl font-bold"
          >
            ✕
          </button>
        </div>

        <!-- Content -->
        <div class="flex-1 p-6 overflow-y-auto">
          <!-- Loading -->
          <div
            v-if="!cvRevampResult || cvRevampProgress < 100"
            class="flex flex-col items-center justify-center h-full text-center"
          >
            <p class="mb-4 text-gray-600">
              Revamping your CV for <strong>{{ selectedJob?.title }}</strong
              >...
            </p>

            <!-- Progress -->
            <div class="w-3/4 bg-gray-200 rounded-full h-4 dark:bg-gray-700">
              <div
                class="bg-blue-500 h-4 rounded-full transition-all duration-500"
                :style="{ width: cvRevampProgress + '%' }"
              ></div>
            </div>
            <p class="mt-3 text-gray-700 font-medium">
              {{ cvRevampProgress }}%
            </p>
          </div>

          <!-- Results -->
        <!-- Results -->
<div v-else class="space-y-6">
  <h3 class="text-xl font-semibold text-gray-800 mb-2">
    Revamped CV
  </h3>

  <!-- Show revamped CV -->
  <div
    v-if="cvRevampResult.revampedCv"
    class="prose max-w-full text-gray-700 bg-gray-50 p-4 rounded-lg shadow-sm"
    v-html="cvRevampResult.revampedCv"
  ></div>

  <!-- Recommendations -->
  <div
    v-if="cvRevampResult.recommendations?.length"
    class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow-sm"
  >
    <h3 class="font-semibold text-blue-700 mb-3 text-lg">
      💡 Recommendations
    </h3>
    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
      <li v-for="(rec, idx) in cvRevampResult.recommendations" :key="idx">
        {{ rec }}
      </li>
    </ul>
  </div>

  <!-- Error fallback -->
  <div
    v-else-if="cvRevampResult.error"
    class="text-red-600 font-medium bg-red-50 border border-red-200 p-4 rounded-lg"
  >
    {{ cvRevampResult.error }}
  </div>
</div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import JobService from "@/services/jobService";
import eligibilityService from "@/services/eligibilityService";
import cvRevampService from "@/services/cvRevamp";
const jobs = ref([]);
const loading = ref(true);

const showModal = ref(false);
const selectedJob = ref(null);
const showCvRevampModal = ref(false);
const cvRevampProgress = ref(0);
const cvRevampResult = ref(null);
const showEligibilityModal = ref(false);
const eligibilityProgress = ref(0);
const eligibilityResult = ref(null);

async function fetchJobs() {
  loading.value = true;
  try {
    const data = await JobService.getJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    console.error("Error fetching jobs:", err);
    jobs.value = [];
  } finally {
    loading.value = false;
  }
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}

function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
}

async function openEligibility(job) {
  selectedJob.value = job;
  showEligibilityModal.value = true;
  eligibilityProgress.value = 0;
  eligibilityResult.value = null;

  try {
    // Fake progress while waiting for backend
    const interval = setInterval(() => {
      if (eligibilityProgress.value < 90) {
        eligibilityProgress.value += 10;
      }
    }, 400);

    const result = await eligibilityService.checkEligibility(job.id);

    clearInterval(interval);
    eligibilityProgress.value = 100;
    eligibilityResult.value = result;
  } catch (error) {
    eligibilityProgress.value = 100;
    eligibilityResult.value = { error: "Failed to check eligibility." };
  }
}

function closeEligibility() {
  showEligibilityModal.value = false;
  eligibilityProgress.value = 0;
  eligibilityResult.value = null;
}
async function openCvRevamp(job) {
  selectedJob.value = job;
  showCvRevampModal.value = true;
  cvRevampProgress.value = 0;
  cvRevampResult.value = null;

  try {
    // Fake progress animation
    const interval = setInterval(() => {
      if (cvRevampProgress.value < 90) {
        cvRevampProgress.value += 10;
      }
    }, 400);

    const result = await cvRevampService.revamp(job.id);

    clearInterval(interval);
    cvRevampProgress.value = 100;
    cvRevampResult.value = result;
  } catch (err) {
    cvRevampProgress.value = 100;
    cvRevampResult.value = { error: "Failed to revamp CV." };
  }
}

function closeCvRevamp() {
  showCvRevampModal.value = false;
  cvRevampProgress.value = 0;
  cvRevampResult.value = null;
}
onMounted(fetchJobs);
</script>
