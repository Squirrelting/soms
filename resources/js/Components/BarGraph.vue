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
      selectedYear: String,
      selectedQuarter: String,
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
          console.error("Error fetching bar chart data:", error);
          isDataEmpty.value = true; // Show "No data available" in case of error
      } finally {
          isLoading.value = false;
      }
  };
  
  // Watch for changes in selectedYear and selectedQuarter props
  watch([() => props.selectedYear, () => props.selectedQuarter], async () => {
      await nextTick(); // Wait until the next DOM update cycle
      fetchBarData(); // Fetch new data whenever the dates change
  });
  
  // Function to render the bar chart
  const renderChart = () => {
      if (chartInstance.value) {
          chartInstance.value.destroy(); // Destroy previous chart instance if it exists
      }
  
      // Ensure the canvas reference is properly assigned
      const barChartRef = document.getElementById("barChart"); // Use the ID to get the canvas element
  
      if (barChartRef) {
          const ctx = barChartRef.getContext("2d"); // Access the canvas context
  
          const labels = chartData.value.map((offense) => offense.offense_name);
          const data = chartData.value.map((offense) => offense.count);
  
          chartInstance.value = new Chart(ctx, {
              type: "bar", // Specify the chart type
              data: {
                  labels: labels,
                  datasets: [
                      {
                          data: data,
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
                  maintainAspectRatio: false, // Allow CSS to manage aspect ratio
                  plugins: {
                      legend: {
                          display: false, // Remove the legend
                      },
                      title: {
                          display: true,
                          text: '5 Most Committed Offenses',
                          font: {
                              size: 18, // Title font size
                              family: 'Arial', // Font family
                              weight: 'bold', // Font weight
                          },
                          color: 'black', // Title color
                          align: 'center', // Center the title text
                      },
                      tooltip: {
                          callbacks: {
                              title: function (tooltipItems) {
                                  return tooltipItems[0].label; // Show offense name on hover
                              },
                          },
                      },
                  },
                  scales: {
                      x: {
                          display: false, // Hide x-axis labels (offense names)
                      },
                      y: {
                          beginAtZero: true,
                          ticks: {
                              callback: function (value) {
                                  return Number.isInteger(value) ? value : null; // Show whole numbers only
                              },
                              stepSize: 1, // Set step size to 1
                              font: {
                                  size: 10, // Set Y-axis tick font size
                              },
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
  