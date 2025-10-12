<template>
  <div class="w-full bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-6">Add Job</h2>

    <form @submit.prevent="submitJob" class="space-y-6">
      <!-- Hiring Company -->
      <div>
        <label class="block text-sm font-medium mb-2">Hiring Company *</label>
        <input
          v-model="job.company"
          type="text"
          placeholder="Enter company name"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- Job Title -->
      <div>
        <label class="block text-sm font-medium mb-2">Job Title *</label>
        <input
          v-model="job.title"
          type="text"
          placeholder="Enter job title"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- Employment Type -->
      <div>
        <label class="block text-sm font-medium mb-2">Employment Type *</label>
        <Multiselect
          v-model="job.type"
          :options="employmentOptions"
          :multiple="true"
          :close-on-select="false"
          :clear-on-select="false"
          placeholder="Select job type(s)"
          label="label"
          track-by="value"
        />
      </div>

      <!-- Experience -->
      <div>
        <label class="block text-sm font-medium mb-2">Experience (Yrs) *</label>
        <input
          v-model="job.experience"
          type="number"
          min="0"
          placeholder="e.g. 3"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- Education Level -->
      <div>
        <label class="block text-sm font-medium mb-2">Education Level *</label>
        <select
          v-model="job.education"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        >
          <option value="">Select education level</option>
          <option
            v-for="option in educationOptions"
            :key="option.id || option.value"
            :value="option.name || option.label || option.value"
          >
            {{ option.name || option.label || option.value }}
          </option>
        </select>
      </div>

      <!-- Salary Range -->
      <div>
        <label class="block text-sm font-medium mb-2">Salary Range *</label>
        <input
          v-model="job.salary"
          type="text"
          placeholder="e.g. 50,000 - 70,000 KES"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- Application Deadline -->
      <div>
        <label class="block text-sm font-medium mb-2">Application Deadline *</label>
        <input
          v-model="job.deadline"
          type="date"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- Job Categories -->
      <div>
        <label class="block text-sm font-medium mb-2">Job Categories *</label>
        <Multiselect
          v-model="job.field"
          :options="industryOptions"
          :multiple="true"
          label="name"
          track-by="id"
          :close-on-select="false"
          placeholder="Select job categories"
        />
      </div>

      <!-- Job Details -->
      <div>
        <label class="block text-sm font-medium mb-2">Job Details *</label>
        <quill-editor
          v-model:content="job.description"
          content-type="html"
          theme="snow"
          class="bg-white border rounded w-full"
          style="min-height: 150px"
          required
        />
      </div>

      <!-- Application Instructions -->
      <div>
        <label class="block text-sm font-medium mb-2">Application Instructions *</label>
        <quill-editor
          v-model:content="job.applicationInstructions"
          content-type="html"
          theme="snow"
          class="bg-white border rounded w-full"
          style="min-height: 150px"
          required
        />
      </div>

      <!-- Job Country -->
      <div>
        <label class="block text-sm font-medium mb-2">Job Country *</label>
        <input
          v-model="job.country"
          type="text"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- County -->
      <div>
        <label class="block text-sm font-medium mb-2">Counties *</label>
        <Multiselect
          v-model="job.county"
          :options="countyOptions"
          :multiple="true"
          :close-on-select="false"
          placeholder="Select applicable counties"
          label="name"
          track-by="name"
        />
      </div>

      <!-- Job Location / Office -->
      <div>
        <label class="block text-sm font-medium mb-2">Job Location / Office *</label>
        <input
          v-model="job.office"
          type="text"
          placeholder="e.g. Westlands, Nairobi"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        />
      </div>

      <!-- Submit Button -->
      <div class="pt-4">
        <button
          type="submit"
          class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 flex items-center justify-center"
          :disabled="loading"
        >
          <span
            v-if="loading"
            class="mr-2 animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"
          ></span>
          {{ loading ? "Posting..." : "Post Job" }}
        </button>
      </div>

      <div v-if="successMessage" class="mt-4 text-green-600 font-medium">
        {{ successMessage }}
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import OptionsService from "@/services/optionsService";
import JobService from "@/services/jobService";
import { QuillEditor } from "@vueup/vue-quill";
import Multiselect from "vue-multiselect";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import "vue-multiselect/dist/vue-multiselect.css";

const auth = useAuthStore();

const job = ref({
  company: "",
  title: "",
  type: [],
  experience: "",
  education: "",
  salary: "",
  deadline: "",
  field: [],
  description: "",
  applicationInstructions: "",
  country: "Kenya",
  county: [],
  office: "",
});

const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

const employmentOptions = [
  { label: "Full-time", value: "Full-time" },
  { label: "Part-time", value: "Part-time" },
  { label: "Contract", value: "Contract" },
  { label: "Internship", value: "Internship" },
  { label: "Remote", value: "Remote" },
];

const loading = ref(false);
const successMessage = ref("");

onMounted(async () => {
  await auth.refreshUser();

  try {
    const [industries, education, counties] = await Promise.all([
      OptionsService.getIndustries(),
      OptionsService.getEducationLevels(),
      OptionsService.getCounties(),
    ]);

    industryOptions.value = industries.data || industries;
    educationOptions.value = education.data || education;
    countyOptions.value = counties.data || counties;
  } catch (err) {
    console.error("Error loading options:", err);
  }
});

async function submitJob() {
  loading.value = true;
  successMessage.value = "";

  const payload = {
    ...job.value,
    type: Array.isArray(job.value.type)
      ? job.value.type.map((t) => (t.value ? t.value : t)).join(",")
      : job.value.type,
    field: Array.isArray(job.value.field)
      ? job.value.field.map((f) => f.id).join(",")
      : job.value.field,
    county: Array.isArray(job.value.county)
      ? job.value.county.map((c) => c.name || c).join(",")
      : job.value.county,
    postedBy: auth.user?.id || null,
  };

  try {
    const res = await JobService.createJob(payload);
    console.log("Job created:", res);
    successMessage.value = "Job posted successfully!";

    // Reset form
    job.value = {
      company: "",
      title: "",
      type: [],
      experience: "",
      education: "",
      salary: "",
      deadline: "",
      field: [],
      description: "",
      applicationInstructions: "",
      country: "Kenya",
      county: [],
      office: "",
    };
  } catch (err) {
    console.error("Error submitting job:", err);
    alert("Failed to post job. Please try again.");
  } finally {
    loading.value = false;
  }
}
</script>
