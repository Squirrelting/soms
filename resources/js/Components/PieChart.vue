<template>
  <div class="chart-container">
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
      data: [1, 0], // Initial data for Male - 1, Female - 0
      backgroundColor: ['#00FFFF', '#FFC0CB'], // Light pink and cyan colors
      borderColor: ['#00FFFF', '#FFC0CB'], // Same as background for solid color effect
      borderWidth: 1,
    },
  ],
});

let chartInstance = null;

const fetchChartData = () => {
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
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            boxWidth: 16,
            font: {
              size: 18, // Normal font size for labels
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
  height: 200px; 
  overflow: hidden;
}
</style>
