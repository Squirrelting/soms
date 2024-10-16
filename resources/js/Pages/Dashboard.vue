<script setup>
import { ref, watchEffect, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from '@/Components/PieChart.vue';
import LineChart from '@/Components/LineChart.vue'; 
import BarGraph from '@/Components/BarGraph.vue'; 
import { CalendarDaysIcon } from "@heroicons/vue/24/solid"; // Heroicons

// Default date values
const today = new Date();
const lastMonth = new Date();
lastMonth.setMonth(today.getMonth() - 1);

const maxDate = today.toISOString().split('T')[0];


// Reactive start and end dates
const startDate = ref(lastMonth.toISOString().split('T')[0]);
const endDate = ref(today.toISOString().split('T')[0]);

// Refs for date inputs
const startDateInput = ref(null);
const endDateInput = ref(null);

// Methods to show the date pickers
const showStartDatePicker = () => {
  startDateInput.value?.showPicker(); // Trigger the native date picker
};

const showEndDatePicker = () => {
  endDateInput.value?.showPicker(); // Trigger the native date picker
};

// Watch for changes in startDate and endDate
watchEffect([startDate, endDate]);

// Formatting dates to a readable format
const formattedStartDate = computed(() =>
  new Date(startDate.value).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  })
);

const formattedEndDate = computed(() =>
  new Date(endDate.value).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  })
);
</script>


<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <div class="m-4 flex space-x-4">
      <!-- Start Date Picker -->
      <div class="flex items-center">
        <label for="startDate" class="mr-2">Start Date:</label>
        <!-- Partially hidden date input field (not completely hidden) -->
        <input type="date" id="startDate" v-model="startDate" ref="startDateInput" :max="maxDate" 
        class="invisible-input" />
        <!-- Calendar icon to open the date picker -->
        <button @click="showStartDatePicker" class="calendar-button">
          <CalendarDaysIcon class="h-6 w-6 text-gray-500" />
        </button>
        <!-- Display the formatted selected date -->
        <span>{{ formattedStartDate }}</span>
      </div>

      <!-- End Date Picker -->
      <div class="flex items-center">
        <label for="endDate" class="mr-2">End Date:</label>
        <!-- Partially hidden date input field (not completely hidden) -->
        <input type="date" id="endDate" v-model="endDate" ref="endDateInput" :max="maxDate" 
        class="invisible-input" />
        <!-- Calendar icon to open the date picker -->
        <button @click="showEndDatePicker" class="calendar-button">
          <CalendarDaysIcon class="h-6 w-6 text-gray-500" />
        </button>
        <!-- Display the formatted selected date -->
        <span>{{ formattedEndDate }}</span>
      </div>
      <!-- Buttons section -->
      <div class="flex space-x-2">
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
.calendar-button {
  background: none;
  border: none;
  cursor: pointer;
  margin-right: 8px;
}

.invisible-input {
  opacity: 0;
  position: absolute;
  z-index: -1;
  pointer-events: none;
}

span {
  margin-left: 8px;
  font-size: 1rem;
  color: #333;
}
.chart-container {
  height: 300px; /* Set a consistent height for both graphs */
}
</style>
