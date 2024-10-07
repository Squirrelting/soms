<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import axios from "axios";

// Initialize the reactive variables
const props = defineProps({
    sectionsTable: Object,
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
const sectionsData = ref(props.sectionsTable);
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
            route("section.index"),
            { search: newSearch, grade: newGrade, section: newSection },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: (page) => {
                    sectionsData.value = page.props.sectionsTable; // Make sure you're assigning `sections`, not `section`
                },
            }
        );
    }
);

const DeleteSection = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this section! This action cannot be undone.",
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
                text: "Please wait while we delete the section data.",
                didOpen: () => Swal.showLoading(),
            });

            router.delete(route("section.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    sectionsData.value.data = sectionsData.value.data.filter(
                        (section) => section.id !== id
                    );

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The section has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => Swal.fire({
                    icon: "error",
                    title: "Failed!",
                    text: "There was a problem deleting the section. Please try again.",
                }),
            });
        }
    });
};
</script>

<template>
    <Head title="Section" />

    <AuthenticatedLayout>
        <!-- Message from the controller -->
        <div
            v-if="$page.props.flash.message"
            role="alert"
            class="alert alert-info mt-4 mx-5 px-4 py-2"
        >
            <span>{{ $page.props.flash.message }}</span>
        </div>

        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold text-gray-700">
                    Grade and Section List
                </h5>

                <div class="flex items-center">
                    <!-- Search Input -->
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by section name"
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

                    <!-- Section Dropdown -->
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

                    <!-- Add Section Button -->
                    <Link
                        :href="route('section.create')"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg ml-4 transition ease-in-out duration-150"
                    >
                        Add Section
                    </Link>
                </div>
            </div>

            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">Grade</th>
                        <th class="py-2 px-4 text-left border">Section</th>
                        <th class="py-2 px-4 text-left border">
                            Edit Section name
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="section in sectionsData.data" :key="section.id">
                        <td class="py-2 px-4 border">
                            {{ section.grade.grade }}
                        </td>
                        <td class="py-2 px-4 border">{{ section.section }}</td>
                        <td class="py-2 px-4 border">
                            <Link
                                :href="route('section.edit', section.id)"
                                class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded"
                                >Edit
                            </Link>
                            <button
                                @click="DeleteSection(section.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination Component -->
            <Pagination :pagination="sectionsData" />
        </div>
    </AuthenticatedLayout>
</template>
