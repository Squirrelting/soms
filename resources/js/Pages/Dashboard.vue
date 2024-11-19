<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from "@/Components/PieChart.vue";
import OffensesPerGrade from "@/Components/OffensesPerGrade.vue";
import LineChart from "@/Components/LineChart.vue";
import BarGraph from "@/Components/BarGraph.vue";
import axios from "axios";

const props = defineProps({
    students: Object,
    perPage: Number,
    schoolYears: Array,
    selectedYear: String,
    selectedQuarter: String,
});

const studentsData = ref(props.students);
const searchQuery = ref("");
const perPage = ref(props.perPage || 10);

const gradesData = ref([]);
const isLoading = ref(false);

const selectedYear = ref(
  props.schoolYears.length > 0
    ? props.schoolYears[props.schoolYears.length - 1].student_schoolyear // Get the latest year
    : ""
);

const selectedQuarter = ref(
  props.schoolYears.length > 0 && props.schoolYears[props.schoolYears.length - 1].quarters.length > 0
    ? props.schoolYears[props.schoolYears.length - 1].quarters[props.schoolYears[props.schoolYears.length - 1].quarters.length - 1]  // Get the latest quarter of the latest year
    : "All Quarters"
);

const filteredQuarters = computed(() => {
  const selectedSchoolYear = props.schoolYears.find(
    (year) => year.student_schoolyear === selectedYear.value
  );
  return selectedSchoolYear ? selectedSchoolYear.quarters : [];
});

watch(selectedQuarter, (newQuarter) => {
  if (newQuarter === "All Quarters") {
    selectedQuarter.value = null;  // Set null for All Quarters selection
  }
  filter();  // Trigger filtering whenever the quarter changes
});

// Watcher for selectedYear to change the quarters
watch(selectedYear, (newYear) => {
  const selectedSchoolYear = props.schoolYears.find(
    (year) => year.student_schoolyear === newYear
  );
  selectedQuarter.value = selectedSchoolYear ? selectedSchoolYear.quarters[selectedSchoolYear.quarters.length - 1] : null;
  filter();  // Trigger filtering whenever the selected year changes
});

const filterQuarters = () => {
    selectedQuarter.value = "";
};

const sortColumn = ref('updated_at');
const sortOrder = ref('desc');

const sortTable = (column) => {
  sortColumn.value = column;
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  filter();
};

const filter = () => {
  isLoading.value = true;

  router.get(
    route("dashboard"),
    {
      search: searchQuery.value,
      sortColumn: sortColumn.value,
      sortOrder: sortOrder.value,
      perPage: perPage.value,
      selectedYear: selectedYear.value,
      selectedQuarter: selectedQuarter.value,
    },
    {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        studentsData.value = page.props.students;
        isLoading.value = false;
      },
    }
  );
};

watch([perPage, searchQuery, selectedYear, selectedQuarter], () => {
  filter();  // Trigger filter on perPage or search query change
});

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

                    <div class="bg-white py-2 px-2 rounded-lg shadow-lg space-y-4">
                <div class="flex justify-between items-center mb-2 space-x-2">

        <div class="flex items-center space-x-2">
        <select
            id="perPage"
            v-model="perPage"
            @change="filter"
            class="select select-xs text-xs py-1 px-1 w-[4rem] h-8 focus:outline-none border-gray-500 focus:ring focus:border-blue-200 focus:ring-blue-200"
        >
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="25">25</option>
        </select>
        
        <!-- Label next to the select dropdown -->
        <span class="text-xs">Entries per page</span>
    </div>

<!-- Search Input -->
<div class="relative w-full max-w-xs">
    <input
        v-model="searchQuery"
        type="text"
        placeholder="Search"
        class="input rounded-lg text-sm h-10 pl-9 pr-3 w-full border-blue-200 ring ring-blue-200 focus:border-blue-300 focus:ring focus:ring-blue-300 focus:outline-none"
    />
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 16 16"
        fill="currentColor"
        class="absolute left-2 top-1/2 transform -translate-y-1/2 h-5 w-5  opacity-70 pointer-events-none"
    >
        <path
            fill-rule="evenodd"
            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
            clip-rule="evenodd"
        />
    </svg>
