<script setup>
import { ref, computed, watch } from "vue";
import { Head, router, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    offendersData: Object,
    offenses: Object,
    grades: Array,
    grade: String,
    section: String,
    schoolYears: Array,
    selectedYear: String,
    selectedQuarter: String,
});

const offenses = ref(props.offenses);
const selectedOffense = ref("");  
const offendersData = ref(props.offendersData);


const selectedYear = ref(props.selectedYear || "");
const selectedQuarter = ref(props.selectedQuarter || "");


const filteredQuarters = computed(() => {
    const selectedSchoolYear = props.schoolYears.find(
        (year) => year.student_schoolyear === selectedYear.value
    );
    return selectedSchoolYear ? selectedSchoolYear.quarters : [];
});

// Define refs for filters
const searchQuery = ref("");
const sanction = ref("");
const offenseFilter = ref("");
const sex = ref("");
const gradeFilter = ref(props.grade || "");
const sectionFilter = ref(props.section || "");
const sections = ref([]);

const filter = () => {
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
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                offendersData.value = page.props.offendersData;
            },
        }
    );
};

// Watch for changes in filters and trigger the filter method
watch(
    [   searchQuery,
        sanction,
        offenseFilter,
        gradeFilter,
        sectionFilter,
        sex,
        selectedYear,
        selectedQuarter,
    ],
    () => {
        filter();
    }
);

// Fetch sections based on selected grade
const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(
            `/students/sections?grade_id=${gradeId}`
        );
        sections.value = response.data.sections; // Populate the sections dropdown
        sectionFilter.value = ""; // Reset section filter when grade changes
    } catch (error) {
        console.error("Error fetching sections:", error);
    }
};

// Watch for grade changes and fetch sections when the grade changes
watch(gradeFilter, (newGrade) => {
    if (newGrade) {
        fetchSections(newGrade);
    } else {
        sections.value = [];
    }
});

// Watcher to filter offenses based on offense type
watch(offenseFilter, () => {
    if (offenseFilter.value === "Minor") {
        offenses.value = props.offenses.minor_offenses; 
    } else if (offenseFilter.value === "Major") {
        offenses.value = props.offenses.major_offenses; 
    } else {
        offenses.value = props.offenses.all_offenses; 
    }
}, { immediate: true }); // Run the watcher immediately on load


</script>

<template>
    <Head title="Reports" />
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-2 space-x-2">
                <h5 class="text-lg font-semibold text-gray-700">List of Offenders</h5>

                <div class="flex space-x-4">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by Name and LRN"
                    class="border border-gray-300 rounded-lg p-1 w-44 text-sm focus:outline-none focus:ring focus:border-blue-300"
                />
                    <select
                        v-model="selectedYear"
                        @change="filterQuarters"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
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
                        v-model="selectedQuarter"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">Select Quarter</option>
                        <option
                            v-for="(quarter, index) in filteredQuarters"
                            :key="index"
                        >
                            {{ quarter }}
                        </option>
                        
                    </select>

                    <select
                        v-model="sanction"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">Status</option>
                        <option value="0">Unresolve</option>
                        <option value="1">Resolved</option>
                    </select>

                    <select
                        v-model="sex"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">All Genders</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>

                    <select
                        v-model="gradeFilter"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
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
                        :disabled="gradeFilter == ''"
                        v-model="sectionFilter"
                        class="border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
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
                        v-model="offenseFilter"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">All Offenses</option>
                        <option value="Minor">Minor Offense</option>
                        <option value="Major">Major Offense</option>
                    </select>
                    <select
                        v-model="selectedOffense"
                        class="pl-2 border border-gray-300 rounded p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">Select Offenses</option>
                        <option
                            v-for="(offense, index) in offenses"
                            :key="index"
                            :value="offense"
                        >
                            {{ offense }}
                        </option>
                    </select>

                </div>
            <table class="w-full bg-white border shadow">
                <thead>
                    <tr>
                        <th class="py-1 px-2 text-left border cursor-pointer text-sm">No.</th>                       
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">LRN</th>
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">Student's Name</th>
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">Sex</th>                        
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">Grade</th>                        
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">Section</th>                        
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">Offense</th>                           
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm">Penalty</th>                        
                        <th class="py-1 px-2 border text-sm">Date Committed</th>
                        <th class="py-1 px-2 border text-sm">Date Recorded</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(offense, index) in offendersData.data"
                        :key="offense.id"
                    >
                        <td class="py-1 px-2 border text-sm">{{ parseInt(index) + 1 }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.lrn }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.student_lastname }}, {{ offense.student_firstname }}, {{ offense.student_middlename }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.student_sex }}</td>
                        <td class="py-1 px-2 border text-sm">Grade {{ offense.student_grade }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.student_section }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.minor_offense || offense.major_offense }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.minor_penalty || offense.major_penalty }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.committed_date }}</td>
                        <td class="py-1 px-2 border text-sm">{{ offense.recorded_date }}</td>
                    </tr>
                </tbody>
            </table>

            <Pagination :pagination="offendersData" />
        </div>
    </AuthenticatedLayout>
</template>

