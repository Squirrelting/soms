<script setup>
import { ref, watch, computed } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import axios from "axios"; 
import { CalendarDaysIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    students: Object,
    search: String,
    grade: String,
    grades: Array,
    section: String,
    sections: {
        type: Array,
        default: () => [],
    },
});

// Default date values
const today = new Date();
const lastMonth = new Date();
lastMonth.setMonth(today.getMonth() - 1);

const maxDate = today.toISOString().split('T')[0];

// Reactive start and end dates
const startDate = ref(lastMonth.toISOString().split("T")[0]);
const endDate = ref(today.toISOString().split("T")[0]);

// Refs for date inputs
const startDateInput = ref(null);
const endDateInput = ref(null);

// Methods to show the date pickers
const showStartDatePicker = () => {
  startDateInput.value?.showPicker();
};
const showEndDatePicker = () => {
  endDateInput.value?.showPicker();
};

// Computed properties for formatted dates
const formattedStartDate = computed(() =>
  new Date(startDate.value).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  })
);

const formattedEndDate = computed(() =>
  new Date(endDate.value).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  })
);


const searchQuery = ref(props.search || "");
const gradeFilter = ref(props.grade || "");
const sectionFilter = ref(props.section || "");
const studentsData = ref(props.students);
const sections = ref(props.sections); 
const sortColumn = ref("id"); 
const sortOrder = ref("desc"); 

// Sorting method
const sortTable = (column) => {
  if (sortColumn.value === column) {
    sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc"; // Toggle sorting order
  } else {
    sortColumn.value = column;
    sortOrder.value = "asc"; // Set ascending as default order for new column
  }
  filter(); // Call the filter method to apply the sorting
};

// Function to filter based on search, grade, section, and date
const filter = () => {
    router.get(
        route("students.index"),
        {
            search: searchQuery.value,
            grade: gradeFilter.value,
            section: sectionFilter.value,
            startDate: startDate.value,
            endDate: endDate.value,
            sortColumn: sortColumn.value, // Pass the sorting column
            sortOrder: sortOrder.value,   // Pass the sorting order
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                studentsData.value = page.props.students;
            },
        }
    );
};

// Watch for changes in filters and trigger the filter method
watch(
    [searchQuery, gradeFilter, sectionFilter, startDate, endDate],
    () => {
        filter();
    }
);

// Fetch sections based on selected grade
const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(`/students/sections?grade_id=${gradeId}`);
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
            <span class="ml-2 text-base text-gray-800">{{ $page.props.flash.message }}</span>
        </div>

        <!-- Student List with Search and Filters -->
        <div class="mt-4 mx-4">

            <div class="flex justify-between items-center mb-2 space-x-2">
    <h5 class="text-lg font-semibold text-gray-700">
        Student List
    </h5>

    <!-- Start Date Picker -->
    <div class="flex items-center space-x-1">
        <label for="startDate" class="text-sm">Start:</label>
        <button @click="showStartDatePicker" class="calendar-button">
            <CalendarDaysIcon class="h-5 w-5 text-gray-500" />
        </button>
        <input
            type="date"
            id="startDate"
            v-model="startDate"
            ref="startDateInput"
            :max="maxDate"
            @change="filter"
            class="invisible-input"
        />
        <span class="text-sm text-gray-700">{{ formattedStartDate }}</span>
    </div>

    <!-- End Date Picker -->
    <div class="flex items-center space-x-1">
        <label for="endDate" class="text-sm">End:</label>
        <button @click="showEndDatePicker" class="calendar-button">
            <CalendarDaysIcon class="h-5 w-5 text-gray-500" />
        </button>
        <input
            type="date"
            id="endDate"
            v-model="endDate"
            ref="endDateInput"
            :max="maxDate"
            @change="filter"
            class="invisible-input"
        />
        <span class="text-sm text-gray-700">{{ formattedEndDate }}</span>
    </div>

    <!-- Search Input -->
    <input
        v-model="searchQuery"
        type="text"
        placeholder="Search by Name and LRN"
        class="border border-gray-300 rounded-lg p-1 w-44 text-sm focus:outline-none focus:ring focus:border-blue-300"
    />

    <!-- Grade Dropdown -->
    <select
        v-model="gradeFilter"
        class="border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
    >
        <option value="">All Grades</option>
        <option v-for="grade in grades" :key="grade.id" :value="grade.id">Grade {{ grade.grade }}</option>

    </select>

    <!-- Section Dropdown -->
    <select
        v-model="sectionFilter"
        class="border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
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

    <!-- Add Student Button -->
    <Link
        :href="route('students.create')"
        class="bg-blue-500 hover:bg-blue-600 text-white text-s font-medium py-1 px-2 rounded-lg transition ease-in-out duration-150"
    >
        Add Student
    </Link>
</div>


            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <!-- <th class="hidden" >
                            ID
                        </th> -->
                        <th class="py-1 px-2 text-left border cursor-pointer text-sm" @click="sortTable('id')">
                            No.
                            <span class="ml-1 text-[8px]">
                                <span :class="sortColumn === 'id' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                                <span :class="sortColumn === 'id' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                            </span>
                        </th>
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('lrn')">
                        LRN
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'lrn' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'lrn' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>

                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('lastname')">
                        Student's Name
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'lastname' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'lastname' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>

                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('sex')">
                            Sex
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'sex' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'sex' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>

                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('grade_id')">
                            Grade
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'grade_id' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'grade_id' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>

                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('section_id')">
                            Section
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'section_id' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'section_id' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>

                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('email')">
                        Parent's Email
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'email' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'email' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>

                        <th class="py-2 px-2 text-left border text-sm">
                            Offenses/Penalties
                        </th>
                        


                        <th class="py-2 px-2 text-left border text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(student, index) in studentsData.data" :key="student.id">
                        <td class="py-2 px-2 border text-sm">{{ student.id }}</td>

                        <!-- <td class="py-2 px-4 text-left border text-sm">{{ index + 1 }}</td> -->
                        <td class="py-2 px-2 border text-sm">{{ student.lrn }}</td>
                        <td class="py-2 px-2 border text-sm">
                            {{ student.lastname }}, {{ student.firstname }} {{ student.middlename }}
                        </td>
                        <td class="py-2 px-2 border text-sm">{{ student.sex }}</td>
                        <td class="py-2 px-2 border text-sm">
                            Grade {{ student.grade?.grade ?? "N/A" }}
                        </td>
                        <td class="py-2 px-2 border text-sm">
                            {{ student.section?.section ?? "N/A" }}
                        </td>

                        <td class="py-2 px-4 border text-sm">
                            <div class="px-2 py-1 text-sm bg-blue-200 text-dark p-3 rounded">
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
        </div>

        <Pagination :pagination="studentsData" />
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
</style>
