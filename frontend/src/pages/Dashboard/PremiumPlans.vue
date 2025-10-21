<template>
  <section class="bg-white dark:bg-gray-900 py-16">
    <div class="text-center mb-12">
      <h1
        class="text-4xl sm:text-5xl font-bold text-gray-800 dark:text-gray-100"
      >
        Find the Perfect Plan
      </h1>
      <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
        Choose the plan that fits your job search goals.
      </p>
    </div>

    <div
      class="w-full max-w-6xl mx-auto flex flex-col lg:flex-row lg:space-x-8 space-y-8 lg:space-y-0 px-4"
    >
      <!-- Starter Plan -->
      <div
        class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transform hover:scale-105 transition-transform duration-300 border border-gray-200 dark:border-gray-700"
      >
        <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
          Starter
        </h3>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Ideal for beginners getting started with job applications.
        </p>

        <div class="mt-8">
          <span class="text-5xl font-bold text-gray-900 dark:text-gray-50"
            >KES 1,000</span
          >
          <span class="text-gray-600 dark:text-gray-400">/ month</span>
        </div>

        <ul class="mt-8 space-y-4 text-gray-700 dark:text-gray-300">
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
           Access to personalized Jobs
          </li>
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            Smart apply upto 1 job per day
          </li>
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            24/7 Support
          </li>
        </ul>

        <button
          @click="selectPlan('Starter', 1000)"
          class="mt-10 w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors"
        >
          Choose Starter
        </button>
      </div>

      <!-- Professional Plan (Featured) -->
      <div
        class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 border-4 border-primary transform hover:scale-105 transition-transform duration-300 relative"
      >
        <span
          class="absolute -top-4 right-6 bg-primary text-white px-4 py-1 rounded-full text-sm font-semibold shadow"
        >
          Most Popular
        </span>

        <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
          Professional
        </h3>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Perfect for active job seekers applying regularly.
        </p>

        <div class="mt-8">
          <span class="text-5xl font-bold text-gray-900 dark:text-gray-50"
            >KES 1,500</span
          >
          <span class="text-gray-600 dark:text-gray-400">/ month</span>
        </div>

        <ul class="mt-8 space-y-4 text-gray-700 dark:text-gray-300">
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            Access to personalized Jobs
          </li>
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            Smart apply upto 3 jobs per day
          </li>
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            24/7 Support
          </li>
        </ul>

        <button
          @click="selectPlan('Professional', 1500)"
          class="mt-10 w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors"
        >
          Choose Professional
        </button>
      </div>

      <!-- Premium Plan -->
      <div
        class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 transform hover:scale-105 transition-transform duration-300 border border-gray-200 dark:border-gray-700"
      >
        <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
          Premium
        </h3>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Designed for serious professionals who want full access.
        </p>

        <div class="mt-8">
          <span class="text-5xl font-bold text-gray-900 dark:text-gray-50"
            >KES 3,000</span
          >
          <span class="text-gray-600 dark:text-gray-400">/ month</span>
        </div>

        <ul class="mt-8 space-y-4 text-gray-700 dark:text-gray-300">
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            Access to personalized Jobs
          </li>
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            Smart apply upto 5 jobs per day
          </li>
          <li class="flex items-center">
            <span class="material-icons text-green-500 mr-3">check_circle</span>
            24/7 Support
          </li>
        </ul>

        <button
          @click="selectPlan('Premium', 3000)"
          class="mt-10 w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors"
        >
          Choose Premium
        </button>
      </div>
    </div>

    <!-- Phone Payment Modal -->
    <div
      v-if="openModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @keydown.esc="closeModal"
    >
      <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
        <h3 class="text-lg font-bold mb-4">
          Pay for {{ selectedPlan.name }} (KES {{ selectedPlan.amount }})
        </h3>

        <label class="block mb-2 text-gray-700">Phone Number</label>
        <input
          v-model="payment.phone"
          type="tel"
          placeholder="e.g., 254712345678 or 0712345678"
          class="w-full border border-gray-300 rounded-lg p-2 mb-1"
        />
        <p v-if="phoneError" class="text-red-600 text-sm mb-2">
          {{ phoneError }}
        </p>

        <div class="flex justify-between items-center mt-4">
          <button
            @click="makePayment"
            class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition"
          >
            Pay KES {{ selectedPlan.amount }}
          </button>
          <button
            @click="closeModal"
            class="text-gray-700 px-4 py-2 rounded-lg border hover:bg-gray-100 transition"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive } from "vue";
import Swal from "sweetalert2";
import renewPlanService from "@/services/renewPlan";

const { initiatePayment, checkPaymentStatus } = renewPlanService;

// State
const openModal = ref(false);
const payment = reactive({ phone: "" });
const phoneError = ref("");
const selectedPlan = reactive({ name: "", amount: 0 });
const order = reactive({
  data: {
    amount: 0,
    orderID: Math.floor(Math.random() * 1000000),
    status: "pending",
  },
});

// Choose plan
function selectPlan(name, amount) {
  selectedPlan.name = name;
  selectedPlan.amount = amount;
  openModal.value = true;
}

// Validate Kenyan phone
function isValidPhone(num) {
  const clean = num.replace(/\D/g, "");
  return /^254\d{9}$/.test(clean) || /^(07|01)\d{8}$/.test(clean);
}

// Close modal
function closeModal() {
  payment.phone = "";
  phoneError.value = "";
  openModal.value = false;
}

// Payment
async function makePayment() {
  const phone = payment.phone.trim();
  const amount = selectedPlan.amount;

  if (!isValidPhone(phone)) {
    phoneError.value =
      "Invalid Kenyan number. Use 254XXXXXXXXX or 07/01XXXXXXXX";
    return;
  }

  phoneError.value = "";

  const confirmResult = await Swal.fire({
    title: "Confirm Payment",
    html: `<b>Plan:</b> ${selectedPlan.name}<br/><b>Phone:</b> ${phone}<br/><b>Amount:</b> KES ${amount}`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Proceed",
    cancelButtonText: "Cancel",
  });

  if (!confirmResult.isConfirmed) return;

  Swal.fire({
    title: "Processing...",
    text: "Check your phone for the STK prompt...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  try {
    const response = await initiatePayment({
      phone,
      amount,
      orderID: order.data.orderID,
      isPremium: 1,
    });
    pollPayment(response.reference);
  } catch (err) {
    Swal.fire("Error", "Failed to initiate payment. Try again.", "error");
  }
}

// Poll payment confirmation
async function pollPayment(trackID) {
  const poll = async () => {
    try {
      const res = await checkPaymentStatus(trackID);
      if (res.status === 1) {
        Swal.fire("Success!", "Payment confirmed!", "success").then(closeModal);
        order.data.status = "paid";
      } else if (res.status === 7 || res.status === 0) {
        setTimeout(poll, 5000);
      } else if (res.status === 2) {
        Swal.fire("Error", res.message, "error");
      }
    } catch (err) {
      console.error(err);
      setTimeout(poll, 5000);
    }
  };
  poll();
}
</script>
