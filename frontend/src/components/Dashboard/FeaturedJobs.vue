<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Featured Jobs -->
    <section>
      <h2 class="text-2xl font-bold text-gray-800 mb-6">Featured Jobs</h2>

      <!-- Loader -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg
          class="animate-spin h-8 w-8 text-indigo-600"
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
          />
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8v8H4z"
          />
        </svg>
      </div>

      <!-- Jobs List -->
      <div v-else class="space-y-5">
        <div
          v-for="job in jobs.slice(0, 5)"
          :key="job.id"
          class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow border border-gray-200 dark:border-gray-700"
        >
          <!-- Job Header -->
          <div
            class="flex flex-col sm:flex-row justify-between items-start mb-4"
          >
            <div>
              <h3 class="text-lg font-semibold text-indigo-700 mb-1">
                {{ job.title }} - {{ job.county }}, {{ job.country }}
              </h3>
              <p class="text-gray-500 dark:text-gray-400 mb-1">
                {{ job.company }} - {{ job.type }}
              </p>
              <div
                class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400 space-x-4"
              >
                <div class="flex items-center">
                  <span class="material-icons text-base mr-1">location_on</span>
                  <span>{{ job.county }}, {{ job.country }}</span>
                </div>
                <div class="flex items-center">
                  <span class="material-icons text-base mr-1">event</span>
                  <span>Deadline: {{ formatDate(job.deadline) }}</span>
                </div>
              </div>
            </div>

            <!-- View Details Button -->
            <button
              class="mt-3 sm:mt-0 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-full shadow hover:bg-indigo-700 transition"
              @click="openModal(job)"
            >
              View Details
              <span class="material-icons text-base ml-2">arrow_forward</span>
            </button>
          </div>

          <!-- Action Buttons -->
          <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <button
                @click="openEligibility(job)"
                class="flex items-center justify-center py-2 px-3 border border-green-500 text-green-600 font-medium rounded-lg hover:bg-green-50 dark:hover:bg-green-900 transition"
              >
                <span class="material-icons text-lg mr-1">check_circle</span>
                Check Eligibility
              </button>
              <button
                @click="$emit('revamp-cv', job)"
                class="flex items-center justify-center py-2 px-3 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition"
              >
                <span class="material-icons text-lg mr-1">description</span>
                CV Revamp
              </button>
              <button
                @click="$emit('generate-cover', job)"
                class="flex items-center justify-center py-2 px-3 border border-yellow-500 text-yellow-600 font-medium rounded-lg hover:bg-yellow-50 dark:hover:bg-yellow-900 transition"
              >
                <span class="material-icons text-lg mr-1">mail</span>
                Cover Letter
              </button>
              <button
                @click="$emit('generate-email', job)"
                class="flex items-center justify-center py-2 px-3 border border-purple-500 text-purple-600 font-medium rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900 transition"
              >
                <span class="material-icons text-lg mr-1">drafts</span>
                Email Template
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Job Details Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-10"
      @click="closeModal"
    >
      <div
        class="bg-white w-full md:max-w-3xl h-[90vh] relative mx-4 rounded-xl shadow-xl flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div
          class="flex justify-between items-center p-5 border-b sticky top-0 bg-white rounded-t-xl z-10"
        >
          <h2 class="text-2xl font-bold">{{ selectedJob?.title }}</h2>
          <button
            @click="closeModal"
            class="text-gray-500 hover:text-gray-800 text-2xl font-bold"
          >
            ✕
          </button>
        </div>

        <!-- Content -->
        <div class="p-5 overflow-y-auto flex-1">
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

          <h3 class="text-lg font-semibold mt-4 mb-2">Description</h3>
          <div v-html="selectedJob.description" class="prose max-w-full mb-4" />

          <h3 class="text-lg font-semibold mb-2">Application Instructions</h3>
          <div
            v-html="selectedJob.applicationInstructions"
            class="prose max-w-full"
          />
        </div>
      </div>
    </div>

    <!-- Eligibility Modal -->
    <div
      v-if="showEligibilityModal"
      class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-10"
      @click="closeEligibility"
    >
      <div
        class="bg-white w-full md:max-w-4xl h-[90vh] relative mx-4 rounded-xl shadow-xl flex flex-col"
        @click.stop
      >
        <!-- Header -->
        <div
          class="flex justify-between items-center p-5 border-b sticky top-0 bg-white rounded-t-xl z-10"
        >
          <h2 class="text-2xl font-bold text-gray-800">
            Eligibility Check – {{ selectedJob?.title }}
          </h2>
          <button
            @click="closeEligibility"
            class="text-gray-500 hover:text-gray-800 text-2xl font-bold"
          >
            ✕
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto flex-1">
          <!-- Loading -->
          <div
            v-if="!eligibilityResult || eligibilityProgress < 100"
            class="flex flex-col items-center justify-center h-full text-center"
          >
            <p class="mb-4 text-gray-600">
              Comparing your CV with
              <strong>{{ selectedJob?.title }}</strong>...
            </p>
            <div class="w-3/4 bg-gray-200 rounded-full h-4">
              <div
                class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                :style="{ width: eligibilityProgress + '%' }"
              ></div>
            </div>
            <p class="mt-3 text-gray-700 font-medium">
              {{ eligibilityProgress }}%
            </p>
          </div>

          <!-- Results -->
          <div v-else class="space-y-8">
            <!-- Match Percentage -->
            <div class="flex flex-col items-center">
              <div
                class="relative flex items-center justify-center w-32 h-32 rounded-full border-8 shadow-md"
                :class="{
                  'border-green-500': eligibilityResult.matchPercentage >= 70,
                  'border-yellow-500': eligibilityResult.matchPercentage >= 40 &&
                    eligibilityResult.matchPercentage < 70,
                  'border-red-500': eligibilityResult.matchPercentage < 40
                }"
              >
                <span class="text-3xl font-bold text-gray-800">
                  {{ eligibilityResult.matchPercentage }}%
                </span>
              </div>
              <p class="mt-2 text-gray-600 font-medium">Match Score</p>
            </div>

            <!-- Matched Skills -->
            <div
              v-if="normalized.matchedSkills.length"
              class="bg-green-50 border border-green-200 rounded-lg p-5 shadow"
            >
              <h3 class="font-semibold text-green-700 mb-3 text-lg">
                ✅ Matched Skills
              </h3>
              <ul class="space-y-3">
                <li
                  v-for="(s, i) in normalized.matchedSkills"
                  :key="'m' + i"
                  class="p-3 bg-white rounded-md shadow-sm"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <div class="text-sm font-medium text-gray-800">
                        {{ s.skill }}
                      </div>
                      <div class="text-xs text-gray-500 mt-1">
                        {{ s.description || "No description provided." }}
                      </div>
                    </div>
                    <span
                      class="text-xs font-semibold px-2 py-1 rounded text-white"
                      :class="{
                        'bg-green-600': s.relevance === 'high',
                        'bg-yellow-500': s.relevance === 'medium',
                        'bg-gray-400': !s.relevance || s.relevance === 'low'
                      }"
                    >
                      {{ (s.relevance || "low").toUpperCase() }}
                    </span>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Missing Skills -->
            <div
              v-if="normalized.missingSkills.length"
              class="bg-red-50 border border-red-200 rounded-lg p-5 shadow"
            >
              <h3 class="font-semibold text-red-700 mb-3 text-lg">
                ❌ Missing Skills
              </h3>
              <ul class="space-y-3">
                <li
                  v-for="(s, i) in normalized.missingSkills"
                  :key="'mm' + i"
                  class="p-3 bg-white rounded-md shadow-sm"
                >
                  <div class="flex justify-between items-start">
                    <div>
                      <div class="text-sm font-medium text-gray-800">
                        {{ s.skill }}
                      </div>
                      <div class="text-xs text-gray-500 mt-1">
                        {{ s.description || "No description provided." }}
                      </div>
                    </div>
                    <span
                      class="text-xs font-semibold px-2 py-1 rounded text-white"
                      :class="{
                        'bg-red-600': s.importance === 'high',
                        'bg-yellow-500': s.importance === 'medium',
                        'bg-gray-400': !s.importance || s.importance === 'low'
                      }"
                    >
                      {{ (s.importance || "low").toUpperCase() }}
                    </span>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Recommendations -->
            <div
              v-if="normalized.recommendations?.length"
              class="bg-blue-50 border border-blue-200 rounded-lg p-5 shadow"
            >
              <h3 class="font-semibold text-blue-700 mb-3 text-lg">
                💡 Recommendations
              </h3>
              <ul class="list-disc list-inside text-sm text-gray-700">
                <li v-for="(rec, idx) in normalized.recommendations" :key="idx">
                  {{ rec }}
                </li>
              </ul>
            </div>

            <!-- Overall Assessment -->
            <div
              v-if="normalized.overallAssessment"
              class="bg-gray-50 border border-gray-200 rounded-lg p-5 shadow"
            >
              <h3 class="font-semibold text-gray-800 mb-3 text-lg">
                📌 Overall Assessment
              </h3>
              <p class="text-sm text-gray-700">
                {{ normalized.overallAssessment }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import JobService from "@/services/jobService";
import eligibilityService from "@/services/eligibilityService";

const jobs = ref([]);
const loading = ref(true);

const showModal = ref(false);
const selectedJob = ref(null);

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
    eligibilityResult.value = result.analysis; // expect backend "analysis" field
  } catch (error) {
    clearInterval(interval);
    eligibilityProgress.value = 100;
    eligibilityResult.value = {
      matchPercentage: 0,
      matchedSkills: [],
      missingSkills: [],
      recommendations: [],
      overallAssessment: "Failed to analyze eligibility."
    };
  }
}

function closeEligibility() {
  showEligibilityModal.value = false;
  eligibilityProgress.value = 0;
  eligibilityResult.value = null;
}

const normalized = computed(() => ({
  matchPercentage: eligibilityResult.value?.matchPercentage || 0,
  matchedSkills: eligibilityResult.value?.matchedSkills || [],
  missingSkills: eligibilityResult.value?.missingSkills || [],
  recommendations: eligibilityResult.value?.recommendations || [],
  overallAssessment: eligibilityResult.value?.overallAssessment || ""
}));

onMounted(fetchJobs);
</script>
