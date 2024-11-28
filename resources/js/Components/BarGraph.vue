<template>
    <div class="chart-container">
      <!-- Centered loading spinner -->
      <span v-if="isLoading" class="loading loading-dots loading-sm mt-10"></span>
      <!-- "No data available" message -->
      <p v-if="!isLoading && isDataEmpty" class="no-data">No data available for BarChart.</p>
      <!-- Canvas for the bar chart -->
      <canvas id="barChart" v-show="!isLoading && !isDataEmpty"></canvas>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch, nextTick } from "vue";
  import { Chart } from "chart.js/auto"; // Import Chart from chart.js/auto
  import axios from "axios";
  
  // Props to pass the start and end dates
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
  
  const chartInstance = ref(null);
  const isLoading = ref(true);
  const chartData = ref([]);
  const isDataEmpty = ref(false); // This will track whether data is available
  
  // Function to fetch bar chart data from the server
  const fetchBarData = async () => {
      isLoading.value = true;
      isDataEmpty.value = false; // Reset isDataEmpty before making the request
  
      try {
          const response = await axios.get("/get-bar-data", {
              params: {
                  selectedYear: props.selectedYear,
                  selectedQuarter: props.selectedQuarter,
              },
          });
  
          chartData.value = response.data;

  
          // If no data is returned, set isDataEmpty to true
          isDataEmpty.value = chartData.value.length === 0;
  
          // Wait for DOM to update before rendering the chart
          await nextTick();

          if (!isDataEmpty.value) {
              renderChart(); // Render the chart after fetching data
          }
      } catch (error) {
          isDataEmpty.value = true; // Show "No data available" in case of error
      } finally {
          isLoading.value = false;
      }
  };
  
  // Watch for changes in selectedYear and selectedQuarter props
  watch(
  [() => props.selectedYear, () => props.selectedQuarter],
  async () => {
    await nextTick(); // Ensure DOM updates
    fetchBarData(); // Fetch new data
  }
);

  
  // Function to render the bar chart
  const renderChart = () => {
    if (chartInstance.value) {
        chartInstance.value.destroy();
    }

    const barChartRef = document.getElementById("barChart"); // Get the canvas element

    // Ensure the canvas is visible
    barChartRef.style.display = 'block';  // Make sure the canvas is visible

    if (barChartRef) {
        const ctx = barChartRef.getContext("2d");

        if (chartData.value.length === 0) {
            return;
        }

        chartInstance.value = new Chart(ctx, {
            type: "bar",
            data: {
                labels: chartData.value.map((offense) => offense.offense_name),
                datasets: [
                    {
                        data: chartData.value.map((offense) => offense.count),
                        backgroundColor: [
                            "rgba(255, 0, 0, 0.2)",
                            "rgba(255, 165, 0, 0.4)",
                            "rgba(54, 162, 235, 0.4)",
                            "rgba(75, 192, 192, 0.4)",
                            "rgba(153, 102, 255, 0.4)",
                        ],
                        borderColor: [
                            "rgba(255, 0, 0, 1)",
                            "rgba(255, 165, 0, 1)",
                            "rgba(54, 162, 235, 1)",
                            "rgba(75, 192, 192, 1)",
                            "rgba(153, 102, 255, 1)",
                        ],
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: true,
                        text: "5 Most Committed Offenses",
                        font: {
                            size: 18,
                            weight: "bold",
                        },
                        color: "black",
                        align: "center",
                    },
                },
                scales: {
                    x: {
                        display: false,
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
    } else {
        console.error("Canvas reference is null");
    }
};


  
  // Fetch data when the component is mounted
  onMounted(async () => {
      await nextTick(); // Ensure DOM is fully rendered
      fetchBarData();
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
  