<script setup>
import { ref, watch, computed } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios";

const props = defineProps({
    students: Object,
    perPage: Number,
    grade: String,
    grades: Array,
    section: String,
    sections: {
        type: Array,
        default: () => [],
    },
    schoolYears: Array,
    selectedYear: String,
    selectedQuarter: String,
});

const perPage = ref(props.perPage || 10);

const selectedYear = ref(props.selectedYear || "");
const selectedQuarter = ref(props.selectedQuarter || "");

const filteredQuarters = computed(() => {
    const selectedSchoolYear = props.schoolYears.find(
        (year) => year.schoolyear === selectedYear.value
    );
    return selectedSchoolYear ? selectedSchoolYear.quarter : [];
});

const filterQuarters = () => {
    selectedQuarter.value = "";
};

const searchQuery = ref("");
const gradeFilter = ref(props.grade || "");
const sectionFilter = ref(props.section || "");
const studentsData = ref(props.students);
const sections = ref(props.sections);
const sortColumn = ref("updated_at");
const sortOrder = ref("desc");

// Sorting method
const sortTable = (column) => {
    if (sortColumn.value === column) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortColumn.value = column;
        sortOrder.value = "asc"; 
    }
    filter(); 
};

const isLoading = ref(false);

const filter = () => {
    isLoading.value = true;

    router.get(
        route("students.index"),
        {
            search: searchQuery.value,
            grade: gradeFilter.value,
            section: sectionFilter.value,
            sortColumn: sortColumn.value,
            sortOrder: sortOrder.value,
            selectedYear: selectedYear.value,
            selectedQuarter: selectedQuarter.value,
            perPage: perPage.value,
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

// Watch for changes in filters and trigger the filter method
watch(
    [searchQuery, gradeFilter, sectionFilter, selectedYear, selectedQuarter],
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

</script>

<template>
    <Head title="Student" />

    <AuthenticatedLayout>
        <!-- Message from the controller -->
        <div
            v-if="$page.props.flash.message"
            role="alert"
            class="alert alert-info mt-4 mx-5 px-4 py-2"
        >
            <span class="ml-2 text-base text-gray-800">{{
                $page.props.flash.message
            }}</span>
        </div>

        <!-- Student List with Search and Filters -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-2 space-x-2">
                <h5 class="text-lg font-semibold text-gray-700">
                    Student List
                </h5>
                
                <div class="flex space-x-1">
                    <select
                        :disabled="isLoading"
                        v-model="selectedYear"
                        @change="filterQuarters"
                        class="select text-gray-700 select-xs text-xs py-1 px-1 w-[8rem] h-8 focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                        <option value="">All year</option>
                        <option
                            v-for="(schoolyear, index) in props.schoolYears"
                            :key="index"
                            :value="schoolyear.schoolyear"
                        >
                            S.Y. {{ schoolyear.schoolyear }}
                        </option>
                    </select>
                    <!-- Quarters Select -->
                    <select
                        :disabled="isLoading"
                        v-model="selectedQuarter"
                        class="select text-gray-700 select-xs text-xs py-1 px-1 w-[6rem] h-8 focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
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
                <!-- Grade Dropdown -->
                <select
                    :disabled="isLoading"
                    v-model="gradeFilter"
                    class="select text-gray-700 select-xs text-xs py-1 px-1 w-[6rem] h-8 focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
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

                <!-- Section Dropdown -->
                <select
                    :disabled="isLoading"
                    v-model="sectionFilter"
                    class="select text-gray-700 select-xs text-xs py-1 px-1 w-[6rem] h-8 focus:outline-none focus:ring focus:border-blue-200 focus:ring-blue-200"
                    >
                    <option value="">All Sections</option>
                    <option
                        v-for="section in sections"
                        :key="section.id"
                        :value="section.id"
                    >
                        {{ section.section }}
                    </option>
                </select>
            </div>

                <!-- Add Student Button -->
                <Link
                    :href="route('students.create')"
                    class="bg-blue-500 hover:bg-blue-600 text-white text-s font-medium py-1 px-2 rounded-lg transition ease-in-out duration-150"
                >
                    Add Student
                </Link>
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

                        <th
                            class="py-2 px-2 text-left border cursor-pointer text-sm"
                            @click="sortTable('email')"
                        >
                            Adviser's Email
                            <span class="ml-1 text-[8px]">
                                <span
                                    :class="
                                        sortColumn === 'email' &&
                                        sortOrder === 'asc'
                                            ? 'text-black'
                                            : 'text-gray-400'
                                    "
                                    >▲</span
                                >
                                <span
                                    :class="
                                        sortColumn === 'email' &&
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

                        <td class="py-2 px-4 border text-sm">
                            <div
                            >
                                {{ student.email }}
                            </div>
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
    </AuthenticatedLayout>
</template>
