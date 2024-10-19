<template>
  <div class="chart-container">
    <canvas id="pieChart" width="400" height="400"></canvas> <!-- Set dimensions -->
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart } from 'chart.js/auto';
import axios from 'axios';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(ChartDataLabels);

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
      data: [0, 0],
      backgroundColor: ['rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'],
      borderColor: ['rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
      borderWidth: 1,
    },
  ],
});

let chartInstance = null;

const fetchChartData = () => {
  axios.get(`/get-pie-data`, {
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
    type: 'pie',
    data: pieData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false, // Adjusts to fit the container
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
  height: 200px; /* Set height for the chart container */
  overflow: hidden; /* Hide overflow if necessary */
}
</style>
