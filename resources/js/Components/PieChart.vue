<template>
  <div>
    <canvas id="pieChart"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import axios from 'axios';
import ChartDataLabels from 'chartjs-plugin-datalabels'; // Import the datalabels plugin

// Register the plugin
Chart.register(ChartDataLabels);

// Props for start and end dates
const props = defineProps({
  startDate: {
    type: String,
    required: true,
  },
  endDate: {
    type: String,
    required: true,
  },
});

// Reactive offense data and chart data
const offenseData = ref({
  male: 0,
  female: 0,
});
const pieData = ref({
  labels: ['Male', 'Female'],
  datasets: [
    {
      label: 'Offenders by Sex',
      data: [0, 0], // Initialize with zero data
      backgroundColor: ['rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'],
      borderColor: ['rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
      borderWidth: 1,
    },
  ],
});

// Chart instance
let chartInstance = null;

// Function to fetch and update chart data based on date range
const fetchChartData = () => {
  axios.get(`/get-pie-data`, {
    params: {
      start_date: props.startDate,
      end_date: props.endDate,
    },
  })
  .then((response) => {
    const data = response.data;
    pieData.value.datasets[0].data = [data.male, data.female];
    pieData.value.labels = [`Male - ${data.male}`, `Female - ${data.female}`]; // Update labels with counts

    if (chartInstance) {
      chartInstance.data = pieData.value;
      chartInstance.update();
    }
  })
  .catch((error) => {
    console.error('Error fetching chart data:', error);
  });
};

// Function to initialize the chart
const createChart = () => {
  const ctx = document.getElementById('pieChart').getContext('2d');

  if (chartInstance) {
    chartInstance.destroy();  // Destroy the old chart if it exists to avoid overlapping
  }

  chartInstance = new Chart(ctx, {
    type: 'pie',
    data: pieData.value,
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
        datalabels: {
          color: '#000', // Customize the label color
          anchor: 'center',
          align: 'center',
          font: {
            weight: 'bold',
            size: 14, // Customize label size
          },
          formatter: (value, ctx) => {
            const total = ctx.chart.data.datasets[0].data.reduce((sum, val) => sum + val, 0); // Calculate the total
            const percentage = total > 0 ? (value / total * 100).toFixed(0) : 0; // Calculate percentage
            return `${percentage}%`; // Display the percentage
          },
        },
      },
    },
  });
};

// Watch for changes in the start and end dates and fetch data when they change
watch(
  () => [props.startDate, props.endDate],
  () => {
    fetchChartData(); // Fetch data on date change
  },
  { immediate: true }
);

// Create the chart when the component is mounted
onMounted(() => {
  createChart();
});
</script>

<style scoped>
/* You can add custom styles if needed */
</style>
