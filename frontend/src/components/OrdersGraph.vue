<template>
  <div class="bg-white rounded-xl shadow-md p-6 h-96">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Paid Orders (by Day)</h2>
    <Line v-if="chartData" :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale);

const props = defineProps({
  graphData: {
    type: Array,
    default: () => [],
  },
});

const chartData = ref(null);

watch(
  () => props.graphData,
  (data) => {
    if (data && data.length > 0) {
      chartData.value = {
        labels: data.map((d) => d.date),
        datasets: [
          {
            label: "Paid Orders",
            data: data.map((d) => d.total_orders),
            borderColor: "rgba(75, 192, 192, 1)",
            backgroundColor: "rgba(75, 192, 192, 0.2)",
            tension: 0.4,
            fill: true,
          },
          {
            label: "Total Amount",
            data: data.map((d) => d.total_amount),
            borderColor: "rgba(255, 99, 132, 1)",
            backgroundColor: "rgba(255, 99, 132, 0.2)",
            tension: 0.4,
            fill: true,
          },
        ],
      };
    }
  },
  { immediate: true }
);

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: "top" },
    title: { display: true, text: "Paid Orders Trend" },
  },
};
</script>
