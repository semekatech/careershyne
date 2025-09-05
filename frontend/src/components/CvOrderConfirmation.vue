<template>
  <div class="w-full min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
      
      <!-- Left Section: Instructions + Order Details -->
      <div class="bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-3xl font-bold mb-6">
          Payment for Order #{{ orderId }}
        </h2>

        <div v-if="order">
          <p class="mb-2"><b>Order Date:</b> {{ order.data.created_at }}</p>
          <p class="mb-2"><b>Amount:</b> KES {{ order.data.amount }}</p>
          <p class="mb-2"><b>Status:</b> 
            <span class="capitalize" :class="order.data.status === 'pending' ? 'text-yellow-600' : 'text-green-600'">
              {{ order.data.status }}
            </span>
          </p>

          <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
            <h3 class="font-semibold text-lg mb-2">Instructions</h3>
            <ul class="list-disc pl-5 text-gray-700 space-y-1">
              <li>Confirm your details are correct before making payment.</li>
              <li>Payment is processed securely through our system.</li>
              <li>Once payment is successful, you’ll receive a confirmation email.</li>
              <li>Use your Order ID <b>#{{ orderId }}</b> for reference.</li>
            </ul>
          </div>
        </div>
        <div v-else class="text-gray-500">Loading order...</div>
      </div>

      <!-- Right Section: Payment Form / Success -->
      <div class="bg-white p-8 rounded-2xl shadow-lg">
        <template v-if="!paymentSuccess && order?.data.status !== 'paid'">
          <h3 class="text-2xl font-bold mb-6">Complete Your Payment</h3>
          <form @submit.prevent="makePayment" class="space-y-4">
            <div>
              <label class="block text-gray-700 mb-1">Payment Method</label>
              <select v-model="payment.method" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
                <option value="mpesa">M-Pesa</option>
              </select>
            </div>

            <div v-if="payment.method === 'mpesa'">
              <label class="block text-gray-700 mb-1">M-Pesa Number</label>
              <input type="text" v-model="payment.phone" placeholder="Enter M-Pesa number"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" />
            </div>

            <button type="submit"
              class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
              Pay Now
            </button>
          </form>
        </template>

        <!-- ✅ Success Message -->
        <template v-else>
          <div class="text-center">
            <h3 class="text-2xl font-bold text-green-600 mb-4">🎉 Payment Successful!</h3>
            <p class="text-gray-700 mb-6">Thank you. Your order has been paid successfully.</p>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { getCvOrder, initiatePayment, checkPaymentStatus } from "@/services/cvOrderService"; 
import Swal from "sweetalert2";

const route = useRoute();
const orderId = route.params.id;
const order = ref(null);
const paymentSuccess = ref(false);

const payment = ref({
  method: "mpesa",
  phone: ""
});

onMounted(async () => {
  order.value = await getCvOrder(orderId);

  // ✅ If already paid, show success immediately
  if (order.value?.data.status === "paid") {
    paymentSuccess.value = true;
  }
});

const isValidPhone = (phone) => /^254\d{9}$/.test(phone);

const makePayment = async () => {
  if (!isValidPhone(payment.value.phone)) {
    Swal.fire("Invalid Number", "Please enter a valid M-Pesa number starting with 254 and 12 digits long.", "error");
    return;
  }

  const confirmResult = await Swal.fire({
    title: "Confirm Payment",
    html: `We are about to initiate an STK push.<br/><br/>
           <b>Phone:</b> ${payment.value.phone}<br/>
           <b>Amount:</b> KES ${order.value.data.amount}<br/>
           <b>Order ID:</b> ${order.value.data.orderID}`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Proceed",
    cancelButtonText: "Cancel"
  });

  if (!confirmResult.isConfirmed) return;

  Swal.fire({
    title: "Processing...",
    text: "Initiating payment, please check your phone...",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  try {
    const response = await initiatePayment({
      phone: payment.value.phone,
      amount: order.value.data.amount,
      orderID: order.value.data.orderID
    });

    confirmPayment(response.reference);
  } catch (error) {
    Swal.fire("Error", "Failed to initiate payment. Please try again.", "error");
  }
};

async function confirmPayment(trackID) {
  Swal.fire({
    title: "Confirming Payment...",
    text: "Please wait while we confirm your payment.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  const poll = async () => {
    try {
      const res = await checkPaymentStatus(trackID);

      if (res.status == 1) {
        Swal.fire("Success!", "Order Payment confirmed successfully!", "success").then(() => {
          paymentSuccess.value = true;
          order.value.data.status = "paid";
        });
      } else if (res.status == 7 || res.status == 0) {
        setTimeout(poll, 5000);
      } else if (res.status == 2) {
        Swal.fire("Oops!", res.message, "error");
      }
    } catch (err) {
      console.log("Payment status check failed.", err);
      setTimeout(poll, 5000);
    }
  };
  poll();
}
</script>
