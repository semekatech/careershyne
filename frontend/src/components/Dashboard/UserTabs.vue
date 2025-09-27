<template>
  <div>
    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
      <nav aria-label="Tabs" class="-mb-px flex space-x-8">
        <a href="#" @click.prevent="activeTab = 'jobs'" :class="tabClass('jobs')">Featured Jobs</a>
        <a href="#" @click.prevent="activeTab = 'cv'" :class="tabClass('cv')">CV Revamp</a>
        <a href="#" @click.prevent="activeTab = 'cover'" :class="tabClass('cover')">Cover Letters</a>
        <a href="#" @click.prevent="activeTab = 'email'" :class="tabClass('email')">Email Templates</a>
        <a href="#" @click.prevent="activeTab = 'interviews'" :class="tabClass('interviews')">Interviews</a>
      </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left content -->
      <div class="lg:col-span-2 bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm">
        <!-- Jobs Tab -->
        <div v-if="activeTab === 'jobs'">
          <h2 class="text-2xl font-semibold mb-6">Featured Jobs</h2>

          <div class="space-y-6">
            <div
              v-for="job in jobs.slice(0, 5)"
              :key="job.id"
              class="border rounded p-4 hover:shadow transition flex flex-col gap-4"
            >
              <!-- Job Details -->
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-blue-600">{{ job.title }}</h3>
                <p class="text-gray-600">{{ job.company }} - {{ job.type }}</p>
                <p class="text-gray-500 text-sm">
                  Location: {{ job.county }}, {{ job.country }} | Deadline: {{ formatDate(job.deadline) }}
                </p>
                <!-- <span
                  :class="[
                    'px-3 py-1 rounded text-xs font-semibold mt-1 inline-block',
                    new Date(job.deadline) >= new Date()
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ new Date(job.deadline) >= new Date() ? 'Active' : 'Expired' }}
                </span> -->
              </div>

              <!-- Actions at bottom -->
              <div class="flex flex-wrap gap-2 mt-4">
                <button
                  @click="checkEligibility(job)"
                  class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition text-sm flex-1"
                >
                  Check Eligibility
                </button>
                <button
                  @click="revampCV(job)"
                  class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition text-sm flex-1"
                >
                  CV Revamp
                </button>
                <button
                  @click="generateCoverLetter(job)"
                  class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-sm flex-1"
                >
                  Cover Letter
                </button>
                <button
                  @click="generateEmailTemplate(job)"
                  class="bg-purple-500 text-white px-3 py-1 rounded hover:bg-purple-600 transition text-sm flex-1"
                >
                  Email Template
                </button>
                <button
                  @click="openModal(job)"
                  class="px-4 py-2 bg-white text-black rounded border border-[#fd624e] transition text-sm flex-1"
                >
                  View Details
                </button>
              </div>
            </div>
          </div>

          <!-- Job Modal -->
          <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-start z-50 overflow-auto pt-4 md:pt-10"
            @click="closeModal"
          >
            <div
              class="bg-white w-full md:max-w-3xl h-[95vh] md:h-[90vh] relative mx-2 md:mx-0 rounded shadow-lg flex flex-col"
              @click.stop
            >
              <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h2 class="text-2xl font-semibold">{{ selectedJob.title }}</h2>
                <button @click="closeModal" class="text-gray-700 hover:text-gray-900 text-2xl font-bold">✕</button>
              </div>
              <div class="p-4 md:p-6 overflow-x-hidden overflow-y-auto flex-1 w-full">
                <p class="text-gray-600 mb-2">{{ selectedJob.company }} - {{ selectedJob.type }}</p>
                <p class="text-gray-500 mb-2">Location: {{ selectedJob.county }}, {{ selectedJob.country }}</p>
                <p class="text-gray-500 mb-2">Deadline: {{ formatDate(selectedJob.deadline) }}</p>
                <p class="text-gray-700 mb-2"><strong>Experience:</strong> {{ selectedJob.experience }} years</p>
                <p class="text-gray-700 mb-2"><strong>Education:</strong> {{ selectedJob.education }}</p>
                <p class="text-gray-700 mb-2"><strong>Salary:</strong> {{ selectedJob.salary }}</p>
                <p class="text-gray-700 mb-2"><strong>Field:</strong> {{ selectedJob.field }}</p>
                <p class="text-gray-700 mb-2"><strong>Description:</strong></p>
                <div v-html="selectedJob.description" class="prose mb-4 max-w-full"></div>
                <p class="text-gray-700 mb-2"><strong>Application Instructions:</strong></p>
                <div v-html="selectedJob.applicationInstructions" class="prose max-w-full"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- CV Revamp Tab -->
        <div v-else-if="activeTab === 'cv'">
          <h3 class="text-xl font-semibold mb-4">Revamp Your CV with AI</h3>
          <p class="text-muted-light dark:text-muted-dark mb-6">
            Upload your current CV, and our AI will provide suggestions to improve it.
          </p>
          <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-12 text-center">
            <span class="material-icons-sharp text-5xl text-primary mb-4">upload_file</span>
            <p class="font-semibold mb-2">Drag and drop your CV here</p>
            <p class="text-muted-light dark:text-muted-dark text-sm mb-4">PDF, DOC, DOCX up to 5MB</p>
            <button
              class="bg-gray-200 dark:bg-gray-600 text-text-light dark:text-text-dark font-medium py-2 px-4 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
            >
              Or browse files
            </button>
          </div>
          <textarea
            class="w-full mt-6 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-4 focus:ring-primary focus:border-primary"
            placeholder="Or paste your CV content here..."
            rows="8"
          ></textarea>
        </div>

        <!-- Other tabs -->
        <div v-else-if="activeTab === 'cover'">Cover Letters content...</div>
        <div v-else-if="activeTab === 'email'">Email Templates content...</div>
        <div v-else-if="activeTab === 'interviews'">Interviews content...</div>
      </div>

      <!-- Recent Activity Card -->
      <div class="bg-surface-light dark:bg-surface-dark p-6 rounded-lg shadow-sm self-start">
        <h3 class="text-xl font-semibold mb-4">Recent Activity</h3>
        <div class="space-y-4">
          <div class="flex items-start">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-full">
              <span class="material-icons-sharp text-green-600 dark:text-green-400">check_circle</span>
            </div>
            <div class="ml-4">
              <p class="font-medium">CV for "Software Engineer" revamped</p>
              <p class="text-sm text-muted-light dark:text-muted-dark">2 hours ago</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
              <span class="material-icons-sharp text-blue-600 dark:text-blue-400">edit</span>
            </div>
            <div class="ml-4">
              <p class="font-medium">Cover letter for "Acme Corp" created</p>
              <p class="text-sm text-muted-light dark:text-muted-dark">1 day ago</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
              <span class="material-icons-sharp text-yellow-600 dark:text-yellow-400">history</span>
            </div>
            <div class="ml-4">
              <p class="font-medium">Email template "Follow-up" used</p>
              <p class="text-sm text-muted-light dark:text-muted-dark">3 days ago</p>
            </div>
          </div>
          <a class="text-primary font-medium text-sm hover:underline" href="#">View all activity</a>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import JobService from "@/services/jobService";

const activeTab = ref("jobs");
const jobs = ref([]);
const showModal = ref(false);
const selectedJob = ref({});

// Fetch jobs
async function fetchJobs() {
  try {
    const data = await JobService.getJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    console.error("Error fetching jobs:", err);
    jobs.value = [];
  }
}

// Modal
function openModal(job) { selectedJob.value = job; showModal.value = true; }
function closeModal() { showModal.value = false; }
function formatDate(dateString) { return new Date(dateString).toLocaleDateString(); }

// Job-specific actions
function checkEligibility(job) { console.log('Check eligibility for:', job.title); }
function revampCV(job) { console.log('Revamp CV for:', job.title); }
function generateCoverLetter(job) { console.log('Generate cover letter for:', job.title); }
function generateEmailTemplate(job) { console.log('Generate email template for:', job.title); }

// Tabs
const tabClass = (tab) => [
  "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm",
  activeTab.value === tab
    ? "border-primary text-primary"
    : "border-transparent text-muted-light dark:text-muted-dark hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600",
];

onMounted(fetchJobs);
</script>
