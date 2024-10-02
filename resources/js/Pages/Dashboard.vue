<script setup>
import { ref, watchEffect } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
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

// Watch for changes in startDate and endDate
watchEffect([startDate, endDate]);
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <div class="m-4 flex space-x-4">
      <div>
        <label for="startDate" class="mr-2">Start Date:</label>
        <input type="date" id="startDate" v-model="startDate" class="form-input" />
      </div>
      <div>
        <label for="endDate" class="mr-2">End Date:</label>
        <input type="date" id="endDate" v-model="endDate" class="form-input" />
      </div>
      <!-- Buttons section -->
      <div class="flex space-x-2">
        <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
          Other button
        </button>
        <Link 
          class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm"
          :href="route('offenders.index')"
          >
          Offenders Per sex 
        </Link>
      </div>
    </div>

    <section class="w-full mx-4 my-8">
      <div class="lg:flex lg:space-x-4 mb-4">
        <div class="flex flex-col lg:flex-column lg:space-x-4 mb-4 w-1/2 mx-auto">
          <!-- Bar Graph -->
          <div class="card bg-gray-100 text-gray-800 shadow-md border rounded-lg mb-4 lg:mb-0 lg:flex-1">
            <div class="bg-orange-600 text-white p-4 rounded-t-lg">
              <h3 class="text-xl font-bold flex items-center">
                <i class="fas fa-chart-bar mr-2"></i> Bar Graph for Top 5 Committed Offenses
              </h3>
            </div>
            <div class="pl-4 bg-white rounded-b-lg chart-container">
              <BarGraph :start-date="startDate" :end-date="endDate" />
            </div>
          </div>

          <!-- Line Chart -->
          <div class="card bg-gray-100 text-gray-800 shadow-md border rounded-lg mt-8 lg:mb-0 lg:flex-1">
            <div class="bg-green-600 text-white p-4 rounded-t-lg">
              <h3 class="text-xl font-bold flex items-center">
                <i class="fas fa-chart-line mr-2"></i> Line Chart for Trends
              </h3>
            </div>
            <div class="p-6 bg-white rounded-b-lg chart-container">
              <LineChart :start-date="startDate" :end-date="endDate" />
            </div>
          </div>
        </div>

        <!-- Pie Chart -->
        <div class="card bg-gray-100 text-gray-800 shadow-md border rounded-lg lg:flex-1">
          <div class="bg-blue-600 text-white p-4 rounded-t-lg">
            <h3 class="text-xl font-bold flex items-center">
              <i class="fas fa-chart-pie mr-2"></i> Pie Chart for Sex Offenders
            </h3>
          </div>
          <div class="p-6 bg-white rounded-b-lg">
            <PieChart :start-date="startDate" :end-date="endDate" />
          </div>
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>

<style scoped>
.chart-container {
  height: 300px; /* Set a consistent height for both graphs */
}
</style>
