<script setup>
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PieChart from '@/Components/PieChart.vue';
import axios from 'axios';

// Get current year
const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);  // Default to current year

// Reactive offense data
const offenseData = ref({
  male: 0,
  female: 0
});

// Reactive chart data
const pieData = ref({
  labels: ['Male', 'Female'],
  datasets: [
    {
      label: 'Offenders by Sex',
      data: [0, 0], // Initialize with zero data
      backgroundColor: [
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
      ],
      borderColor: [
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)',
      ],
      borderWidth: 1,
    },
  ],
});

// Function to fetch and update chart data based on year
const fetchChartData = (year) => {
  axios.get(`/get-chart-data/${year}`)
    .then((response) => {
      const data = response.data;

      // Update offenseData and pieData
      offenseData.value.male = data.male;
      offenseData.value.female = data.female;

      // Update pie chart data
      pieData.value.datasets[0].data = [data.male, data.female];
    })
    .catch((error) => {
      console.error('Error fetching chart data:', error);
    });
};

// Function to handle year change
const changeYear = (event) => {
  selectedYear.value = event.target.value;
  fetchChartData(selectedYear.value);
};

// Fetch data when component is mounted
onMounted(() => {
  fetchChartData(selectedYear.value);  // Load data for the current year by default
});
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <section class="w-full lg:w-1/2 mx-4 my-8">
      <!-- Year Selection Dropdown -->
      <div class="mb-4">
        <label for="yearSelect" class="mr-2">Select Year:</label>
        <select id="yearSelect" @change="changeYear" class="form-select" v-model="selectedYear">
          <option value="2024">2024</option>
          <option value="2023">2023</option>
          <option value="2022">2022</option>
          <option value="2021">2021</option>
        </select>
      </div>

      <!-- Card -->
      <div class="card bg-gray-100 text-gray-800 shadow-xl border border-gray-200 rounded-lg">
        <!-- Card header with solid blue color -->
        <div class="card-header bg-blue-600 text-white p-4 rounded-t-lg">
          <h3 class="card-title text-xl font-bold flex items-center">
            <i class="fas fa-map-marker-alt mr-2"></i> Pie Chart for Sex Offenders in {{ selectedYear }}
          </h3>
        </div>
        <!-- Card body with white background -->
        <div class="card-body p-6 bg-white rounded-b-lg">
          <!-- PieChart component -->
          <div class="flex justify-center items-center">
            <PieChart :chartData="pieData" />
          </div>
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
