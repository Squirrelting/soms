<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import axios from "axios"; // Import axios for API calls

const props = defineProps({
    students: Object,
    search: String,
    grade: String,
    section: String,
    sections: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref(props.search || "");
const gradeFilter = ref(props.grade || "");
const sectionFilter = ref(props.section || "");
const studentsData = ref(props.students);
const sections = ref(props.sections); // This will be populated based on the selected grade

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
        fetchSections(newGrade); // Fetch sections based on the selected grade
    } else {
        sections.value = []; // Clear sections if no grade is selected
    }
});

// Watch for changes in the search input, grade, and section filters
watch(
    [searchQuery, gradeFilter, sectionFilter],
    ([newSearch, newGrade, newSection]) => {
        router.get(
            route("students.index"),
            { search: newSearch, grade: newGrade, section: newSection },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    studentsData.value = page.props.students;
                },
            }
        );
    }
);

const DeleteStudent = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this student! This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Deleting...",
                text: "Please wait while we delete the student data.",
                didOpen: () => Swal.showLoading(),
            });

            router.delete(route("students.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    studentsData.value.data = studentsData.value.data.filter(
                        (student) => student.id !== id
                    );

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The student has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () =>
                    Swal.fire({
                        icon: "error",
                        title: "Failed!",
                        text: "There was a problem deleting the student. Please try again.",
                    }),
            });
        }
    });
};
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
            <span>{{ $page.props.flash.message }}</span>
        </div>

        <!-- Student List with Search and Filters -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold text-gray-700">
                    Student List
                </h5>
                <div class="flex items-center">
                    <!-- Search Input -->
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by first name, last name, LRN"
                        class="border border-gray-300 rounded-lg p-2 w-72 focus:outline-none focus:ring focus:border-blue-300"
                    />

                    <!-- Grade Dropdown -->
                    <select
                        v-model="gradeFilter"
                        class="ml-4 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:border-blue-300"
                    >
                        <option value="">All Grades</option>
                        <option
                            v-for="grade in [
                                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
                            ]"
                            :key="grade"
                            :value="grade"
                        >
                            Grade {{ grade }}
                        </option>
                    </select>

                    <!-- Section Dropdown (filtered by selected grade) -->
                    <select
                        v-model="sectionFilter"
                        class="ml-4 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:border-blue-300"
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

                    <Link
                        :href="route('import.students')"
                        class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded"
                        >import
                    </Link>

                    <!-- Add Student Button -->
                    <Link
                        :href="route('students.create')"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg ml-4 transition ease-in-out duration-150"
                    >
                        Add Student
                    </Link>
                </div>
            </div>

            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">LRN</th>
                        <th class="py-2 px-4 text-left border">First Name</th>
                        <th class="py-2 px-4 text-left border">Last Name</th>
                        <th class="py-2 px-4 text-left border">Sex</th>
                        <th class="py-2 px-4 text-left border">Grade</th>
                        <th class="py-2 px-4 text-left border">Section</th>
                        <th class="py-2 px-4 text-left border">
                            Offenses/Penalties
                        </th>
                        <th class="py-2 px-4 text-left border">
                            Parent's Email
                        </th>
                        <th class="py-2 px-4 text-left border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in studentsData.data" :key="student.id">
                        <td class="py-2 px-4 border">{{ student.lrn }}</td>
                        <td class="py-2 px-4 border">
                            {{ student.firstname }}
                        </td>
                        <td class="py-2 px-4 border">{{ student.lastname }}</td>
                        <td class="py-2 px-4 border">{{ student.sex }}</td>
                        <td class="py-2 px-4 border">
                            {{ student.grade?.grade ?? "N/A" }}
                        </td>
                        <td class="py-2 px-4 border">
                            {{ student.section?.section ?? "N/A" }}
                        </td>
                        <td class="py-2 px-4 border">
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
                                        class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"
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
                                        class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"
                                    >
                                        {{
                                            student.submitted_major_offenses_count
                                        }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-2 px-4 border">
                            <div v-if="student.email">
                                <Link
                                    :href="route('show.email', student.id)"
                                    class="px-2 py-1 text-sm bg-blue-300 text-dark p-3 rounded"
                                >
                                    {{ student.email }}
                                </Link>
                            </div>
                        </td>

                        <td class="py-2 px-4 border">
                            <Link
                                :href="route('students.edit', student.id)"
                                class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded"
                                >Edit</Link
                            >
                            <Link
                                :href="route('students.print', student.id)"
                                class="px-2 py-1 text-sm bg-blue-500 text-white p-3 rounded"
                                >Print</Link
                            >
                            <button
                                v-if="
                                    student.submitted_minor_offenses_count ===
                                        0 &&
                                    student.submitted_major_offenses_count === 0
                                "
                                @click="DeleteStudent(student.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white rounded"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :pagination="studentsData" />
    </AuthenticatedLayout>
</template>
