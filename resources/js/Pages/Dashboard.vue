<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PieChart from '@/Components/PieChart.vue';

// Default date values
const today = new Date();
const lastMonth = new Date();
lastMonth.setMonth(today.getMonth() - 1);

// Reactive start and end dates
const startDate = ref(lastMonth.toISOString().split('T')[0]);
const endDate = ref(today.toISOString().split('T')[0]);
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <section class="w-full lg:w-1/2 mx-4 my-8">
      <!-- Date Selection -->
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

      <!-- Card -->
      <div class="card bg-gray-100 text-gray-800 shadow-xl border border-gray-200 rounded-lg">
        <!-- Card header with solid blue color -->
        <div class="card-header bg-blue-600 text-white p-4 rounded-t-lg">
          <h3 class="card-title text-xl font-bold flex items-center">
            <i class="fas fa-map-marker-alt mr-2"></i> Pie Chart for Sex Offenders
          </h3>
        </div>
        <!-- Card body with white background -->
        <div class="card-body p-6 bg-white rounded-b-lg">
          <!-- Pass dates to PieChart component -->
          <PieChart :start-date="startDate" :end-date="endDate" />
        </div>
      </div>
    </section>
  </AuthenticatedLayout>
</template>
