<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    errors: Object,
    student: Object,
    grades: Array,
    sections: {
        type: Array,  // Keep this as Array if you expect it to be an array
        default: () => []
    }
});


// Form data setup
const form = useForm({
    lrn: props.student.lrn,
    firstname: props.student.firstname,
    lastname: props.student.lastname,
    sex: props.student.sex,
    grade_id: props.student.grade_id,  // Keep as grade_id
    section_id: props.student.section_id,  // Keep as section_id
    email: props.student.email,
});

// Grade and Section setup
const grades = ref(props.grades);
const sections = ref(props.sections);

// Fetch sections based on selected grade dynamically
const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(`/students/sections?grade_id=${gradeId}`);
        sections.value = response.data.sections;
        form.section_id = "";  // Reset section_id after fetching new sections
    } catch (error) {
        console.error('Error fetching sections:', error);
    }
};

// Watch for changes in the grade and load sections accordingly
watch(() => form.grade_id, (newGrade) => {  // Keep form.grade_id
    fetchSections(newGrade);
});

const updateStudent = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this student's information?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Updating...",
                text: "Please wait while we update the student data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            form.put(route("students.update", props.student.id), {
                onSuccess: () => {
                    Swal.fire(
                        "Updated!",
                        "Student information has been updated.",
                        "success"
                    );
                    form.reset();
                },
                onError: (errors) => {
                    console.log(errors);  // Log validation errors
                    Swal.fire(
                        "Error",
                        "Failed to update the student. Please check the input.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Edit Student" />

    <AuthenticatedLayout>
        <div v-if="$page.props.flash.message" role="alert" class="alert alert-info mt-4 mx-5 px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $page.props.flash.message }}</span>
        </div>

        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Edit Student</h5>
                <Link :href="route('students.index')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
            </div>

            <form @submit.prevent="updateStudent()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <!-- Student ID -->
                        <div class="mb-3">
                            <label>Student ID</label>
                            <input type="text" v-model="form.lrn" class="py-1 w-full" />
                            <div v-if="errors.lrn" class="text-red-500">{{ errors.lrn }}</div>
                        </div>

                        <!-- First Name -->
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" v-model="form.firstname" class="py-1 w-full" />
                            <div v-if="errors.firstname" class="text-red-500">{{ errors.firstname }}</div>
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" v-model="form.lastname" class="py-1 w-full" />
                            <div v-if="errors.lastname" class="text-red-500">{{ errors.lastname }}</div>
                        </div>

                        <!-- Sex -->
                        <div class="mb-3">
                            <label>Sex</label>
                            <select v-model="form.sex" class="py-1 w-full">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div v-if="errors.sex" class="text-red-500">{{ errors.sex }}</div>
                        </div>

                        <!-- Grade -->
                        <div class="mb-3">
                            <label>Grade</label>
                            <select v-model="form.grade_id" class="py-1 w-full">
                                <option value="">Select Grade</option>
                                <option v-for="grade in grades" :key="grade.id" :value="grade.id">
                                    {{ grade.grade }}
                                </option>
                            </select>
                            <div v-if="errors.grade_id" class="text-red-500">{{ errors.grade_id }}</div>
                        </div>

                        <!-- Section -->
                        <div class="mb-3">
                            <label>Section</label>
                            <select v-model="form.section_id" class="py-1 w-full">
                                <option value="">Select Section</option>
                                <option v-for="section in sections" :key="section.id" :value="section.id">
                                    {{ section.section }}
                                </option>
                            </select>
                            <div v-if="errors.section_id" class="text-red-500">{{ errors.section_id }}</div>
                        </div>

                        <!-- Parent's Email -->
                        <div class="mb-3">
                            <label>Parent's Email</label>
                            <input type="email" v-model="form.email" class="py-1 w-full" />
                            <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button type="submit" :disabled="form.processing" class="bg-blue-500 text-white py-2 px-5 rounded mb-4">
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
