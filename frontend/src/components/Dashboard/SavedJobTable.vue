<template>
  <div class="w-full p-4">
    <!-- Card Container -->
    <div class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6">
      <section>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
          Saved Jobs
        </h2>

        <!-- Loader -->
        <div v-if="loading" class="space-y-4 animate-pulse">
          <div
            v-for="n in 5"
            :key="n"
            class="bg-gray-100 dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700"
          >
            <div
              class="h-6 bg-gray-300 dark:bg-gray-700 rounded w-3/4 mb-3"
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
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-md"
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
            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-md"
          >
            Browse Jobs
          </router-link>
        </div>

        <!-- Jobs List -->
        <div v-else class="space-y-4">
          <div
            v-for="job in jobs.slice(0, 100)"
            :key="job.id"
            class="bg-gray-50 dark:bg-gray-800 p-6 rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow"
          >
            <div
              class="flex flex-col sm:flex-row justify-between items-start gap-4 sm:gap-0"
            >
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-indigo-600 mb-1">
                  {{ job.title }} - {{ job.county }}, {{ job.country }}
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-2">
                  {{ job.company }} • {{ job.type }}
                </p>

                <div
                  class="flex flex-wrap items-center text-sm text-gray-500 dark:text-gray-400 space-x-4 mb-2"
                >
                  <div class="flex items-center gap-1">
                    <span class="material-icons text-base">location_on</span>
                    <span>{{ job.county }}, {{ job.country }}</span>
                  </div>
                  <div class="flex items-center gap-1">
                    <span class="material-icons text-base">event</span>
                    <span>Deadline: {{ formatDate(job.deadline) }}</span>
                  </div>
                </div>

                <div class="text-sm text-gray-500 dark:text-gray-400">
                  <span class="font-medium">Saved by:</span>
                  {{ job.user_name || "You" }} |
                  <span class="font-medium">Saved on:</span>
                  {{ formatDate(job.saved_at || job.created_at) }}
                </div>
              </div>

              <!-- Buttons -->
              <div class="flex flex-col sm:flex-row gap-2 mt-4 sm:mt-0">
                <button
                  @click="openModal(job)"
                  class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-semibold shadow-md flex items-center gap-2 transition-colors"
                >
                  View Details
                  <span class="material-icons text-base">arrow_forward</span>
                </button>

                <button
                  :class="[
                    'px-4 py-2 rounded-full font-semibold shadow-md flex items-center gap-2 transition-colors',
                    job.application_status === 'applied'
                      ? 'bg-gray-400 text-white cursor-not-allowed'
                      : 'bg-green-600 hover:bg-green-700 text-white',
                  ]"
                  :disabled="job.application_status === 'applied'"
                  @click="openApplyModal(job)"
                >
                  <span class="material-icons text-base">send</span>
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
    </div>

    <!-- Job Details Modal -->
    <JobModal v-if="showModal" :job="selectedJob" @close="closeModal" />

    <!-- Apply Modal -->
    <!-- Apply Modal -->
    <!-- Apply Modal -->
    <div
      v-if="showApplyModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
    >
      <div
        class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh] relative"
      >
        <!-- Close Button -->
        <button
          @click="closeApplyModal"
          class="absolute top-4 right-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
        >
          <span class="material-icons">close</span>
        </button>

        <h3 class="text-2xl font-bold mb-2 text-gray-800 dark:text-gray-100">
          Apply for {{ applyJob.title }}
        </h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6 text-sm">
          Applying on behalf of <strong>{{ applyJob.user_name }}</strong>
        </p>

        <div class="space-y-5">
          <!-- Subject -->
          <div>
            <label class="block font-medium mb-2"
              >Subject <span class="text-red-500">*</span></label
            >
            <input
              v-model="applyForm.subject"
              type="text"
              placeholder="Enter email subject"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:bg-gray-800 dark:text-gray-100 transition"
            />
          </div>

          <!-- Email Body -->
          <!-- Email Body -->
          <div>
            <label class="block font-medium mb-2"
              >Email Body <span class="text-red-500">*</span></label
            >

            <QuillEditor
              v-model:content="applyForm.body"
              contentType="html"
              theme="snow"
              placeholder="e.g. Influencers will showcase their daily skincare routine using Aloe Skincare products, highlighting the hydrating and soothing effects of aloe vera. Deliverables include one Instagram Reel or TikTok video, and one Instagram Story. Key talking points: natural ingredients, skin healing, and everyday glow. CTA: 'Try the Aloe Glow today!'"
              class="min-h-[150px]"
            />
          </div>
          <!-- Attach CV -->
          <div>
            <label class="block font-medium mb-2">
              Attach CV <span class="text-red-500">*</span>
            </label>

            <!-- Show existing CV if present -->
            <div
              v-if="applyJob.value?.existing_cv"
              class="mb-3 flex items-center gap-3"
            >
              <span class="material-icons text-gray-500">description</span>
              <a
                :href="applyJob.value.existing_cv"
                target="_blank"
                class="text-indigo-600 hover:underline"
              >
                {{ getFileNameFromPath(applyJob.value.existing_cv) }}
              </a>
            </div>

            <!-- Upload new CV -->
            <div
              class="relative w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500 transition"
            >
              <input
                type="file"
                @change="handleFileChange($event, 'cv')"
                accept=".pdf,.doc,.docx"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              />
              <div class="text-center">
                <span class="material-icons text-3xl text-gray-400 mb-2"
                  >upload_file</span
                >
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  Drag & drop your CV here, or click to select
                </p>
                <p
                  v-if="applyForm.cvName"
                  class="text-sm mt-2 text-gray-700 dark:text-gray-200"
                >
                  Selected file: {{ applyForm.cvName }}
                </p>
              </div>
            </div>
          </div>
          <!-- Cover Letter -->
          <div>
            <label class="block font-medium mb-2"
              >Cover Letter (Optional)</label
            >

            <!-- Show existing cover letter if present -->
            <div
              v-if="applyJob.value?.existing_cover_letter"
              class="mb-3 flex items-center gap-3"
            >
              <span class="material-icons text-gray-500">description</span>
              <a
                :href="applyJob.value.existing_cover_letter"
                target="_blank"
                class="text-indigo-600 hover:underline"
              >
                {{ getFileNameFromPath(applyJob.value.existing_cover_letter) }}
              </a>
            </div>

            <!-- Upload new cover letter -->
            <div
              class="relative w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 flex flex-col items-center justify-center cursor-pointer hover:border-indigo-500 transition"
            >
              <input
                type="file"
                @change="handleFileChange($event, 'coverLetter')"
                accept=".pdf,.doc,.docx"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              />
              <div class="text-center">
                <span class="material-icons text-3xl text-gray-400 mb-2"
                  >upload_file</span
                >
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                  Drag & drop your cover letter here, or click to select
                </p>
                <p
                  v-if="applyForm.coverLetterName"
                  class="text-sm mt-2 text-gray-700 dark:text-gray-200"
                >
                  Selected file: {{ applyForm.coverLetterName }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap justify-end gap-3 mt-6">
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
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow transition"
          >
            Send Application
          </button>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, nextTick } from "vue";

