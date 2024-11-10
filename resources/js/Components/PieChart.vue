<template>
  <div class="chart-container">
    <!-- Centered loading spinner -->
    <span v-if="isLoading" class="loading loading-dots loading-sm mt-10"></span>
    <!-- Canvas for the pie chart -->
    <canvas id="pieChart" v-show="!isLoading"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
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

const fetchChartData = async () => {
  isLoading.value = true;

  try {
    const response = await axios.get(`/get-pie-data`, {
      params: {
        selectedYear: props.selectedYear,
        selectedQuarter: props.selectedQuarter,
      },
    });

    const data = response.data;
    pieData.value.datasets[0].data = [data.male, data.female];

    if (chartInstance) {
      chartInstance.data = pieData.value;
      chartInstance.update();
    }
  } catch (error) {
    console.error('Error fetching chart data:', error);
  } finally {
    isLoading.value = false;
  }
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
      responsive: true, // Make sure chart resizes with screen
      maintainAspectRatio: false, // Allow the chart to resize freely based on container size
      plugins: {
        legend: {
          position: 'right',
          labels: {
            boxWidth: 16,
            font: {
              size: 16, // Increased font size for labels
              weight: 'bold', // Bold font weight
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
        datalabels: {
          display: false, // Disable data labels inside the chart
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

onMounted(async () => {
  await nextTick();
  createChart();
});
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;  
  min-height: 225px; 
  max-height: 225px; 
}

.loading {
  position: absolute;
  top: 40%;
  left: 50%;
  transform: translate(-50%, -50%); /* Center the spinner */
  z-index: 10; /* Ensure it appears on top of the chart */
}

canvas {
  display: block;
  width: 100%;
  height: 100%;
  z-index: 1; /* Ensure the chart stays behind the loading spinner */
}

@media (min-width: 1024px) {
  .chart-container {
    min-height: 225px; 
    max-height: 225px;
  }
}
</style>
