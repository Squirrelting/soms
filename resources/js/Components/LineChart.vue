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
    startDate: {
        type: String,
        required: true,
    },
    endDate: {
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

const fetchChartData = () => {
    axios
        .get("/get-line-data", {
            params: {
                start_date: props.startDate,
                end_date: props.endDate,
            },
        })
        .then((response) => {
            const data = response.data;

            // Clear existing data
            lineData.value.labels = [];
            lineData.value.datasets[0].data = [];
            lineData.value.datasets[1].data = [];

            // Populate minor offenses data
            data.minor.forEach((offense) => {
                if (!lineData.value.labels.includes(offense.date)) {
                    lineData.value.labels.push(offense.date); // Add formatted date to labels
                }
                lineData.value.datasets[0].data.push(offense.count); // Add minor offense count
            });

            // Populate major offenses data
            data.major.forEach((offense) => {
                if (!lineData.value.labels.includes(offense.date)) {
                    lineData.value.labels.push(offense.date); // Add formatted date to labels if not already added
                }
                lineData.value.datasets[1].data.push(offense.count); // Add major offense count
            });

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
                },
            },
        },
    });
};

// Watch startDate and endDate changes
watch(
    () => [props.startDate, props.endDate],
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
