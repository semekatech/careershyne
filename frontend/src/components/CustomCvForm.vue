<template>
  <div class="max-w-4xl mx-auto my-10 bg-white p-8 rounded-2xl shadow-md">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
      CV Builder + Cover Letter
    </h2>

    <form @submit.prevent="submitForm" class="space-y-8">
      <!-- Section 1: Personal Information -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Personal Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input v-model="form.fullname" type="text" placeholder="Full Name" class="input" required />
          <input v-model="form.email" type="email" placeholder="Email Address" class="input" required />
          <input v-model="form.phone" type="tel" placeholder="Phone Number" class="input" required />
          <input v-model="form.location" type="text" placeholder="Location (City, Country)" class="input" />
        </div>
      </div>

      <!-- Section 2: Career Goal -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Career Goal / Objective</h3>
        <textarea
          v-model="form.careerGoal"
          rows="3"
          placeholder="Seeking a marketing role to apply my digital skills and grow in a dynamic environment."
          class="input"
        ></textarea>
      </div>

      <!-- Section 3: Education Background -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Education Background</h3>
        <div v-for="(edu, index) in form.education" :key="index" class="space-y-3 mb-4 border p-4 rounded-lg">
          <input v-model="edu.institution" type="text" placeholder="Institution Name" class="input" />
          <input v-model="edu.degree" type="text" placeholder="Degree / Certificate" class="input" />
          <div class="grid grid-cols-2 gap-4">
            <input v-model="edu.startDate" type="date" class="input" />
            <input v-model="edu.endDate" type="date" class="input" />
          </div>
        </div>
        <button type="button" @click="addEducation" class="btn-secondary">➕ Add More</button>
      </div>

      <!-- Section 4: Work Experience -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Work Experience</h3>
        <div v-for="(job, index) in form.experience" :key="index" class="space-y-3 mb-4 border p-4 rounded-lg">
          <input v-model="job.title" type="text" placeholder="Job Title" class="input" />
          <input v-model="job.company" type="text" placeholder="Company Name" class="input" />
          <div class="grid grid-cols-2 gap-4">
            <input v-model="job.startDate" type="date" class="input" />
            <input v-model="job.endDate" type="date" class="input" />
          </div>
          <textarea v-model="job.responsibilities" rows="3" placeholder="Key Responsibilities & Achievements" class="input"></textarea>
        </div>
        <button type="button" @click="addExperience" class="btn-secondary">➕ Add More</button>
      </div>

      <!-- Section 5: Skills -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Skills</h3>
        <input
          v-model="form.skills"
          type="text"
          placeholder="Project Management, Data Analysis, Communication, MS Excel"
          class="input"
        />
      </div>

      <!-- Section 6: Certifications -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Certifications (Optional)</h3>
        <div v-for="(cert, index) in form.certifications" :key="index" class="space-y-3 mb-4 border p-4 rounded-lg">
          <input v-model="cert.name" type="text" placeholder="Certification Name" class="input" />
          <input v-model="cert.issuer" type="text" placeholder="Issuing Organization" class="input" />
          <input v-model="cert.year" type="text" placeholder="Year of Completion" class="input" />
        </div>
        <button type="button" @click="addCertification" class="btn-secondary">➕ Add More</button>
      </div>

      <!-- Section 7: Links -->
      <div>
        <h3 class="text-xl font-semibold mb-4">LinkedIn & Other Links</h3>
        <input v-model="form.linkedin" type="url" placeholder="LinkedIn Profile URL" class="input" />
        <input v-model="form.portfolio" type="url" placeholder="Portfolio / Personal Website" class="input" />
      </div>

     

      <!-- Section 9: File Upload -->
      <div>
        <h3 class="text-xl font-semibold mb-4">Upload Old CV / Supporting Docs (Optional)</h3>
        <input type="file" @change="handleFileUpload" accept=".pdf,.doc,.docx" class="input cursor-pointer" />
        <p v-if="form.cv" class="mt-1 text-sm text-gray-600">
          Selected: <span class="font-medium">{{ form.cv.name }}</span>
        </p>
      </div>

      <!-- Submit -->
      <button type="submit" :disabled="loading" class="btn-primary">
        <span v-if="loading">Submitting...</span>
        <span v-else>Submit Order</span>
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import Swal from "sweetalert2";
import { useRouter } from "vue-router";
import { submitCvOrder } from "@/services/cvOrderService";

const router = useRouter();

const form = ref({
  fullname: "",
  email: "",
  phone: "",
  location: "",
  careerGoal: "",
  type: "cvscratch",
  education: [{ institution: "", degree: "", startDate: "", endDate: "" }],
  experience: [{ title: "", company: "", startDate: "", endDate: "", responsibilities: "" }],
  skills: "",
  certifications: [{ name: "", issuer: "", year: "" }],
  linkedin: "",
  portfolio: "",
  coverRole: "",
  coverWhy: "",
  coverStrengths: "",
  cv: null,
});

const loading = ref(false);

const addEducation = () => {
  form.value.education.push({ institution: "", degree: "", startDate: "", endDate: "" });
};

const addExperience = () => {
  form.value.experience.push({ title: "", company: "", startDate: "", endDate: "", responsibilities: "" });
};

const addCertification = () => {
  form.value.certifications.push({ name: "", issuer: "", year: "" });
};

const handleFileUpload = (e) => {
  form.value.cv = e.target.files[0];
};

const submitForm = async () => {
  loading.value = true;
  try {
    const data = await submitCvOrder(form.value);
    await Swal.fire({
      title: "Order Submitted 🎉",
      text: "Redirecting to payment...",
      icon: "success",
      timer: 3000,
      showConfirmButton: false,
    });
    router.push({ name: "PaymentPage", params: { id: data.id } });
  } catch (err) {
    console.error(err);
    Swal.fire("Error ❌", "Something went wrong. Please try again.", "error");
  } finally {
    loading.value = false;
  }
};
</script>

<style>
.input {
  @apply w-full px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none;
}
.btn-primary {
  @apply w-full bg-[#ff9c30] text-white py-3 rounded-lg hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed;
}
.btn-secondary {
  @apply px-4 py-2 mt-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition;
}

</style>
