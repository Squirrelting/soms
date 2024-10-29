<template>
    <div class="chart-container">
      <canvas id="lineChart"></canvas>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from "vue";
  import { Chart } from "chart.js/auto";
  import axios from "axios";
  import ChartDataLabels from "chartjs-plugin-datalabels";
  
  // Register the plugin
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
  
  // Reactive data structure
  const lineData = ref({
    labels: [], // Empty initially
    datasets: [
      {
        label: "Minor Offenses",
        data: [],
        backgroundColor: "rgba(75, 192, 192, 0.2)",
        borderColor: "rgba(75, 192, 192, 1)",
        borderWidth: 2,
        tension: 0.3,
      },
      {
        label: "Major Offenses",
        data: [],
        backgroundColor: "rgba(255, 99, 132, 0.2)",
        borderColor: "rgba(255, 99, 132, 1)",
        borderWidth: 2,
        tension: 0.3,
      },
    ],
  });
  
  let chartInstance = null;
  
  // Utility function for formatting dates
  function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { month: "short", day: "2-digit", year: "numeric" };
    return date.toLocaleDateString("en-US", options).replace(",", ".");
  }
  
  // Fetch chart data from the API
  const fetchChartData = () => {
    axios
      .get("/get-line-data", {
        params: {
          selectedYear: props.selectedYear,
          selectedQuarter: props.selectedQuarter,
        },
      })
      .then((response) => {
        const data = response.data;
  
        // Clear existing data
        lineData.value.labels = [];
        lineData.value.datasets[0].data = [];
        lineData.value.datasets[1].data = [];
  
        const offenseCounts = {};
  
        // Populate minor offenses
        data.minor.forEach((offense) => {
          const committedDate = formatDate(offense.date);
          const count = offense.count;
          if (committedDate) {
            offenseCounts[committedDate] = offenseCounts[committedDate] || { minor: 0, major: 0 };
            offenseCounts[committedDate].minor += count;
          }
        });
  
        // Populate major offenses
        data.major.forEach((offense) => {
          const committedDate = formatDate(offense.date);
          const count = offense.count;
          if (committedDate) {
            offenseCounts[committedDate] = offenseCounts[committedDate] || { minor: 0, major: 0 };
            offenseCounts[committedDate].major += count;
          }
        });
  
        // Populate chart data with labels and dataset values
        for (const date in offenseCounts) {
          const minorCount = offenseCounts[date].minor;
          const majorCount = offenseCounts[date].major;
          if (minorCount > 0 || majorCount > 0) {
            lineData.value.labels.push(date);
            lineData.value.datasets[0].data.push(minorCount);
            lineData.value.datasets[1].data.push(majorCount);
          }
        }
  
        // Update the chart if it already exists, otherwise create a new chart
        if (chartInstance) {
          chartInstance.data = lineData.value;
          chartInstance.update();
        } else {
          createChart();
        }
      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
      });
  };
  
  // Function to create the chart
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
        plugins: {
          legend: {
            position: "bottom", // Move the legend to the bottom
          },
          title: {
            display: true,
            text: "Offenders by Sex",
            font: {
              size: 18,
              family: "Arial",
              weight: "bold",
            },
            color: "black",
            align: "center",
          },
          datalabels: {
            color: "#000",
            anchor: "end",
            align: "end",
            font: {
              weight: "bold",
              size: 10,
            },
            formatter: (value) => `${value}`, // Show data value
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return Number.isInteger(value) ? value : null; // Show whole numbers only
              },
              stepSize: 1, // Ensure step size of 1
            },
          },
        },
      },
    });
  };
  
  // Watch props and re-fetch data when the selectedYear or selectedQuarter changes
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
    overflow: hidden;
  }
  </style>
  