<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PieChart from '@/Components/PieChart.vue';
import LineChart from '@/Components/LineChart.vue'; 
import BarGraph from '@/Components/BarGraph.vue'; 


// Default date values
const today = new Date();
const lastMonth = new Date();
lastMonth.setMonth(today.getMonth() - 1);

// Reactive start and end dates
const startDate = ref(lastMonth.toISOString().split('T')[0]);
const endDate = ref(today.toISOString().split('T')[0]);

// Function to fetch line data based on the current dates
function fetchLineData() {
    console.log(`Fetching data from ${startDate.value} to ${endDate.value}`);
    // API logic can be added here later if necessary
}

// Watch for changes in startDate and endDate
watch([startDate, endDate], () => {
    fetchLineData(); // Fetch data whenever the dates change
});
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <section class="w-full lg:w-1/2 mx-4 my-8">
      <div class="mb-4 flex">
        <div class="mr-4">
          <label for="startDate" class="mr-2">Start Date:</label>
          <input type="date" id="startDate" v-model="startDate" class="form-input" />
        </div>
        <div>
          <label for="endDate" class="mr-2">End Date:</label>
          <input type="date" id="endDate" v-model="endDate" class="form-input" />
        </div>
      </div>

      <div class="card bg-gray-100 text-gray-800 shadow-xl border border-gray-200 rounded-lg">
        <div class="card-header bg-orange-600 text-white p-4 rounded-t-lg">
          <h3 class="card-title text-xl font-bold flex items-center">
            <i class="fas fa-chart-line mr-2"></i> Bar Graph for Top 5 Committed Offenses
          </h3>
        </div>
        <div class="card-body p-6 bg-white rounded-b-lg">
          <BarGraph :start-date="startDate" :end-date="endDate" />
        </div>
      </div>

      <div class="card bg-gray-100 text-gray-800 shadow-xl border border-gray-200 rounded-lg">
        <div class="card-header bg-green-600 text-white p-4 rounded-t-lg">
          <h3 class="card-title text-xl font-bold flex items-center">
            <i class="fas fa-chart-line mr-2"></i> Line Chart for Trends
          </h3>
        </div>
        <div class="card-body p-6 bg-white rounded-b-lg">
          <LineChart :start-date="startDate" :end-date="endDate" />
        </div>
      </div>

      <div class="card bg-gray-100 text-gray-800 shadow-xl border border-gray-200 rounded-lg mb-4">
        <div class="card-header bg-blue-600 text-white p-4 rounded-t-lg">
          <h3 class="card-title text-xl font-bold flex items-center">
            <i class="fas fa-map-marker-alt mr-2"></i> Pie Chart for Sex Offenders
          </h3>
        </div>
        <div class="card-body p-6 bg-white rounded-b-lg">
          <PieChart :start-date="startDate" :end-date="endDate" />
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
