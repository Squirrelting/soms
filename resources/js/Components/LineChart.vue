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
    schoolYear: {
        type: String,
        required: true,
    },
    quarter: {
        type: String,
        required: true,
    },
});

// Reactive data structure for chart data
const lineData = ref({
    labels: [],
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
                school_year: props.schoolYear,
                quarter: props.quarter,
            },
        })
        .then((response) => {
            const minorData = response.data.minor;
            const majorData = response.data.major;

            // Prepare data for the chart
            lineData.value.labels = minorData.map((item) => item.date);
            lineData.value.datasets[0].data = minorData.map((item) => item.count);
            lineData.value.datasets[1].data = majorData.map((item) => item.count);

            // Destroy previous chart instance if it exists
            if (chartInstance) {
                chartInstance.destroy();
            }

            // Create new chart instance
            const ctx = document.getElementById("lineChart").getContext("2d");
            chartInstance = new Chart(ctx, {
                type: "line",
                data: lineData.value,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                        },
                        datalabels: {
                            anchor: "end",
                            align: "end",
                            formatter: (value) => value,
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: "Date",
                            },
                        },
                        y: {
                            title: {
                                display: true,
                                text: "Number of Offenses",
                            },
                        },
                    },
                },
            });
        })
        .catch((error) => {
            console.error("Error fetching line data:", error);
        });
};

// Watch for changes in props and fetch data accordingly
watch([() => props.schoolYear, () => props.quarter], () => {
    fetchChartData();
});

// Fetch data when the component is mounted
onMounted(() => {
    fetchChartData();
});
</script>
