<template>
  <div>
    <canvas id="pieChart"></canvas>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch } from 'vue';
import Chart from 'chart.js/auto';

// Props passed from the parent component
const props = defineProps({
  chartData: {
    type: Object,
    required: true,
  },
});

let chartInstance = null;

// Method to render or update the chart
const renderChart = () => {
  const ctx = document.getElementById('pieChart').getContext('2d');

  // Destroy the old chart if it exists to avoid overlapping charts
  if (chartInstance) {
    chartInstance.destroy();
  }

  // Create a new chart instance
  chartInstance = new Chart(ctx, {
    type: 'pie',
    data: props.chartData,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: 'Offenders by Sex',
        },
      },
    },
  });
};

// Watch for changes in the `chartData` prop and re-render the chart
watch(
  () => props.chartData,
  (newData) => {
    renderChart(); // Re-render the chart when chartData changes
  },
  { deep: true }
);

// Run the chart rendering function on mount
onMounted(() => {
  renderChart();
});

// Cleanup chart instance when the component is unmounted
onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>


