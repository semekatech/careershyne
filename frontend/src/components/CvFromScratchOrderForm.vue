<template>
  <div class="max-w-2xl mx-auto my-10 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center mb-6">Build Your CV From Scratch</h2>

    <form @submit.prevent="submitForm" class="space-y-4">
      <!-- Full Name -->
      <div>
        <label class="block text-gray-700">Full Name</label>
        <input
          v-model="form.fullname"
          type="text"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-gray-700">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        />
      </div>

      <!-- Phone -->
      <div>
        <label class="block text-gray-700">Phone</label>
        <input
          v-model="form.phone"
          type="tel"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        />
      </div>

      <!-- Career Objective -->
      <div>
        <label class="block text-gray-700">Career Objective</label>
        <textarea
          v-model="form.objective"
          rows="3"
          required
          placeholder="Write a short statement about your career goals..."
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        ></textarea>
      </div>

      <!-- Education -->
      <div>
        <label class="block text-gray-700">Education</label>
        <textarea
          v-model="form.education"
          rows="3"
          required
          placeholder="List your education background (e.g., Degree, Institution, Year)..."
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        ></textarea>
      </div>

      <!-- Work Experience -->
      <div>
        <label class="block text-gray-700">Work Experience</label>
        <textarea
          v-model="form.experience"
          rows="4"
          placeholder="List your past jobs, responsibilities, and achievements..."
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        ></textarea>
      </div>

      <!-- Skills -->
      <div>
        <label class="block text-gray-700">Key Skills</label>
        <textarea
          v-model="form.skills"
          rows="2"
          required
          placeholder="e.g., Project Management, Data Analysis, Communication..."
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        ></textarea>
      </div>

      <!-- Certifications -->
      <div>
        <label class="block text-gray-700">Certifications (if any)</label>
        <textarea
          v-model="form.certifications"
          rows="2"
          placeholder="e.g., PMP, CPA, Google Analytics..."
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"
        ></textarea>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-[#ff9c30] text-white py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-600"
      >
        <span v-if="loading">Submitting...</span>
        <span v-else>Submit Order</span>
      </button>

      <input
        v-model="form.type"
        type="hidden"
        required
        value="cv-from-scratch"
        class="hidden"
      />
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { submitCvOrder } from "@/services/cvOrderService";

const router = useRouter();

const form = ref({
  fullname: "",
  email: "",
  phone: "",
  objective: "",
  education: "",
  experience: "",
  skills: "",
  certifications: "",
  type: "cv-from-scratch",
});

const loading = ref(false);

const submitForm = async () => {
  loading.value = true;

  try {
    const data = await submitCvOrder(form.value);
    await Swal.fire({
      title: "Order Saved Successfully 🎉",
      html: `<p class="mt-2 text-green-600">Redirecting to payment page...</p>`,
      icon: "success",
      timer: 4000,
      timerProgressBar: true,
      showConfirmButton: false,
    });
    router.push({ name: "PaymentPage", params: { id: data.id } });

    // Reset form
    form.value = {
      fullname: "",
      email: "",
      phone: "",
      objective: "",
      education: "",
      experience: "",
      skills: "",
      certifications: "",
      type: "cv-from-scratch",
    };
  } catch (err) {
    console.error(err);
    Swal.fire("Error ❌", "Something went wrong. Please try again.", "error");
  } finally {
    loading.value = false;
  }
};
</script>
