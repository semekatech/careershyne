<template>
  <section class="bg-white py-16">
    <div class="max-w-5xl mx-auto px-4">

      <div class="bg-white p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

        <!-- Budget Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-2">See what you get</h3>
          <p class="text-gray-600 mb-6">
            Slide or input the amount you want to see the items you will receive.
          </p>

          <label class="block text-gray-700 text-sm font-medium mb-2">Amount</label>
          <div class="flex items-center bg-gray-50 border border-gray-300 rounded-lg p-2 mb-6">
            <span class="mr-2">🇰🇪</span>
            <span class="text-gray-700 mr-3">KES</span>
            <input type="number" v-model="budget" min="100" max="5000" step="100" class="bg-transparent text-gray-900 w-24 outline-none"/>
          </div>

          <input type="range" min="100" max="5000" step="100" v-model="budget" class="w-full accent-orange-500"/>
        </div>

        <!-- Items Card -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
          <h3 class="text-xl font-bold text-gray-900 mb-6">You Will Receive</h3>

          <ul class="space-y-4 mb-6">
            <li class="flex justify-between"><span class="text-gray-700">CV Items</span><span class="text-orange-600 font-semibold">{{ items.cv }}</span></li>
            <li class="flex justify-between"><span class="text-gray-700">Cover Letters</span><span class="text-orange-600 font-semibold">{{ items.coverLetter }}</span></li>
            <li class="flex justify-between"><span class="text-gray-700">Eligibility Checks</span><span class="text-orange-600 font-semibold">{{ items.eligibility }}</span></li>
            <li class="flex justify-between"><span class="text-gray-700">Email Credits</span><span class="text-orange-600 font-semibold">{{ items.email }}</span></li>
          </ul>

          <hr class="border-gray-200 mb-6"/>
          <div class="flex justify-between text-lg font-bold text-gray-900 mb-6">
            <span>Total cost</span>
            <span>KES {{ items.total }}</span>
          </div>

          <button 
            :disabled="items.total === 0"
            @click="openModal = true" 
            class="w-full py-3 rounded-lg font-semibold text-white bg-orange-500 hover:bg-orange-600 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            Pay {{ items.total }} Via STK
          </button>
        </div>

      </div>
    </div>

    <!-- Modal -->
    <div v-if="openModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @keydown.esc="closeModal">
      <div class="bg-white rounded-xl p-6 w-full max-w-md relative">
        <h3 class="text-lg font-bold mb-4">Enter your phone number</h3>

        <label class="block mb-2 text-gray-700">Phone Number</label>
        <input 
          v-model="payment.phone" 
          type="tel" 
          placeholder="e.g., 254712345678 or 0712345678" 
          class="w-full border border-gray-300 rounded-lg p-2 mb-1"/>
        <p v-if="phoneError" class="text-red-600 text-sm mb-2">{{ phoneError }}</p>

        <div class="flex justify-between items-center mt-4">
          <button @click="makePayment" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
            Pay KES {{ items.total }}
          </button>
          <button @click="closeModal" class="text-gray-700 px-4 py-2 rounded-lg border hover:bg-gray-100 transition">
            Cancel
          </button>
        </div>
      </div>
    </div>

  </section>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import Swal from 'sweetalert2'
import { initiatePayment, checkPaymentStatus } from '@/services/renewPlan'

// Budget
const budget = ref(100)

// Prices
const prices = reactive({ cv: 20, coverLetter: 20, eligibility: 20, email: 10 })

// Computed items
const items = computed(() => {
  const P = budget.value
  if (P <= 0 || P % 100 !== 0) return { cv: 0, coverLetter: 0, eligibility: 0, email: 0, total: 0 }
  const n = Math.floor(P / 100)
  const cv = 2 * n
  const eligibility = n
  const email = 2 * Math.floor(P / 200)
  const coverLetter = 2 * n - Math.floor(P / 200)
  const total = cv*prices.cv + coverLetter*prices.coverLetter + eligibility*prices.eligibility + email*prices.email
  return { cv, coverLetter, eligibility, email, total }
})

// Modal & Payment
const openModal = ref(false)
const payment = reactive({ phone: '' })
const phoneError = ref('')

// Order
const order = reactive({ data: { amount: 0, orderID: Math.floor(Math.random()*1000000), status: 'pending' } })

// Validate Kenyan phone
function isValidPhone(num) {
  const clean = num.replace(/\D/g,'')
  return /^254\d{9}$/.test(clean) || /^(07|01)\d{8}$/.test(clean)
}

// Close modal
function closeModal() {
  payment.phone = ''
  phoneError.value = ''
  openModal.value = false
}

// Payment
async function makePayment() {
  order.data.amount = items.value.total
  const phone = payment.phone.trim()
  if (!isValidPhone(phone)) {
    phoneError.value = 'Invalid Kenyan number. Use 254XXXXXXXXX or 07/01XXXXXXXX'
    return
  }
  phoneError.value = ''

  const confirmResult = await Swal.fire({
    title: "Confirm Payment",
    html: `<b>Phone:</b> ${phone}<br/><b>Amount:</b> KES ${order.data.amount}<br/><b>Order ID:</b> ${order.data.orderID}`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Proceed",
    cancelButtonText: "Cancel"
  })
  if (!confirmResult.isConfirmed) return

  Swal.fire({ title: "Processing...", text: "Check your phone...", allowOutsideClick:false, didOpen:()=>Swal.showLoading() })

  try {
    const response = await initiatePayment({ phone, amount: order.data.amount, orderID: order.data.orderID })
    pollPayment(response.reference)
  } catch (err) {
    Swal.fire("Error", "Failed to initiate payment. Try again.", "error")
  }
}

// Poll payment confirmation
async function pollPayment(trackID) {
  const poll = async () => {
    try {
      const res = await checkPaymentStatus(trackID)
      if(res.status === 1) {
        Swal.fire("Success!", "Payment confirmed!", "success").then(closeModal)
        order.data.status = "paid"
      } else if(res.status === 7 || res.status === 0) {
        setTimeout(poll, 5000)
      } else if(res.status === 2) {
        Swal.fire("Error", res.message, "error")
      }
    } catch(err) {
      console.error(err)
      setTimeout(poll, 5000)
    }
  }
  poll()
}
</script>
