<template>
  <div class="max-w-lg mx-auto my-10 bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center mb-6">Order Your Custom CV</h2>
    
    <form @submit.prevent="submitForm" class="space-y-4">
      <!-- Full Name -->
      <div>
        <label class="block text-gray-700">Full Name</label>
        <input v-model="form.fullname" type="text" required 
               class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"/>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-gray-700">Email</label>
        <input v-model="form.email" type="email" required 
               class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"/>
      </div>
      <!-- Phone -->
      <div>
        <label class="block text-gray-700">Phone</label>
        <input v-model="form.phone" type="tel" required 
               class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300"/>
      </div>

      <!-- CV Upload -->
      <div>
        <label class="block text-gray-700">Upload CV</label>
        <input type="file" @change="handleFileUpload" accept=".pdf,.doc,.docx" required
               class="w-full mt-1 px-4 py-2 border rounded-lg"/>
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

      <!-- Feedback Messages -->
      <p v-if="successMessage" class="text-green-600 font-medium mt-3">
        {{ successMessage }}
      </p>
      <p v-if="errorMessage" class="text-red-600 font-medium mt-3">
        {{ errorMessage }}
      </p>
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
  cv: null,
});

const loading = ref(false);

const handleFileUpload = (e) => {
  form.value.cv = e.target.files[0];
};

const submitForm = async () => {
  loading.value = true;

  try {
    const data = await submitCvOrder(form.value);

    // ✅ Show SweetAlert2 popup
    await Swal.fire({
      title: "Order Saved Successfully 🎉",
      html: `
        <p class="mt-2 text-green-600">Redirecting to payment page...</p>
      `,
      icon: "success",
      timer: 4000,
      timerProgressBar: true,
      showConfirmButton: false,
    });

    // ✅ Redirect to payment page with order ID
    router.push({ 
      name: "PaymentPage", 
      params: { id: data.id } 
    });

    // Reset form
    form.value = { fullname: "", email: "", phone: "", cv: null };
  } catch (err) {
    console.error(err);
    Swal.fire("Error ❌", "Something went wrong. Please try again.", "error");
  } finally {
    loading.value = false;
  }
};
</script>