import Swal from "sweetalert2";
import JobService from "@/services/jobService";
import JobModal from "@/components/Dashboard/modals/JobModal.vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
const jobs = ref([]);
const loading = ref(true);
const error = ref("");
const selectedJob = ref(null);
const showModal = ref(false);
const quillEditor = ref(null);

const showApplyModal = ref(false);
const applyJob = ref(null);
import { useAuthStore } from "@/stores/auth";
const authStore = useAuthStore();
const authUser = authStore.user;

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

async function openApplyModal(job) {
  applyJob.value = job;
  applyForm.value.subject = "";
  applyForm.value.body = "";

  // Pre-fill CV
  // ✅ Pre-fill CV (just show filename, don’t fetch or re-upload)
  // ✅ Pre-fill CV
  if (job.existing_cv) {
    applyForm.value.cv = null;
    applyForm.value.cvName = getFileNameFromPath(job.existing_cv);
    applyJob.value.existing_cv = job.existing_cv; // <-- keep full URL
  } else {
    applyForm.value.cv = null;
    applyForm.value.cvName = "";
    applyJob.value.existing_cv = "";
  }

  // ✅ Pre-fill Cover Letter
  if (job.existing_cover_letter) {
    applyForm.value.coverLetter = null;
    applyForm.value.coverLetterName = getFileNameFromPath(
      job.existing_cover_letter
    );
    applyJob.value.existing_cover_letter = job.existing_cover_letter; // <-- keep full URL
  } else {
    applyForm.value.coverLetter = null;
    applyForm.value.coverLetterName = "";
    applyJob.value.existing_cover_letter = "";
  }

  showApplyModal.value = true;
}

async function fetchFileFromUrl(url, fileName) {
  const res = await fetch(url);
  const blob = await res.blob();
  return new File([blob], fileName, { type: blob.type });
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
function getFileNameFromPath(path) {
  if (!path) return "";
  return path.split("/").pop(); // last part of URL
}

async function submitApplication() {
  if (
    !applyForm.value.subject ||
    !applyForm.value.body ||
    (!applyForm.value.cv && !applyJob.value.existing_cv)
  ) {
    Swal.fire(
      "Validation Error",
      "Subject, body, and at least one CV (uploaded or existing) are required.",
      "warning"
    );
    return;
  }

  try {
    const formData = new FormData();
    formData.append("user_id", applyJob.value.user_id);
    formData.append("subject", applyForm.value.subject);
    formData.append("body", applyForm.value.body);

    // 🟢 Attach new or existing CV
    // CV upload
    if (applyForm.value.cv) {
      formData.append("cv", applyForm.value.cv);
    } else if (applyJob.value.existing_cv) {
      formData.append("cv_url", applyJob.value.existing_cv);
    }

    // Cover letter upload
    if (applyForm.value.coverLetter) {
      formData.append("cover_letter", applyForm.value.coverLetter);
    } else if (applyJob.value.existing_cover_letter) {
      formData.append("cover_letter_url", applyJob.value.existing_cover_letter);
    }

    const res = await JobService.applyOnBehalf(applyJob.value.id, formData);

    // Update job status locally
    const jobIndex = jobs.value.findIndex((j) => j.id === applyJob.value.id);
    if (jobIndex !== -1) {
      jobs.value[jobIndex].application_status = "applied";
    }

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

const editorOptions = {
  theme: "snow",
  modules: {
    toolbar: [
      ["bold", "italic", "underline"],
      [{ list: "ordered" }, { list: "bullet" }],
      ["link", "image"],
      ["clean"],
    ],
  },
};
function decodeHtml(html) {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}
async function generateAIContent(job) {
  if (!job) return;

  try {
    Swal.fire({
      title: "Generating...",
      text: "AI is generating your email...",
      didOpen: () => Swal.showLoading(),
      allowOutsideClick: false,
    });

    const userId = job.user_id || authUser.id;
    const res = await JobService.generateContent(job.id, userId);
    const data = res.data || res;

    // Update subject normally
    applyForm.value.subject = data.subject || "";
    applyForm.value.body = data.body || "";

    Swal.close();
  } catch (err) {
    console.error(err);
    Swal.fire({
      icon: "error",
      title: "Failed",
      text:
        err.response?.data?.message ||
        "Could not generate AI content. Try again.",
    });
  }
}

onMounted(fetchJobs);
</script>
