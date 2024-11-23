<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { Head, router, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

const props = defineProps({
    offendersData: Object,
    perPage: Number,
    offenses: Object,
    grades: Array,
    sections: {
        type: Array,
        default: () => [],
    },
    grade: String,
    section: String,
    schoolYears: Array,
    selectedYear: String,
    selectedQuarter: String,
    sanction: String,
    sex: String,
    offenseFilter: String,
    selectedOffense: String,
});

const perPage = ref(props.perPage || 10);

const offenses = ref(props.offenses);
const offendersData = ref(props.offendersData);

const selectedYear = ref(props.selectedYear || "");
const selectedQuarter = ref(props.selectedQuarter || "");

const filteredQuarters = computed(() => {
    const selectedSchoolYear = props.schoolYears.find(
        (year) => year.student_schoolyear === selectedYear.value
    );
    return selectedSchoolYear ? selectedSchoolYear.quarters : [];
});

const filterQuarters = () => {
    selectedQuarter.value = "";
};

const searchQuery = ref("");
const sanction = ref(props.sanction || "");
const offenseFilter = ref(props.offenseFilter || "");
const sex = ref(props.sex || "");
const gradeFilter = ref(props.grade || "");
const sectionFilter = ref(props.section || ""); 
const sections = ref(props.sections);
const selectedOffense = ref(props.selectedOffense || "");

const isLoading = ref(false);

const filter = () => {
    isLoading.value = true;
    router.get(
        route("reports.index"),
        {
            search: searchQuery.value,
            sanction: sanction.value,
            sex: sex.value,
            offenseFilter: offenseFilter.value,
            grade: gradeFilter.value,
            section: sectionFilter.value, 
            selectedYear: selectedYear.value,
            selectedQuarter: selectedQuarter.value,
            selectedOffense: selectedOffense.value,
            page: offendersData.value.current_page,
            perPage: perPage.value,

        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: (page) => {
                offendersData.value = page.props.offendersData;
                isLoading.value = false;

            },
        }
    );
};

// Watch for changes in filters and trigger the filter method
watch(
    [
        searchQuery,
        sanction,
        offenseFilter,
        gradeFilter,
        sectionFilter, 
        sex,
        selectedYear,
        selectedQuarter,
        selectedOffense,
    ],
    () => {
        filter();
    }
);

const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(`/students/sections?grade_id=${gradeId}`);
        sections.value = response.data.sections;
        sectionFilter.value = ""; 
    } catch (error) {
        console.error("Error fetching sections:", error);
    }
};

watch(gradeFilter, (newGrade) => {
    if (newGrade) {
        fetchSections(newGrade);
    } else {
        sections.value = [];
    }
});

watch(
    offenseFilter,
    () => {
        if (offenseFilter.value === "Minor") {
            offenses.value = props.offenses.minor_offenses;
        } else if (offenseFilter.value === "Major") {
            offenses.value = props.offenses.major_offenses;
        } else {
            offenses.value = props.offenses.all_offenses;
        }
    },
    { immediate: true }
);

onMounted(() => {
    selectedYear.value =
        props.selectedYear ||
        new URLSearchParams(window.location.search).get("selectedYear") ||
        "";
    selectedQuarter.value =
        props.selectedQuarter ||
        new URLSearchParams(window.location.search).get("selectedQuarter") ||
        "";
    sanction.value =
        props.sanction || new URLSearchParams(window.location.search).get("sanction") || "";
    sex.value = props.sex || new URLSearchParams(window.location.search).get("sex") || "";
    sectionFilter.value =
        props.section || new URLSearchParams(window.location.search).get("section") || ""; 
    offenseFilter.value =
        props.offenseFilter || new URLSearchParams(window.location.search).get("offenseFilter") || "";
    selectedOffense.value =
        props.selectedOffense || new URLSearchParams(window.location.search).get("selectedOffense") || "";
});

// Computed property for print URL with filters
const printUrl = computed(() => {
  return route("printoffenders", {
    search: searchQuery.value,
    sanction: sanction.value,
    sex: sex.value,
    offenseFilter: offenseFilter.value,
    grade: gradeFilter.value,
    section: sectionFilter.value,
    selectedYear: selectedYear.value,
    selectedQuarter: selectedQuarter.value,
    selectedOffense: selectedOffense.value,
  });
});

// Computed property for export Excel URL with filters
const exportExcel = computed(() => {
  return route("exportexcel", {
    search: searchQuery.value,
    sanction: sanction.value,
    sex: sex.value,
    offenseFilter: offenseFilter.value,
    grade: gradeFilter.value,
    section: sectionFilter.value,
    selectedYear: selectedYear.value,
    selectedQuarter: selectedQuarter.value,
    selectedOffense: selectedOffense.value,
  });
});


