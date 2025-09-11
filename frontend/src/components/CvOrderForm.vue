<template>
  <div class="max-w-lg mx-auto my-10 bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">
      Order Your Custom CV
    </h2>

    <form @submit.prevent="submitForm" class="space-y-5">
      <!-- Full Name -->
      <div>
        <label class="block text-gray-700 font-medium">Full Name</label>
        <input
          v-model="form.fullname"
          type="text"
          placeholder="John Doe"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
        />
      </div>

      <!-- Email -->
      <div>
        <label class="block text-gray-700 font-medium">Email</label>
        <input
          v-model="form.email"
          type="email"
          placeholder="you@example.com"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
        />
      </div>

      <!-- Phone -->
      <div>
        <label class="block text-gray-700 font-medium">Phone</label>
        <input
          v-model="form.phone"
          type="tel"
          placeholder="+254700000000"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-orange-300 focus:outline-none"
        />
      </div>

      <!-- CV Upload -->
      <div>
        <label class="block text-gray-700 font-medium">Upload CV</label>
        <input
          type="file"
          @change="handleFileUpload"
          accept=".pdf,.doc,.docx"
          required
          class="w-full mt-1 px-4 py-2 border rounded-lg cursor-pointer"
        />
        <p v-if="form.cv" class="mt-1 text-sm text-gray-600">
          Selected: <span class="font-medium">{{ form.cv.name }}</span>
        </p>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="loading || !isFormValid"
        class="w-full bg-[#ff9c30] text-white py-3 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-orange-600"
      >
        <span v-if="loading">Submitting...</span>
        <span v-else>Submit Order</span>
      </button>

      <!-- Feedback Messages -->
      <transition name="fade">
        <p v-if="successMessage" class="text-green-600 font-medium mt-3">
          {{ successMessage }}
        </p>
      </transition>
      <transition name="fade">
        <p v-if="errorMessage" class="text-red-600 font-medium mt-3">
          {{ errorMessage }}
        </p>
      </transition>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import Swal from "sweetalert2";
import { submitCvOrder } from "@/services/cvOrderService";

const router = useRouter();
const route = useRoute();

const form = ref({
  fullname: "",
  email: "",
  phone: "",
  type: "cv",
  cv: null,
  ref: "", // <-- added ref here
});

const loading = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

// Capture file
const handleFileUpload = (e) => {
  form.value.cv = e.target.files[0];
};

// Validate form
const isFormValid = computed(() => {
  return form.value.fullname && form.value.email && form.value.phone && form.value.cv;
});

onMounted(() => {
  form.value.ref = route.query.ref || "";
});

// Submit form
const submitForm = async () => {
  loading.value = true;
  successMessage.value = "";
  errorMessage.value = "";
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
    form.value = { fullname: "", email: "", phone: "", type: "cv", cv: null, ref: "" };
    document.querySelector('input[type="file"]').value = "";
  } catch (err) {
    console.error(err);
    errorMessage.value = "Something went wrong. Please try again.";
    Swal.fire("Error ❌", errorMessage.value, "error");
  } finally {
    loading.value = false;
  }
};
</script>


<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
