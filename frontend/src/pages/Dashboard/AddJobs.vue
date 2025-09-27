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
        <select
          v-model="job.type"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        >
          <option value="">Select type</option>
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Contract">Contract</option>
          <option value="Internship">Internship</option>
        </select>
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

      <!-- Job Field -->
      <div>
        <label class="block text-sm font-medium mb-2">Job Field *</label>
        <select
          v-model="job.field"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        >
          <option value="">Select Job Category</option>
          <option
            v-for="option in industryOptions"
            :key="option.id || option.value"
            :value="option.name || option.label || option.value"
          >
            {{ option.name || option.label || option.value }}
          </option>
        </select>
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
        <textarea
          v-model="job.applicationInstructions"
          rows="4"
          placeholder="e.g. Send your CV to hr@company.com"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        ></textarea>
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
        <label class="block text-sm font-medium mb-2">County *</label>
        <select
          v-model="job.county"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-indigo-500"
          required
        >
          <option value="">Select County</option>
          <option
            v-for="option in countyOptions"
            :key="option.id || option.value"
            :value="option.name || option.label || option.value"
          >
            {{ option.name || option.label || option.value }}
          </option>
        </select>
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
          class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
        >
          Post Job
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import OptionsService from "@/services/optionsService";
import JobService from "@/services/jobService"; // ✅ import service
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

const auth = useAuthStore();

const job = ref({
  company: "",
  title: "",
  type: "",
  experience: "",
  education: "",
  salary: "",
  deadline: "",
  field: "",
  description: "",
  applicationInstructions: "",
  country: "Kenya",
  county: "",
  office: ""
});

const industryOptions = ref([]);
const educationOptions = ref([]);
const countyOptions = ref([]);

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
  const payload = {
    ...job.value,
    postedBy: auth.user?.id || null,
  };

  try {
    const res = await JobService.createJob(payload); 
    console.log("Job created:", res);
    alert("Job posted successfully!");

    // Reset form
    job.value = {
      company: "",
      title: "",
      type: "",
      experience: "",
      education: "",
      salary: "",
      deadline: "",
      field: "",
      description: "",
      applicationInstructions: "",
      country: "Kenya",
      county: "",
      office: ""
    };
  } catch (err) {
    console.error("Error submitting job:", err);
    alert("Failed to post job. Please try again.");
  }
}
</script>
