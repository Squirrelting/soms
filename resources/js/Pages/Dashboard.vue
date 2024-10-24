<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from "@/Components/PieChart.vue";
import StudentTable from "@/Components/StudentTable.vue";
import LineChart from "@/Components/LineChart.vue";
import BarGraph from "@/Components/BarGraph.vue";

const props = defineProps({
    students: Object,
    schoolYears: Array,
});
const studentsData = ref(props.students);

const selectedYear = ref(props.schoolYears.length > 0 ? props.schoolYears[0].student_schoolyear : "");
const selectedQuarter = ref(props.schoolYears.length > 0 && props.schoolYears[0].quarters.length > 0 ? props.schoolYears[0].quarters[0] : "");

const filteredQuarters = computed(() => {
    const selectedSchoolYear = props.schoolYears.find(
        (year) => year.student_schoolyear === selectedYear.value
    );
    return selectedSchoolYear ? selectedSchoolYear.quarters : [];
});

watch(() => props.schoolYears, (newValue) => {
    if (newValue.length > 0) {
        selectedYear.value = newValue[0].student_schoolyear;
        selectedQuarter.value = newValue[0].quarters[0] || "";
    } else {
        selectedYear.value = "";
        selectedQuarter.value = "";
    }
});

const filterQuarters = () => {
    selectedQuarter.value = "";
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="m-4 flex flex-col lg:flex-row lg:space-x-4">
            <!-- Main Content Section -->
            <div class="flex-grow">
                <div class="flex flex-wrap space-x-2 mb-4">
                    <!-- School Year Selector -->
                    <select
                        v-model="selectedYear"
                        @change="filterQuarters"
                        class="border border-gray-300 rounded-lg p-1 text-xs focus:outline-none focus:ring focus:border-blue-300"
                    >
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
                        class="border border-gray-300 rounded-lg p-1 text-xs focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">Select Quarter</option>
                        <option
                            v-for="(quarter, index) in filteredQuarters"
                            :key="index"
                        >
                            {{ quarter }}
                        </option>
                    </select>
                </div>

                <div class="flex flex-col lg:flex-row gap-4">
                    <!-- Bar Graph -->
                    <div class="flex-grow bg-white rounded-lg p-2" style="height: 225px">
                        <BarGraph :selectedYear="selectedYear" :selectedQuarter="selectedQuarter" />
                    </div>

                    <!-- Line Chart -->
                    <div class="flex-grow bg-white rounded-lg p-2" style="height: 225px">
                        <LineChart :selectedYear="selectedYear" :selectedQuarter="selectedQuarter" />
                    </div>

                    <!-- Pie Chart -->
                    <div class="bg-white rounded-lg p-2 w-full lg:w-64" style="height: 225px">
                        <PieChart :selectedYear="selectedYear" :selectedQuarter="selectedQuarter" />
                    </div>
                </div>

                <!-- Table Section -->
                <StudentTable :students="studentsData.data" />
            </div>

            <!-- Vertical Card Section -->
            <div class="w-full lg:w-1/6 bg-gray-100 rounded-lg p-2 mt-2 lg:mt-0 lg:ml-2">
                <h2 class="text-sm font-semibold mb-2">Offenses per Grade</h2>
                <ul>
                    <li class="mb-1 p-1 bg-white rounded-lg shadow text-sm">Grade 7</li>
                    <li class="mb-1 p-1 bg-white rounded-lg shadow text-sm">Grade 8</li>
                    <li class="mb-1 p-1 bg-white rounded-lg shadow text-sm">Grade 9</li>
                    <li class="mb-1 p-1 bg-white rounded-lg shadow text-sm">Grade 10</li>
                    <li class="mb-1 p-1 bg-white rounded-lg shadow text-sm">Grade 11</li>
                    <li class="mb-1 p-1 bg-white rounded-lg shadow text-sm">Grade 12</li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>