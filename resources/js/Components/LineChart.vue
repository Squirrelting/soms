<template>
    <div>
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
        },
        {
            label: "Major Offenses",
            data: [],
            backgroundColor: "rgba(255, 99, 132, 0.2)",
            borderColor: "rgba(255, 99, 132, 1)",
            borderWidth: 2,
        },
    ],
});

let chartInstance = null;

function formatDate(dateString) {
    // Create a new Date object from the dateString
    const date = new Date(dateString);

    // Define options for formatting
    const options = { month: 'short', day: '2-digit', year: 'numeric' };

    // Format the date using toLocaleDateString
    return date.toLocaleDateString('en-US', options).replace(',', '.');
}

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

            // Create a mapping for counts
            const offenseCounts = {};

            // Populate minor offenses data
            data.minor.forEach((offense) => {
                const committedDate = formatDate(offense.date); // Ensure to use correct field
                const count = offense.count;

                if (committedDate) {
                    offenseCounts[committedDate] = offenseCounts[committedDate] || { minor: 0, major: 0 };
                    offenseCounts[committedDate].minor += count; // Accumulate minor offense counts
                }
            });

            // Populate major offenses data
            data.major.forEach((offense) => {
                const committedDate = formatDate(offense.date); // Ensure to use correct field
                const count = offense.count;

                if (committedDate) {
                    offenseCounts[committedDate] = offenseCounts[committedDate] || { minor: 0, major: 0 };
                    offenseCounts[committedDate].major += count; // Accumulate major offense counts
                }
            });

            // Create labels and data arrays, filtering out zero values
            for (const date in offenseCounts) {
                const minorCount = offenseCounts[date].minor;
                const majorCount = offenseCounts[date].major;

                // Only add to the chart if there's a non-zero count
                if (minorCount > 0 || majorCount > 0) {
                    lineData.value.labels.push(date);
                    lineData.value.datasets[0].data.push(minorCount);
                    lineData.value.datasets[1].data.push(majorCount);
                }
            }

            // Handle chart instance
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

const createChart = () => {
    const ctx = document.getElementById("lineChart").getContext("2d");

    if (chartInstance) {
        chartInstance.destroy(); // Destroy the old chart if it exists
    }

    chartInstance = new Chart(ctx, {
        type: "line",
        data: lineData.value,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Offenses Over Time",
                },
                datalabels: {
                    color: "#000",
                    anchor: "end",
                    align: "end",
                    font: {
                        weight: "bold",
                        size: 14,
                    },
                    formatter: (value) => `${value}`, // Show data value
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return Number.isInteger(value) ? value : null; // Show whole numbers only
                        },
                        stepSize: 1, // Set step size to 1 to force whole numbers
                    },
                },
            },
        },
    });
};

// Watch selectedYear and selectedQuarter changes
watch(
    () => [props.selectedYear, props.selectedQuarter],
    () => {
        fetchChartData();
    },
    { immediate: true }
);

onMounted(() => {
    createChart(); // Create the chart when mounted
});
</script>

<style scoped>
/* Add styles here if needed */
</style>