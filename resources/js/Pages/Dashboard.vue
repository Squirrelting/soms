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
    schoolYears: Array,
    selectedYear: String,
    selectedQuarter: String,
});

const studentsData = ref([]);
const gradesData = ref([]);
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
    getGradesData();
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

    const params = {
        selectedSchoolyear: selectedYear.value,
    };

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

const getGradesData = () => {
    isLoading.value = true;
    const params = { selectedSchoolyear: selectedYear.value };
    if (selectedQuarter.value) {
        params.selectedQuarter = selectedQuarter.value;
    }
    axios
        .get(route("get.grade.data", params))
        .then((response) => {
            gradesData.value = response.data.offensesPerGrade;
            isLoading.value = false;
        })
        .catch((error) => {
            console.error("Error fetching data:", error);
            isLoading.value = false;
        });
};

onMounted(() => {
    getStudentsData();
    getGradesData();
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="m-4 grid gap-4 grid-cols-1 lg:grid-cols-8">
            <!-- Main Content Section -->
            <div class="lg:col-span-7">
                <div class="flex flex-wrap gap-2 mb-4">
                    <!-- School Year Selector -->
                    <select
                        v-model="selectedYear"
                        :disabled="isLoading"
                        @change="filterQuarters"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-full sm:w-[8rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
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
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-full sm:w-[8rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
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

                <div class="grid gap-4 grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
  <!-- Bar Graph (larger size) -->
  <div class="bg-white rounded-lg p-2 xl:col-span-1 md:col-span-1 col-span-1">
    <BarGraph
      :selectedYear="selectedYear"
      :selectedQuarter="selectedQuarter"
    />
  </div>

  <!-- Line Chart (larger size) -->
  <div class="bg-white rounded-lg p-2 xl:col-span-1 md:col-span-1 col-span-1">
    <LineChart
      :selectedYear="selectedYear"
      :selectedQuarter="selectedQuarter"
    />
  </div>

  <!-- Pie Chart (smaller size) -->
  <div class="bg-white rounded-lg p-2 xl:col-span-1 md:col-span-1 col-span-1">
    <PieChart
      :selectedYear="selectedYear"
      :selectedQuarter="selectedQuarter"
    />
  </div>
</div>


                
                <!-- Table Section -->
                <div class="my-4">
                    <span v-if="isLoading" class="loading loading-spinner loading-lg"></span>
                    <StudentTable :students="studentsData" />
                </div>
            </div>

            <!-- Vertical Card Section with Responsive Width -->
            <div class="lg:col-span-1">
                <span v-if="isLoading" class="loading loading-spinner loading-lg"></span>
                <OffensesPerGrade :offensesPerGrade="gradesData" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
