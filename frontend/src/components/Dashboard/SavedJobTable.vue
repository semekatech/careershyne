<template>
  <div class="w-full p-6 sm:p-8">
    <!-- Card Container -->
    <div
      class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden"
    >
      <section class="p-6 sm:p-8">
        <!-- Header -->
        <div
          class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-8"
        >
          <div>
            <h2
              class="text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight"
            >
              Saved Jobs
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">
              Manage and apply to jobs you’ve saved.
            </p>
          </div>
        </div>

        <!-- Tabs -->
        <div
          class="inline-flex items-center bg-gray-100 dark:bg-gray-800 rounded-full p-1 mb-4 sm:mb-8 gap-4"
        >
          <button
            @click="activeTab = 'pending'"
            :class="[
              'px-5 py-2 rounded-full text-sm font-semibold transition-all',
              activeTab === 'pending'
                ? 'bg-indigo-600 text-white shadow-md'
                : 'text-gray-600 dark:text-gray-300 hover:text-indigo-600',
            ]"
          >
            Pending
          </button>
          <button
            @click="activeTab = 'applied'"
            :class="[
              'px-5 py-2 rounded-full text-sm font-semibold transition-all',
              activeTab === 'applied'
                ? 'bg-indigo-600 text-white shadow-md'
                : 'text-gray-600 dark:text-gray-300 hover:text-indigo-600',
            ]"
          >
            Applied
          </button>
        </div>

        <!-- Search -->
        <div class="mb-6">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by user name..."
            class="w-full sm:w-1/2 rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 dark:bg-gray-800 dark:text-gray-100 transition"
          />
        </div>

        <!-- Loader -->
        <div v-if="loading" class="space-y-4 animate-pulse">
          <div
            v-for="n in 5"
            :key="n"
            class="bg-gray-100 dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700"
          >
            <div class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-2/3 mb-3" />
            <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/2" />
          </div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="text-center py-16">
          <p class="text-red-600 dark:text-red-400 font-medium mb-4 text-lg">
            {{ error }}
          </p>
          <button
            @click="fetchJobs"
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition"
          >
            Retry
          </button>
        </div>

        <!-- Empty -->
        <div
          v-else-if="filteredJobs.length === 0"
          class="text-center py-16 text-gray-600 dark:text-gray-400"
        >
          <p class="text-lg mb-4 font-medium">
            No {{ activeTab === "pending" ? "pending" : "applied" }} jobs found.
          </p>
          <router-link
            to="/jobs"
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition"
          >
            Browse Jobs
          </router-link>
        </div>

        <!-- Jobs List -->
        <!-- Jobs List -->
        <div v-else class="grid gap-5">
          <div
            v-for="job in filteredJobs"
            :key="job.id"
            class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-sm hover:shadow-lg transition-shadow p-6 sm:p-7"
          >
            <div
              class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
              <div class="flex-1">
                <h3
                  class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-1"
                >
                  {{ job.title }}
                </h3>

                <p
                  class="text-gray-500 dark:text-gray-400 text-sm mb-3 flex items-center gap-1"
                >
                  <span class="material-icons text-base">business</span>
                  {{ job.company }} • {{ job.type }}
                </p>

                <p
                  class="text-gray-500 dark:text-gray-400 text-sm mb-3 flex items-center gap-1"
                >
                  <span class="material-icons text-base">person</span>
                  {{ job.user_name }}
                </p>

                <div
                  class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400 gap-4"
                >
                  <div class="flex items-center gap-1">
                    <span class="material-icons text-base">place</span>
                    {{ job.county }}, {{ job.country }}
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="material-icons text-base">event</span>
                    <span v-if="activeTab === 'pending'"
                      >Saved On: {{ formatDate(job.saved_on) }}</span
                    >
                    <span v-else
                      >Applied On: {{ formatDate(job.applied_on) }}</span
                    >
                  </div>
                  <div
                    class="flex items-center gap-1"
                    v-if="activeTab === 'pending'"
                  >
                    <span class="material-icons text-base">event</span>
                    Deadline: {{ formatDate(job.deadline) }}
                  </div>
                </div>
              </div>

              <div class="flex flex-wrap sm:flex-nowrap gap-3 sm:gap-4">
                <button
                  @click="openModal(job, activeTab)"
                  class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-100 rounded-full font-medium transition flex items-center gap-2"
                >
                  <span class="material-icons text-base">visibility</span>
                  Details
                </button>

                <button
                  v-if="activeTab === 'pending'"
                  @click="openApplyModal(job)"
                  class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-medium transition flex items-center gap-2 shadow-sm"
                >
                  <span class="material-icons text-base">send</span>
                  Apply on Behalf
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Job Details Modal -->
    <JobModal v-if="showModal" :job="selectedJob" @close="closeModal" />

    <!-- Apply Modal -->
    <div
      v-if="showApplyModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
      <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl relative max-h-[95vh] overflow-y-auto"
      >
        <!-- Header -->
        <div
          class="sticky top-0 z-10 bg-white dark:bg-gray-900 flex justify-between items-start p-6 border-b border-gray-200 dark:border-gray-700"
        >
          <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
              Apply for {{ applyJob.title }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
              Applying on behalf of <strong>{{ applyJob.user_name }}</strong>
            </p>
          </div>
          <button
            @click="closeApplyModal"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <span class="material-icons text-2xl">close</span>
          </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-6 space-y-5">
          <!-- Subject -->
          <div>
            <label class="block text-sm font-medium mb-2"
              >Subject <span class="text-red-500">*</span></label
            >
            <input
              v-model="applyForm.subject"
              type="text"
              placeholder="Enter subject"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2.5 focus:ring-2 focus:ring-indigo-400 dark:bg-gray-800 dark:text-gray-100 transition"
            />
          </div>

          <!-- Email Body -->
          <div>
            <label class="block text-sm font-medium mb-2"
              >Email Body <span class="text-red-500">*</span></label
            >
            <QuillEditor
              v-model:content="applyForm.body"
              contentType="html"
              theme="snow"
              class="min-h-[200px] rounded-lg overflow-hidden"
              placeholder="Write your message here..."
            />
          </div>
          <!-- CV Upload -->
          <div>
            <label class="block text-sm font-medium mb-2"
              >CV (PDF, DOC, DOCX)</label
            >
            <div
              class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-indigo-500 transition"
            >
              <input
                type="file"
                accept=".pdf,.doc,.docx"
                @change="handleFileChange($event, 'cv')"
                class="hidden"
                ref="cvInput"
              />
              <div
                @click="$refs.cvInput.click()"
                class="flex flex-col items-center justify-center"
              >
                <span class="material-icons text-4xl text-gray-400 mb-2"
                  >upload_file</span
                >
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  {{ applyForm.cvName || "Click to upload CV" }}
                </p>
              </div>
            </div>

            <div v-if="applyJob?.existing_cv" class="mt-3 text-sm">
              <span class="text-gray-500 dark:text-gray-400">Existing CV:</span>
              <a
                :href="getFullCVUrl(applyJob.existing_cv)"
                target="_blank"
                class="text-indigo-600 hover:underline ml-1"
                >{{ getFileNameFromPath(applyJob.existing_cv) }}</a
              >
            </div>
          </div>

          <!-- Cover Letter Upload -->
          <div class="mt-5">
            <label class="block text-sm font-medium mb-2"
              >Cover Letter (PDF, DOC, DOCX)</label
            >
            <div
              class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center cursor-pointer hover:border-indigo-500 transition"
            >
              <input
                type="file"
                accept=".pdf,.doc,.docx"
                @change="handleFileChange($event, 'coverLetter')"
                class="hidden"
                ref="coverLetterInput"
              />
              <div
                @click="$refs.coverLetterInput.click()"
                class="flex flex-col items-center justify-center"
              >
                <span class="material-icons text-4xl text-gray-400 mb-2"
                  >upload_file</span
                >
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  {{
                    applyForm.coverLetterName || "Click to upload Cover Letter"
                  }}
                </p>
              </div>
            </div>

            <div v-if="applyJob?.existing_cover_letter" class="mt-3 text-sm">
              <span class="text-gray-500 dark:text-gray-400"
                >Existing Cover Letter:</span
              >
              <a
                :href="getFullCVUrl(applyJob.existing_cover_letter)"
                target="_blank"
                class="text-indigo-600 hover:underline ml-1"
                >{{ getFileNameFromPath(applyJob.existing_cover_letter) }}</a
              >
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="sticky bottom-0 z-10 bg-white dark:bg-gray-900 flex justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        >
          <button
            @click="generateAIContent(applyJob)"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow transition"
          >
            Generate AI Content
          </button>
          <button
            @click="closeApplyModal"
            class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 shadow transition"
          >
            Cancel
          </button>
          <button
            @click="submitApplication"
            :disabled="submitting"
            class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md flex items-center gap-2 disabled:opacity-60"
          >
            <span v-if="!submitting">Send Application</span>
            <svg
              v-else
              class="animate-spin h-5 w-5 text-white"
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
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              ></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import Swal from "sweetalert2";
import JobService from "@/services/jobService";
import JobModal from "@/components/Dashboard/modals/JobModal.vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { useAuthStore } from "@/stores/auth";

const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const activeTab = ref("pending");
const searchQuery = ref("");
const selectedJob = ref(null);
const showModal = ref(false);
const showApplyModal = ref(false);
const applyJob = ref(null);
const submitting = ref(false);

const authStore = useAuthStore();
const authUser = authStore.user;

const applyForm = ref({
  subject: "",
  body: "",
  cv: null,
  coverLetter: null,
  cvName: "",
  coverLetterName: "",
});

const filteredJobs = computed(() => {
  return jobs.value
    .filter((job) =>
      activeTab.value === "applied"
        ? job.application_status === "applied"
        : job.application_status !== "applied"
    )
    .filter((job) =>
      job.user_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

async function fetchJobs() {
  loading.value = true;
  error.value = "";
  try {
    const data = await JobService.getSavedJobs();
    jobs.value = Array.isArray(data.data) ? data.data : [];
  } catch (err) {
    error.value =
      err.response?.status === 403
        ? "Access denied. Upgrade your plan to access jobs."
        : "Error fetching jobs.";
  } finally {
    loading.value = false;
  }
}

function openModal(job, tab) {
  if (tab === "applied") {
    // Show application details for applied jobs
    selectedJob.value = {
      ...job,
      isApplied: true, // flag to render application content
    };
  } else {
    // Show regular job details
    selectedJob.value = {
      ...job,
      isApplied: false,
    };
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  selectedJob.value = null;
}
function closeApplyModal() {
  showApplyModal.value = false;
  applyJob.value = null;
}

function handleFileChange(e, type) {
  const file = e.target.files[0];
  if (!file) return;
  if (type === "cv") {
    applyForm.value.cv = file;
    applyForm.value.cvName = file.name;
  } else {
    applyForm.value.coverLetter = file;
    applyForm.value.coverLetterName = file.name;
  }
}

function getFileNameFromPath(path) {
  return path?.split("/").pop() || "";
}
function getFullCVUrl(path) {
  return path?.startsWith("http")
    ? path
    : `https://careershyne.com/storage/${path}`;
}

async function openApplyModal(job) {
  applyJob.value = job;
  applyForm.value.subject = "";
  applyForm.value.body = "";
  applyForm.value.cv = null;
  applyForm.value.coverLetter = null;
  applyForm.value.cvName = job.existing_cv
    ? getFileNameFromPath(job.existing_cv)
    : "";
  applyForm.value.coverLetterName = job.existing_cover_letter
    ? getFileNameFromPath(job.existing_cover_letter)
    : "";
  showApplyModal.value = true;
}

async function submitApplication() {
  if (
    !applyForm.value.subject ||
    !applyForm.value.body ||
    (!applyForm.value.cv && !applyJob.value.existing_cv)
  ) {
    return Swal.fire(
      "Validation Error",
      "Please fill subject, body, and attach a CV.",
      "warning"
    );
  }

  submitting.value = true;
  try {
    const formData = new FormData();
    formData.append("user_id", applyJob.value.user_id);
    formData.append("subject", applyForm.value.subject);
    formData.append("body", applyForm.value.body);

    if (applyForm.value.cv) formData.append("cv", applyForm.value.cv);
    else if (applyJob.value.existing_cv)
      formData.append("cv_url", applyJob.value.existing_cv);

    if (applyForm.value.coverLetter)
      formData.append("cover_letter", applyForm.value.coverLetter);
    else if (applyJob.value.existing_cover_letter)
      formData.append("cover_letter_url", applyJob.value.existing_cover_letter);

    const res = await JobService.applyOnBehalf(applyJob.value.id, formData);

    const idx = jobs.value.findIndex((j) => j.id === applyJob.value.id);
    if (idx !== -1) jobs.value[idx].application_status = "applied";

    Swal.fire({
      icon: "success",
      title: "Application Sent",
      text: res.data?.message || "Application sent successfully!",
      timer: 2000,
      showConfirmButton: false,
    });
    closeApplyModal();
  } catch (err) {
    Swal.fire({
      icon: "error",
      title: "Failed",
      text: err.response?.data?.message || "Failed to send application.",
    });
  } finally {
    submitting.value = false;
  }
}

async function generateAIContent(job) {
  try {
    Swal.fire({
      title: "Generating AI Content...",
      didOpen: () => Swal.showLoading(),
    });
    const res = await JobService.generateContent(job.id, job.user_id);
    const data = res.data || res;
    applyForm.value.subject = data.subject || "";
    applyForm.value.body = data.body || "";
    Swal.close();
  } catch {
    Swal.fire({
      icon: "error",
      title: "AI Generation Failed",
      text: "Please try again later.",
    });
  }
}

onMounted(fetchJobs);
</script>