// Check if there is data, if not, show SweetAlert and prevent navigation
const checkDataAndProceed = (action) => {
  if (!offendersData.value.data || offendersData.value.data.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No offenses data",
      text: "There are no offenses data to export or print.",
    });
  } else {
    // Perform the action (either export or print)
    if (action === "print") {
      window.open(printUrl.value, "_blank");
    } else if (action === "export") {
      window.location.href = exportExcel.value;
    }
  }
};
</script>

<template>
    <Head title="Reports" />
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-2 space-x-2">
                <h5 class="text-lg font-semibold text-gray-700">
                    List of Offenders
                </h5>

                <div class="flex space-x-1">
                    <select
                        :disabled="isLoading"
                        v-model="selectedYear"
                        @change="filterQuarters"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[8rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                        <option value="">All year</option>
                        <option
                            v-for="(schoolyear, index) in props.schoolYears"
                            :key="index"
                            :value="schoolyear.student_schoolyear"
                        >
                            S.Y. {{ schoolyear.student_schoolyear }}
                        </option>
                    </select>

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

                    <div class="flex space-x-1">
                    <select
                        :disabled="isLoading"
                        v-model="gradeFilter"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[6rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                        <option value="">All Grades</option>
                        <option
                            v-for="grade in grades"
                            :key="grade.id"
                            :value="grade.id"
                        >
                            Grade {{ grade.grade }}
                        </option>
                    </select>

                    <select
                        :disabled="isLoading"
                        v-model="sectionFilter"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[8rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                        <option value="">All Sections</option>
                        <option
                            v-for="section in sections"
                            :key="section.section"
                            :value="section.section"
                        >
                            {{ section.section }}
                        </option>
                    </select>
                    </div>

                    <div class="flex space-x-1">
                    <select
                        :disabled="isLoading"
                        v-model="sex"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[6rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                        <option value="">All Sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>

                <select
                        :disabled="isLoading"
                        v-model="sanction"
                        class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[6rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                        <option value="">Status</option>
                        <option value="0">Unresolve</option>
                        <option value="1">Resolved</option>
                    </select>
                    </div>

                    <div class="flex space-x-1">
                    <button
                        @click="checkDataAndProceed('print')"
                        class="bg-blue-500 text-white py-1 px-3 rounded text-sm"
                    >
                        Print
                    </button>
                    <button
                        @click="checkDataAndProceed('export')"
                        class="bg-green-500 text-white py-1 px-3 rounded text-sm"
                    >
                        Export to Excel
                    </button>
                    </div>
                </div>

            <div class="flex space-x-1 mb-2">
                <select
                   :disabled="isLoading"
                    v-model="offenseFilter"
                    class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[8rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                    <option value="">All Offenses</option>
                    <option value="Minor">Minor Offense</option>
                    <option value="Major">Major Offense</option>
                </select>
                <select
                    :disabled="isLoading"
                    v-model="selectedOffense"
                    class="select text-gray-700 h-8 select-xs text-xs py-1 px-1 w-[46rem] focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                >
                    <option value="">Select All Offenses</option>
                    <option
                        v-for="(offense, index) in offenses"
                        :key="index"
                        :value="offense"
                    >
                        {{ offense }}
                    </option>
                </select>
            </div>

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
            <table class="w-full bg-white border shadow">
                <thead>
                    <tr>
                        <th
                            class="py-1 px-2 text-left border cursor-pointer text-sm"
                        >
                            No.
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            LRN
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Student's Name
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Sex
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Grade
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Section
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Offense
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Penalty
                        </th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Status
                        </th>
                        <th class="py-1 px-2 border text-sm">Date Committed</th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Recorded By
                        </th>
                        <th class="py-1 px-2 border text-sm">Date Recorded</th>
                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                        >
                            Resolved By
                        </th>
                        <th class="py-1 px-2 border text-sm">Date Resolved</th>

                    </tr>
                </thead>
                <tbody>
                        <!-- Check if studentsData is empty -->
    <tr v-if="!offendersData.data || offendersData.data.length === 0">
        <td
            class="py-2 px-2 text-center border text-sm"
            colspan="10"
        >
            No data available.
        </td>
    </tr>
                    <tr
                        v-for="(offense, index) in offendersData.data"
                        :key="offense.id"
                    >
                        <td class="py-1 px-2 border text-sm">
                            {{ parseInt(index) + 1 }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.lrn }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.student_lastname }},
                            {{ offense.student_firstname }},
                            {{ offense.student_middlename }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.student_sex }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            Grade {{ offense.student_grade }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.student_section }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.minor_offense || offense.major_offense }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.minor_penalty || offense.major_penalty }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
    {{ offense.sanction == 1 ? 'Resolved' : 'Unresolved' }}
</td>

                        <td class="py-1 px-2 border text-sm">
                            {{ offense.committed_date }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.recorded_by }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.recorded_date }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.cleansed_by }}
                        </td>
                        <td class="py-1 px-2 border text-sm">
                            {{ offense.cleansed_date }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <Pagination :pagination="offendersData" />
        </div>
        </div>
    </AuthenticatedLayout>
</template>
