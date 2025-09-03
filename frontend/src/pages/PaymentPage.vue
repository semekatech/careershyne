<template>
  <div class="max-w-2xl mx-auto my-10 p-6 bg-white shadow rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Payment for Order #{{ orderId }}</h2>
    <div v-if="order">
      <p><b>Name:</b> {{ order.fullname }}</p>
      <p><b>Email:</b> {{ order.email }}</p>
      <p><b>Phone:</b> {{ order.phone }}</p>
      <p class="mt-4 text-green-600">Proceed to payment here...</p>
    </div>
    <div v-else class="text-gray-500">Loading order...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { getCvOrder } from "@/services/cvOrderService";

const route = useRoute();
const orderId = route.params.id;
const order = ref(null);

onMounted(async () => {
  order.value = await getCvOrder(orderId);
});
</script>
