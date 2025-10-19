<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <section>
      <h2 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4">
        Saved Jobs
      </h2>

      <!-- Loader -->
      <div v-if="loading" class="space-y-4 animate-pulse">
        <div
          v-for="n in 5"
          :key="n"
          class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700"
        >
          <div
            class="h-5 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-3"
          ></div>
          <div
            class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/2 mb-2"
          ></div>
        </div>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="text-center py-10">
        <p class="text-red-600 dark:text-red-400 font-medium mb-4">
          {{ error }}
        </p>
        <button
          @click="fetchJobs"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors"
        >
          Retry
        </button>
      </div>

      <!-- No Jobs -->
      <div v-else-if="jobs.length === 0" class="text-center py-10">
        <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">
          You have no saved jobs yet.
        </p>
        <router-link
          to="/jobs"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors"
          >Browse Jobs</router-link
        >
      </div>

      <!-- Jobs List -->
      <div v-else class="space-y-4">
        <div
          v-for="job in jobs.slice(0, 100)"
          :key="job.id"
          class="bg-card-light dark:bg-card-dark p-4 rounded-2xl border border-gray-200 dark:border-gray-700"
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
                class="flex items-center text-sm text-subtext-light dark:text-subtext-dark space-x-3 mb-2"
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

              <div
                class="text-sm text-subtext-light dark:text-subtext-dark mb-2"
              >
                <span class="font-medium">Saved by:</span>
                {{ job.user_name || "You" }} |
                <span class="font-medium">Saved on:</span>
                {{ formatDate(job.saved_at || job.created_at) }}
              </div>
            </div>

            <!-- Buttons -->
            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-2 mt-3 sm:mt-0">
              <button
                class="px-4 py-2 bg-primary text-white font-semibold rounded-full shadow-md hover:bg-indigo-700 transition-colors flex items-center justify-center whitespace-nowrap"
                @click="openModal(job)"
              >
                View Details
                <span class="material-icons text-base ml-2">arrow_forward</span>
              </button>

              <!-- Apply on behalf -->
              <button
                :class="[
                  'px-4 py-2 font-semibold rounded-full shadow-md flex items-center justify-center whitespace-nowrap transition-colors',
                  job.application_status === 'applied'
                    ? 'bg-gray-400 text-white cursor-not-allowed'
                    : 'bg-green-500 text-white hover:bg-green-600',
                ]"
                :disabled="job.application_status === 'applied'"
                @click="openApplyModal(job)"
              >
                <span class="material-icons text-base mr-2">send</span>
                {{
                  job.application_status === "applied"
                    ? "Applied"
                    : "Apply on behalf"
                }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Job Details Modal -->
    <JobModal v-if="showModal" :job="selectedJob" @close="closeModal" />

    <!-- Apply Modal -->
    <!-- Apply Modal -->
    <div
      v-if="showApplyModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div
        class="bg-white dark:bg-gray-800 p-6 rounded-xl w-full max-w-lg shadow-lg"
      >
        <h3 class="text-xl font-semibold mb-4">
          Apply for {{ applyJob.title }}
        </h3>
        <p class="mb-4 text-sm text-subtext-light dark:text-subtext-dark">
          Applying on behalf of <strong>{{ applyJob.user_name }}</strong>
        </p>

        <!-- Subject -->
        <div class="mb-4">
          <label class="block font-medium mb-1"
            >Subject <span class="text-red-500">*</span></label
          >
          <input
            v-model="applyForm.subject"
            type="text"
            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2"
            placeholder="Enter email subject"
          />
        </div>

        <!-- Email Body -->
        <div class="mb-4">
          <label class="block font-medium mb-1"
            >Email Body <span class="text-red-500">*</span></label
          >
          <textarea
            v-model="applyForm.body"
            rows="5"
            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2"
            placeholder="Enter email body"
          ></textarea>
        </div>

        <!-- CV Upload -->
        <div class="mb-4">
          <label class="block font-medium mb-1"
            >Attach CV <span class="text-red-500">*</span></label
          >
          <input
            type="file"
            @change="handleFileChange($event, 'cv')"
            accept=".pdf,.doc,.docx"
            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2"
          />
          <p v-if="applyForm.cvName" class="text-sm mt-1 text-gray-500">
            Selected file: {{ applyForm.cvName }}
          </p>
        </div>

        <!-- Cover Letter Upload (Optional) -->
        <div class="mb-4">
          <label class="block font-medium mb-1"
            >Attach Cover Letter (Optional)</label
          >
          <input
            type="file"
            @change="handleFileChange($event, 'coverLetter')"
            accept=".pdf,.doc,.docx"
            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2"
          />
          <p
            v-if="applyForm.coverLetterName"
            class="text-sm mt-1 text-gray-500"
          >
            Selected file: {{ applyForm.coverLetterName }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-2">
          <button
            @click="closeApplyModal"
            class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="submitApplication"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
          >
            Send Application
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";
import JobService from "@/services/jobService";
import JobModal from "@/components/Dashboard/modals/JobModal.vue";

const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const selectedJob = ref(null);
const showModal = ref(false);

const showApplyModal = ref(false);
const applyJob = ref(null);

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString();
}

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const data = await JobService.getSavedJobs();
    if (Array.isArray(data.data)) jobs.value = data.data;
    else jobs.value = [];
  } catch (err) {
    error.value =
      err.response?.status === 403
        ? "Access denied. Upgrade your plan to access jobs."
        : "Error fetching jobs. Please try again later.";
  } finally {
    loading.value = false;
  }
}

function openModal(job) {
  selectedJob.value = job;
  showModal.value = true;
}

function closeModal() {
  selectedJob.value = null;
  showModal.value = false;
}

function openApplyModal(job) {
  applyJob.value = job;
  applyForm.value.subject = "";
  applyForm.value.body = "";
  showApplyModal.value = true;
}

function closeApplyModal() {
  applyJob.value = null;
  showApplyModal.value = false;
}

const applyForm = ref({
  subject: "",
  body: "",
  cv: null,
  coverLetter: null,
  cvName: "",
  coverLetterName: "",
});

function handleFileChange(event, type) {
  const file = event.target.files[0];
  if (!file) return;

  if (type === "cv") {
    applyForm.value.cv = file;
    applyForm.value.cvName = file.name;
  } else if (type === "coverLetter") {
    applyForm.value.coverLetter = file;
    applyForm.value.coverLetterName = file.name;
  }
}

async function submitApplication() {
  if (
    !applyForm.value.subject ||
    !applyForm.value.body ||
    !applyForm.value.cv
  ) {
    Swal.fire(
      "Validation Error",
      "Subject, body, and CV are required.",
      "warning"
    );
    return;
  }

  try {
    const formData = new FormData();
    formData.append("user_id", applyJob.value.user_id);
    formData.append("subject", applyForm.value.subject);
    formData.append("body", applyForm.value.body);
    formData.append("cv", applyForm.value.cv);
    if (applyForm.value.coverLetter)
      formData.append("cover_letter", applyForm.value.coverLetter);

    const res = await JobService.applyOnBehalf(applyJob.value.id, formData);
    Swal.fire({
      icon: "success",
      title: "Application Sent",
      text:
        res.data?.message ||
        `Application sent on behalf of ${applyJob.value.user_name}`,
      timer: 2000,
      showConfirmButton: false,
    });

    closeApplyModal();
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Failed",
      text:
        err.response?.data?.message ||
        "Could not send application. Please try again.",
    });
  }
}

onMounted(fetchJobs);
</script>
