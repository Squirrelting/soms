<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from 'axios';

// Define props
const props = defineProps({
    errors: Object,
    grades: Array,
    sections: Array,
});

// Define form using useForm
const form = useForm({
    grade_id: "", 
    section_id: "", 
    file: null // Adding file to the form object
});

// Create a ref for the file input
const fileInput = ref(null);

const sections = ref(props.sections);

// Watch for grade changes and fetch sections accordingly
watch(() => form.grade_id, async (newGrade) => {
    if (newGrade) {
        await fetchSections(newGrade);
    } else {
        form.section_id = ""; 
        sections.value = [];
    }
});

// Function to fetch sections based on selected grade
const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(`/students/sections?grade_id=${gradeId}`);
        sections.value = response.data.sections;
        form.section_id = ""; 
    } catch (error) {
        console.error('Error fetching sections:', error);
    }
};

// Import student function with SweetAlert and Inertia post
const importStudent = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to add this student!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Saving...",
                text: "Please wait while we save the student.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Append the file from the input element
            form.file = fileInput.value.files[0];

            // Send form data using Inertia's post method
            form.post(route("import.store"), {
                onFinish: () => {
                    Swal.close(); // Close the loading alert once the request finishes

                    // Show success message
                    Swal.fire({
                        icon: "success",
                        title: "Student Added",
                        text: "The student has been successfully added!",
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    form.reset(); // Reset the form after successful submission
                },
                onError: () => {
                    Swal.close(); // Close the loading alert if an error occurs

                    // Show error message
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem saving the student. Please try again.",
                    });
                }
            });
        }
    });
};
</script>


<template>
    <Head title="Import Student" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Import Student</h5>
                <Link
                    :href="route('students.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link>
            </div>

            <form @submit.prevent="importStudent()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Student's Grade</label>
                            <select v-model="form.grade_id" class="py-1 w-full">
                                <option value="">Select Grade</option>
                                <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.grade }}</option>
                            </select>
                            <div v-if="errors.grade_id" class="text-red-500">{{ errors.grade_id }}</div>
                        </div>

                        <div class="mb-3">
                            <label>Student's Section</label>
                            <select v-model="form.section_id" class="py-1 w-full">
                                <option value="">Select Section</option>
                                <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.section }}</option>
                            </select>
                            <div v-if="errors.section_id" class="text-red-500">{{ errors.section_id }}</div>
                        </div>

                        <!-- File input -->
                        <div>
                            <label class="block mb-1">Upload CSV File:</label>
                            <input type="file" ref="fileInput" required class="border border-gray-300 rounded py-1 w-full" />
                        </div>

                        <!-- Submit button -->
                        <div>
                            <button type="submit" class="bg-blue-500 text-white py-2 px-5 rounded">Import CSV</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