</div>
</div>
            <table class="w-full bg-white border border-gray-200 shadow">

                <thead>
                    <tr>
                        <th class="hidden" @click="sortTable('updated_at')">
                            updated_at
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'updated_at' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'updated_at' &&
                                        sortOrder === 'desc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▼</span
                                >
                            </span>
                        </th>

                        <th
                            class="py-1 px-2 text-left border cursor-pointer text-sm"
                        >
                            No.
                        </th>

                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                            @click="sortTable('lrn')"
                        >
                            LRN
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'lrn' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'lrn' &&
                                        sortOrder === 'desc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▼</span
                                >
                            </span>
                        </th>

                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                            @click="sortTable('lastname')"
                        >
                            Student's Name
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'lastname' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'lastname' &&
                                        sortOrder === 'desc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▼</span
                                >
                            </span>
                        </th>

                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                            @click="sortTable('sex')"
                        >
                            Sex
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'sex' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'sex' &&
                                        sortOrder === 'desc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▼</span
                                >
                            </span>
                        </th>

                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                            @click="sortTable('grade_id')"
                        >
                            Grade
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'grade_id' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'grade_id' &&
                                        sortOrder === 'desc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▼</span
                                >
                            </span>
                        </th>

                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                            @click="sortTable('section_id')"
                        >
                            Section
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'section_id' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'section_id' &&
                                        sortOrder === 'desc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▼</span
                                >
                            </span>
                        </th>

                        <th class="py-2 px-2 text-left border text-sm">
                            Offenses/Penalties
                        </th>

                        <th class="py-2 px-2 text-left border text-sm">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="!studentsData.data || studentsData.data.length === 0">
                    <td
                        class="py-2 px-2 text-center border text-sm"
                        colspan="10"
                    >
                        No data available.
                    </td>
                </tr>
                    <tr
                        v-for="(student, index) in studentsData.data"
                        :key="student.id"
                    >
                        <td class="hidden">{{ student.updated_at }}</td>
                        <td class="py-2 px-4 text-left border text-sm">
                            {{ parseInt(index) + 1 }}
                        </td>
                        <td class="py-2 px-2 border text-sm">
                            {{ student.lrn }}
                        </td>
                        <td class="py-2 px-2 border text-sm">
                            {{ student.lastname }}, {{ student.firstname }}
                            {{ student.middlename }}
                        </td>
                        <td class="py-2 px-2 border text-sm">
                            {{ student.sex }}
                        </td>
                        <td class="py-2 px-2 border text-sm">
                            Grade {{ student.grade?.grade ?? "N/A" }}
                        </td>
                        <td class="py-2 px-2 border text-sm">
                            {{ student.section?.section ?? "N/A" }}
                        </td>

                        <td class="py-2 px-2 border text-sm">
                            <div
                                class="flex justify-center items-center gap-4 relative"
                            >
                                <!-- Minor offenses button -->
                                <div class="relative">
                                    <Link
                                        :href="
                                            route('minor.offenses', student.id)
                                        "
                                        class="px-2 py-0.5 text-s bg-yellow-300 text-dark rounded"
                                    >
                                        Minor
                                    </Link>
                                    <!-- Badge for minor offenses -->
                                    <span
                                        v-if="
                                            student.submitted_minor_offenses_count >
                                            0
                                        "
                                        class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-s rounded-full h-4 w-4 flex items-center justify-center"
                                    >
                                        {{
                                            student.submitted_minor_offenses_count
                                        }}
                                    </span>
                                </div>

                                <!-- Major offenses button -->
                                <div class="relative">
                                    <Link
                                        :href="
                                            route('major.offenses', student.id)
                                        "
                                        class="px-2 py-0.5 text-s bg-red-300 text-dark rounded"
                                    >
                                        Major
                                    </Link>
                                    <!-- Badge for major offenses -->
                                    <span
                                        v-if="
                                            student.submitted_major_offenses_count >
                                            0
                                        "
                                        class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-s rounded-full h-4 w-4 flex items-center justify-center"
                                    >
                                        {{
                                            student.submitted_major_offenses_count
                                        }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-2 px-4 border text-sm">
                            <Link
                                :href="route('students.edit', student.id)"
                                class="px-2 py-1 bg-green-500 text-white p-3 rounded"
                                >Edit</Link
                            >
                            <Link
                                :href="route('students.view', student.id)"
                                class="px-2 py-1 bg-blue-500 text-white p-3 rounded"
                                >View</Link
                            >
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :pagination="studentsData" />
        </div>
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
