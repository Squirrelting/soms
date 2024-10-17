<script setup>
import { ref, watchEffect, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from "@/Components/PieChart.vue";
import LineChart from "@/Components/LineChart.vue";
import BarGraph from "@/Components/BarGraph.vue";

const props = defineProps({
    schoolYears: Array,
});

const selectedYear = ref('')
const selectedQuarter = ref('')

const filteredQuarters = computed(() => {
  const selectedSchoolYear = props.schoolYears.find(
    (year) => year.student_schoolyear === selectedYear.value
  )
  return selectedSchoolYear ? selectedSchoolYear.quarters : []
})

const filterQuarters = () => {
  selectedQuarter.value = '' 
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="m-4 flex space-x-4">
            <select
                v-model="selectedYear"
                @change="filterQuarters"
                class="border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
            >
                <option value="">Select School Year</option>
                <option
                    v-for="(schoolyear, index) in props.schoolYears"
                    :key="index"
                    :value="schoolyear.student_schoolyear"
                >
                    {{ schoolyear.student_schoolyear }}
                </option>
            </select>

            <!-- Quarters Select -->
            <select
                v-model="selectedQuarter"
                class="border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
            >
                <option value="">Select Quarter</option>
                <option
                    v-for="(quarter, index) in filteredQuarters"
                    :key="index"
                >
                    {{ quarter }}
                </option>
            </select>
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
                <div
                    class="flex flex-col lg:flex-column lg:space-x-4 mb-4 w-1/2 mx-auto"
                >
                    <!-- Bar Graph -->
                    <div
                        class="card bg-gray-100 text-gray-800 shadow-md border rounded-lg mb-4 lg:mb-0 lg:flex-1"
                    >
                        <div class="bg-orange-600 text-white p-4 rounded-t-lg">
                            <h3 class="text-xl font-bold flex items-center">
                                <i class="fas fa-chart-bar mr-2"></i> Bar Graph
                                for Top 5 Committed Offenses
                            </h3>
                        </div>
                        <div class="pl-4 bg-white rounded-b-lg chart-container">
                            <BarGraph
                                :start-date="startDate"
                                :end-date="endDate"
                            />
                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div
                        class="card bg-gray-100 text-gray-800 shadow-md border rounded-lg mt-8 lg:mb-0 lg:flex-1"
                    >
                        <div class="bg-green-600 text-white p-4 rounded-t-lg">
                            <h3 class="text-xl font-bold flex items-center">
                                <i class="fas fa-chart-line mr-2"></i> Line
                                Chart for Trends
                            </h3>
                        </div>
                        <div class="p-6 bg-white rounded-b-lg chart-container">
                            <LineChart
                                :start-date="startDate"
                                :end-date="endDate"
                            />
                        </div>
                    </div>
                </div>

                <!-- Pie Chart -->
                <div
                    class="card bg-gray-100 text-gray-800 shadow-md border rounded-lg lg:flex-1"
                >
                    <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-chart-pie mr-2"></i> Pie Chart for
                            Sex Offenders
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
