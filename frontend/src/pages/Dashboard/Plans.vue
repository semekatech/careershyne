<template>
  <section class="bg-white py-16">
    <div class="max-w-5xl mx-auto px-4">
      <div
        class="bg-white p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start"
      >
        <!-- Budget Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-2">Pay As You Go</h3>
          <p>
            Only pay for what you use. Build your own plan based on your needs.
          </p>
          <br />
          <p class="text-gray-600 mb-6">
            Adjust the slider to see the corresponding features.
          </p>

          <label class="block text-gray-700 text-sm font-medium mb-2"
            >Amount</label
          >
          <div
            class="flex items-center bg-gray-50 border border-gray-300 rounded-lg p-2 mb-6"
          >
            <!-- <span class="mr-2">🇰🇪</span> -->
            <span class="text-gray-700 mr-3">KES</span>
            <input
              type="number"
              readonly
              v-model="budget"
              min="100"
              max="5000"
              step="100"
              class="bg-transparent text-gray-900 w-24 outline-none"
            />
          </div>

          <input
            type="range"
            min="100"
            max="5000"
            step="100"
            v-model="budget"
            class="w-full accent-orange-500 appearance-none h-2 rounded-lg outline-none cursor-pointer bg-gray-200"
          />
        </div>

        <!-- Items Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-6">You Will Receive</h3>

          <ul class="mt-8 space-y-4 text-sm">
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2"
                  >description</span
                >CV Items</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.cv
              }}</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2">mail</span>Cover
                Letters</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.coverLetter
              }}</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2"
                  >check_circle_outline</span
                >Eligibility Checks</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.eligibility
              }}</span>
            </li>
            <li class="flex justify-between items-center">
              <span class="flex items-center"
                ><span class="material-icons text-primary mr-2">email</span
                >Email Credits</span
              >
              <span class="font-semibold text-primary text-lg">{{
                items.email
              }}</span>
            </li>
          </ul>

          <hr class="border-gray-200 mb-6" />
          <div
            class="flex justify-between text-lg font-bold text-gray-900 mb-6"
          >
            <span>Total cost</span>
            <span>KES {{ budget }}</span>
          </div>

          <button
            :disabled="items.total === 0"
            @click="openModal = true"
            class="w-full py-3 rounded-lg font-semibold text-white bg-orange-500 hover:bg-orange-600 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Pay {{ budget }} Via STK
          </button>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div
      v-if="openModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @keydown.esc="closeModal"
    >
      <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
        <h3 class="text-lg font-bold mb-4">Enter your phone number</h3>

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
            class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition"
          >
            Pay KES {{ budget }}
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
import { ref, reactive, computed } from "vue";
import Swal from "sweetalert2";
import renewPlanService from "@/services/renewPlan";
const { initiatePayment, checkPaymentStatus } = renewPlanService;

// Budget (in multiples of 100)
const budget = ref(100);

// Prices per item
const prices = reactive({
  cv: 20,
  coverLetter: 15,
  eligibility: 15,
  email: 10,
});

// Computed allocation
const items = computed(() => {
  const P = budget.value;
  if (P <= 0 || P % 100 !== 0)
    return { cv: 0, coverLetter: 0, eligibility: 0, email: 0, total: 0 };

  // Split budget based on percentage
  const allocation = {
    cv: P * 0.4,
    coverLetter: P * 0.3,
    eligibility: P * 0.2,
    email: P * 0.1,
  };

  // Calculate how many of each item can be bought
  const cv = Math.floor(allocation.cv / prices.cv);
  const coverLetter = Math.floor(allocation.coverLetter / prices.coverLetter);
  const eligibility = Math.floor(allocation.eligibility / prices.eligibility);
  const email = Math.floor(allocation.email / prices.email);

  // Calculate total spent value
  const total =
    cv * prices.cv +
    coverLetter * prices.coverLetter +
    eligibility * prices.eligibility +
    email * prices.email;

  return { cv, coverLetter, eligibility, email, total };
});

// Modal & Payment
const openModal = ref(false);
const payment = reactive({ phone: "" });
const phoneError = ref("");

// Order
const order = reactive({
  data: {
    amount: 0,
    orderID: Math.floor(Math.random() * 1000000),
    status: "pending",
  },
});

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
  order.data.amount = items.value.total;
  const phone = payment.phone.trim();
  if (!isValidPhone(phone)) {
    phoneError.value =
      "Invalid Kenyan number. Use 254XXXXXXXXX or 07/01XXXXXXXX";
    return;
  }
  phoneError.value = "";

  const confirmResult = await Swal.fire({
    title: "Confirm Payment",
    html: `<b>Phone:</b> ${phone}<br/><b>Amount:</b> KES ${order.data.amount}`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Proceed",
    cancelButtonText: "Cancel",
  });
  if (!confirmResult.isConfirmed) return;

  Swal.fire({
    title: "Processing...",
    text: "Check your phone...",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  try {
    const response = await initiatePayment({
      phone,
      amount: order.data.amount,
      orderID: order.data.orderID,
      isPremium: 0,
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
