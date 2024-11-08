<template>
  <div class="chart-container">
    <span v-if="isLoading" class="loading loading-dots loading-sm"></span>
    <canvas id="lineChart"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
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

const isLoading = ref(false);

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

function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { month: "short", day: "2-digit", year: "numeric" };
  return date.toLocaleDateString("en-US", options).replace(",", ".");
}

const fetchChartData = () => {
  isLoading.value = true;
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

      const offenseCounts = {};

      data.minor.forEach((offense) => {
        const committedDate = formatDate(offense.date);
        const count = offense.count;
        if (committedDate) {
          offenseCounts[committedDate] =
            offenseCounts[committedDate] || { minor: 0, major: 0 };
          offenseCounts[committedDate].minor += count;
        }
      });

      data.major.forEach((offense) => {
        const committedDate = formatDate(offense.date);
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
          lineData.value.labels.push(date);
          lineData.value.datasets[0].data.push(minorCount);
          lineData.value.datasets[1].data.push(majorCount);
        }
      }

      if (chartInstance) {
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
      maintainAspectRatio: false, // Allow dynamic resizing
      plugins: {
        legend: {
          position: "bottom",
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
      },
      scales: {
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
  width: 100%;  /* Full width */
  height: 100%; /* Full height */
  min-height: 225px; /* Minimum height */
  max-height: 225px; /* Constrains the height */
  overflow: hidden; /* Prevents overflow */

}
</style>
