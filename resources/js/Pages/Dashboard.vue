<script setup>
import { ref, computed, watch } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from "@/Components/PieChart.vue";
import StudentTable from "@/Components/StudentTable.vue";
import OffensesPerGrade from "@/Components/OffensesPerGrade.vue";
import LineChart from "@/Components/LineChart.vue";
import BarGraph from "@/Components/BarGraph.vue";

const props = defineProps({
    students: Object,
    schoolYears: Array,
    offensesPerGrade: Array,
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
                        S.Y. {{ schoolyear.student_schoolyear }}
                        </option>
                    </select>

                    <!-- Quarters Select -->
                    <select
                        v-model="selectedQuarter"
                        class="border border-gray-300 rounded-lg p-1 text-xs focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">All Quarter</option>
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
            <div class="flex">
                <OffensesPerGrade :offensesPerGrade="offensesPerGrade" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>