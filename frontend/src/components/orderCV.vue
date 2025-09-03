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
      <button type="submit"
              class="w-full bg-[#ff9c30] text-white py-3 rounded-lg hover:bg-orange-600 transition">
        Submit Order
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";

const form = ref({
  fullname: "",
  email: "",
  phone: "",
  cv: null,
});

const handleFileUpload = (e) => {
  form.value.cv = e.target.files[0];
};

const submitForm = () => {
  console.log("Form submitted:", form.value);
  alert("Your request has been submitted!");
  // Later: send to backend via API
};
</script>
