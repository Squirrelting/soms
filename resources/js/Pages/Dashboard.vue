<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from "@/Components/PieChart.vue";
import StudentTable from "@/Components/StudentTable.vue";
import OffensesPerGrade from "@/Components/OffensesPerGrade.vue";
import LineChart from "@/Components/LineChart.vue";
import BarGraph from "@/Components/BarGraph.vue";
import axios from "axios";

const props = defineProps({
    students: Object,
    schoolYears: Array,
    offensesPerGrade: Array,
    selectedYear: String,
    selectedQuarter: String,
});

const studentsData = ref([]);
const isLoading = ref(false);

const selectedYear = ref(
    props.schoolYears.length > 0 ? props.schoolYears[0].student_schoolyear : ""
);
const selectedQuarter = ref(
    props.schoolYears.length > 0 && props.schoolYears[0].quarters.length > 0
        ? props.schoolYears[0].quarters[0]
        : ""
);

const filteredQuarters = computed(() => {
    const selectedSchoolYear = props.schoolYears.find(
        (year) => year.student_schoolyear === selectedYear.value
    );
    getStudentsData();
    return selectedSchoolYear ? selectedSchoolYear.quarters : [];
});

watch(
    () => props.schoolYears,
    (newValue) => {
        if (newValue.length > 0) {
            selectedYear.value = newValue[0].student_schoolyear;
            selectedQuarter.value = newValue[0].quarters[0] || "";
        } else {
            selectedYear.value = "";
            selectedQuarter.value = "";
        }
    }
);

const filterQuarters = () => {
    selectedQuarter.value = "";
};

const getStudentsData = () => {
    isLoading.value = true;

    // Prepare route parameters
    const params = {
        selectedSchoolyear: selectedYear.value,
    };

    // Only add selectedQuarter if it’s not empty
    if (selectedQuarter.value) {
        params.selectedQuarter = selectedQuarter.value;
    }

    axios
        .get(route("get.table.data", params))
        .then((response) => {
            studentsData.value = response.data.students;
            isLoading.value = false;
        })
        .catch((error) => {
            console.error("Error fetching data:", error);
            isLoading.value = false;
        });
};


onMounted(() => {
    getStudentsData();
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="m-4 flex flex-col lg:flex-row lg:space-x-4">
            <!-- Main Content Section -->
            <div class="flex-grow">
                <div class="flex flex-wrap space-x-2 mb-2">
                    <!-- School Year Selector -->
                    <select
                        v-model="selectedYear"
                        :disabled="isLoading"
                        @change="filterQuarters"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[8rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
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
                        :disabled="isLoading"
                        v-model="selectedQuarter"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[6rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
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
                    <div
                        class="flex-grow bg-white rounded-lg p-2"
                        style="height: 225px"
                    >
                        <BarGraph
                            :selectedYear="selectedYear"
                            :selectedQuarter="selectedQuarter"
                        />
                    </div>

                    <!-- Line Chart -->
                    <div
                        class="flex-grow bg-white rounded-lg p-2"
                        style="height: 225px"
                    >
                        <LineChart
                            :selectedYear="selectedYear"
                            :selectedQuarter="selectedQuarter"
                        />
                    </div>

                    <!-- Pie Chart -->
                    <div
                        class="bg-white rounded-lg p-2 w-full lg:w-64"
                        style="height: 225px"
                    >
                        <PieChart
                            :selectedYear="selectedYear"
                            :selectedQuarter="selectedQuarter"
                        />
                    </div>
                </div>
                <!-- Table Section -->
                <span v-if="isLoading" class="loading loading-spinner loading-lg"></span>
                <StudentTable :students="studentsData" />
            </div>

            <!-- Vertical Card Section -->
            <div class="flex">
                <OffensesPerGrade :offensesPerGrade="offensesPerGrade" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
