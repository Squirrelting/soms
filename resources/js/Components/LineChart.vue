<template>
  <div class="chart-container">
    <!-- Centered loading spinner -->
    <span v-if="isLoading" class="loading loading-dots loading-sm"></span>
    <!-- "No data available" message -->
    <p v-if="!isLoading && isDataEmpty" class="no-data">No data available for LineChart</p>
    <!-- Canvas for the line chart -->
    <canvas id="lineChart" v-show="!isLoading && !isDataEmpty"></canvas>
  </div>
</template>



<script setup>
import { ref, onMounted, watch, nextTick } from "vue";
import { Chart } from "chart.js/auto";
import axios from "axios";
import ChartDataLabels from "chartjs-plugin-datalabels";

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

const isLoading = ref(true); // Set to true initially to show the loading spinner

const lineData = ref({
  labels: [],
  datasets: [
    {
      label: "Minor Offenses",
      data: [],
      backgroundColor: "rgba(255, 206, 86, 0.2)",
      borderColor: "rgba(255, 206, 86, 1)",
      borderWidth: 2,
      tension: 0.3,
    },
    {
      label: "Major Offenses",
      data: [],
      backgroundColor: "rgba(75, 192, 75, 0.2)",
      borderColor: "rgba(75, 192, 75, 1)",
      borderWidth: 2,
      tension: 0.3,
    },
  ],
});

let chartInstance = null;

function formatDateForAxis(dateString) {
  const date = new Date(dateString);
  const options = { month: "short", day: "2-digit" };
  return date.toLocaleDateString("en-US", options).replace(",", ".");
}

function formatDateForTooltip(dateString) {
  const date = new Date(dateString);
  const options = { month: "short", day: "2-digit", year: "numeric" };
  return date.toLocaleDateString("en-US", options);
}

const originalDates = ref([]); // New array to store original dates from the database

const isDataEmpty = ref(false); // Flag to check if data is empty

const fetchChartData = () => {
  isLoading.value = true;
  isDataEmpty.value = false;

  axios
    .get("/get-line-data", {
      params: {
        selectedYear: props.selectedYear,
        selectedQuarter: props.selectedQuarter,
      },
    })
    .then((response) => {
      const data = response.data;

      lineData.value.labels = [];
      lineData.value.datasets[0].data = [];
      lineData.value.datasets[1].data = [];
      originalDates.value = [];

      const offenseCounts = {};

      data.minor.forEach((offense) => {
        const committedDate = offense.date;
        const count = offense.count;
        if (committedDate) {
          offenseCounts[committedDate] =
            offenseCounts[committedDate] || { minor: 0, major: 0 };
          offenseCounts[committedDate].minor += count;
        }
      });

      data.major.forEach((offense) => {
        const committedDate = offense.date;
        const count = offense.count;
        if (committedDate) {
          offenseCounts[committedDate] =
            offenseCounts[committedDate] || { minor: 0, major: 0 };
          offenseCounts[committedDate].major += count;
        }
      });

      for (const date in offenseCounts) {
        const minorCount = offenseCounts[date].minor;
        const majorCount = offenseCounts[date].major;
        if (minorCount > 0 || majorCount > 0) {
          lineData.value.labels.push(formatDateForAxis(date));
          originalDates.value.push(date);
          lineData.value.datasets[0].data.push(minorCount);
          lineData.value.datasets[1].data.push(majorCount);
        }
      }

      if (
        lineData.value.labels.length === 0 &&
        lineData.value.datasets[0].data.length === 0 &&
        lineData.value.datasets[1].data.length === 0
      ) {
        isDataEmpty.value = true;
      } else if (chartInstance) {
        chartInstance.data = lineData.value;
        chartInstance.update();
      } else {
        createChart();
      }

      isLoading.value = false;
    })
    .catch((error) => {
      console.error("Error fetching chart data:", error);
      isLoading.value = false;
    });
};

const createChart = () => {
  const ctx = document.getElementById("lineChart").getContext("2d");

  if (chartInstance) {
    chartInstance.destroy();
  }

  chartInstance = new Chart(ctx, {
    type: "line",
    data: lineData.value,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: {
            boxWidth: 10, // Set the width of the box
            boxHeight: 10, // Set the height of the box
            padding: 10,   // Optional: space between boxes and labels
            font: {
              size: 12,    // Optional: font size for label text
            },
          },
        },
        title: {
          display: true,
          text: "Offenses Trends",
          font: {
            size: 18,
            family: "Arial",
            weight: "bold",
          },
          color: "black",
          align: "center",
        },
        datalabels: {
          display: false,
        },
        tooltip: {
          callbacks: {
            title: (tooltipItems) => {
              const index = tooltipItems[0].dataIndex;
              return formatDateForTooltip(originalDates.value[index]);
            },
          },
        },
      },
      scales: {
        x: {
          ticks: {
            autoSkip: true,
            maxRotation: 0,
            minRotation: 0,
          },
        },
        y: {
          beginAtZero: true,
          ticks: {
            callback: function (value) {
              return Number.isInteger(value) ? value : null;
            },
            stepSize: 1,
          },
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
  width: 100%;  
  min-height: 225px; 
  max-height: 225px; 
}

.loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%); /* Center the spinner */
  z-index: 10; /* Ensure it appears on top of the chart */
}

.no-data {
  text-align: center;
  margin-top: 100px;
  font-size: 1.2rem;
  color: #666;
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
