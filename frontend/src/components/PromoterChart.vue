<template>
  <div class="w-full min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Left Section: Instructions + Order Details -->
      <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-3xl font-bold mb-6">
          Payment for Order #{{ orderId }}
        </h2>

        <div v-if="order">
          <p class="mb-2"><b>Order Date:</b> KES {{ order.data.created_at }}</p>
          <p class="mb-2"><b>Amount:</b> KES {{ order.data.amount }}</p>
          <p class="mb-2">
            <b>Status:</b>
            <span
              class="capitalize"
              :class="
                order.data.status === 'pending'
                  ? 'text-yellow-600'
                  : 'text-green-600'
              "
            >
              {{ order.data.status }}
            </span>
          </p>

          <div
            class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg"
          >
            <h3 class="font-semibold text-lg mb-2">Instructions</h3>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
              <li>Confirm your details are correct before making payment.</li>
              <li>Payment is processed securely through our system.</li>
              <li>
                Once payment is successful, you’ll receive a confirmation email.
              </li>
              <li>Your Order will be delivered within 24 hours.</li>
              <li>
                Use your Order ID <b>#{{ orderId }}</b> for reference.
              </li>
            </ul>
          </div>
        </div>

        <div v-else class="text-gray-500">Loading order...</div>
      </div>

      <!-- Right Section: Payment Form -->
      <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h3 class="text-2xl font-bold mb-6">Complete Your Payment</h3>
        <form @submit.prevent="makePayment" class="space-y-4">
          <div>
            <label class="block text-gray-700 mb-1">Payment Method</label>
            <select
              v-model="payment.method"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
            >
              <option value="mpesa">M-Pesa</option>
              <option value="card">Credit/Debit Card</option>
            </select>
          </div>

          <div v-if="payment.method === 'mpesa'">
            <label class="block text-gray-700 mb-1">M-Pesa Number</label>
            <input
              type="text"
              v-model="payment.phone"
              placeholder="Enter M-Pesa number"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
            />
          </div>

          <div v-if="payment.method === 'card'">
            <label class="block text-gray-700 mb-1">Card Number</label>
            <input
              type="text"
              placeholder="XXXX XXXX XXXX XXXX"
              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
            />

            <div class="grid grid-cols-2 gap-4 mt-2">
              <input
                type="text"
                placeholder="MM/YY"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
              />
              <input
                type="text"
                placeholder="CVV"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200"
              />
            </div>
          </div>

          <button
            type="submit"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
          >
            Pay Now
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { getCvOrder } from "@/services/cvOrderService";

const route = useRoute();
const orderId = route.params.id;
const order = ref(null);

const payment = ref({
  method: "mpesa",
  phone: "",
});

onMounted(async () => {
  order.value = await getCvOrder(orderId);
});

const makePayment = () => {
  alert(`Processing ${payment.value.method} payment for Order #${orderId}`);
};
</script>
