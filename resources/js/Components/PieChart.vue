<template>
  <div class="chart-container">
    <span v-if="isLoading" class="loading loading-dots loading-sm mt-10"></span>
    <canvas id="pieChart"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import axios from 'axios';

const props = defineProps({
  selectedYear: {
    type: String,
    required: true,
  },
  selectedQuarter: {
    type: String,
    required: true,
  },
});

const pieData = ref({
  labels: ['Male', 'Female'],
  datasets: [
    {
      label: 'Offenders by Sex',
      data: [1, 0], 
      backgroundColor: ['#00FFFF', '#FFC0CB'], // Light pink and cyan colors
      borderColor: ['#00FFFF', '#FFC0CB'], 
      borderWidth: 1,
    },
  ],
});

let chartInstance = null;
const isLoading = ref(false);

const fetchChartData = () => {
  isLoading.value = true;
  axios
    .get(`/get-pie-data`, {
      params: {
        selectedYear: props.selectedYear,
        selectedQuarter: props.selectedQuarter,
      },
    })
    .then((response) => {
      const data = response.data;
      pieData.value.datasets[0].data = [data.male, data.female];

      if (chartInstance) {
        chartInstance.data = pieData.value;
        chartInstance.update();
        isLoading.value = false;
      }
    })
    .catch((error) => {
      console.error('Error fetching chart data:', error);
    });
};

const createChart = () => {
  const ctx = document.getElementById('pieChart').getContext('2d');

  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: pieData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false, // Allow dynamic resizing
      plugins: {
        legend: {
          position: 'right',
          labels: {
            boxWidth: 16,
            font: {
              size: 12, // Normal font size for labels
            },
            generateLabels: (chart) => {
              const data = chart.data;
              return data.labels.map((label, index) => ({
                text: `${label} - ${data.datasets[0].data[index]}`, // Format as 'Male - 1' and 'Female - 0'
                fillStyle: data.datasets[0].backgroundColor[index],
                strokeStyle: data.datasets[0].borderColor[index],
                lineWidth: data.datasets[0].borderWidth,
              }));
            },
          },
        },
        title: {
          display: true,
          text: 'Offenders by Sex',
          font: {
            size: 18, // Title text size
            family: 'Arial', // Font family (optional)
            weight: 'bold', // Font weight (optional)
          },
          color: 'black', // Title color
          align: 'center', // Center the text
        },
      },
    },
  });
};

watch(
  () => [props.selectedYear, props.selectedQuarter],
  () => {
    fetchChartData();
  },
  { immediate: true }
);

onMounted(() => {
  createChart();
});
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;  /* Full width */
  height: 100%; /* Full height */
  min-height: 225px; /* Minimum height */
  max-height: 225px; /* Constrains the height */
  overflow: hidden; /* Prevents overflow */

}
</style>