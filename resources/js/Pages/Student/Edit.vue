<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { onMounted, ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    errors: Object,
    student: Object,
    grades: Array,
    sections: {
        type: Array,
        default: () => [],
    },
});

const yearToday = ref("");
const nextYear = ref("");

// Form data setup
const form = useForm({
    lrn: props.student.lrn,
    firstname: props.student.firstname,
    middlename: props.student.middlename,
    lastname: props.student.lastname,
    sex: props.student.sex,
    quarter: props.student.quarter,
    grade_id: props.student.grade_id,
    section_id: props.student.section_id,
    email: props.student.email,
    yeartoday: yearToday.value,
    nextyear: nextYear.value,
});

onMounted(() => {
    yearToday.value = props.student.schoolyear.split("-")[0];
    nextYear.value = props.student.schoolyear.split("-")[1];
    form.yeartoday = yearToday.value;
    form.nextyear = nextYear.value;
});

watch(() => form.yeartoday, (newYearToday) => {
    form.nextyear = parseInt(newYearToday) + 1;
});


// Watch for changes in grade_id to dynamically load sections
const sections = ref(props.sections);
const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(
            `/students/sections?grade_id=${gradeId}`
        );
        sections.value = response.data.sections;
        form.section_id = ""; // Reset section_id after fetching new sections
    } catch (error) {
        console.error("Error fetching sections:", error);
    }
};




// Watch for changes in grade_id
watch(
    () => form.grade_id,
    (newGrade) => {
        if (newGrade) {
            fetchSections(newGrade);
        } else {
            form.section_id = "";
            sections.value = [];
        }
    }
);

const formatName = (name) => {
    return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
};

const checkLrnExists = async (lrn) => {
    try {
        const response = await axios.post(route("check.lrn"), {
            lrn,
            student_id: props.student.id, // Pass the current student ID
        });
        return response.data.exists; // Server response
    } catch (error) {
        console.error("Error checking LRN:", error.response?.data || error.message);
        return false; // Default to no match on error
    }
};


const updateStudent = async () => {
    const lrnExists = await checkLrnExists(form.lrn);

    if (lrnExists && form.lrn !== props.student.lrn) {
        Swal.fire({
            icon: "error",
            title: "Duplicate LRN",
            text: "LRN with that already exists for another student.",
        });
        return; // Stop further execution if duplicate LRN exists
    }

    // Proceed with the update if no duplicate
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
                text: "Please wait while we update the student.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            form.put(route("students.update", props.student.id), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Student Updated",
                        text: "The student's information has been successfully updated!",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem updating the student. Please try again.",
                    });
                },
            });
        }
    });
};

</script>
<template>
    <Head title="Edit Student" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

            <div class="flex justify-between">
                <h5 class="m-4">Edit Student</h5>
            </div>

            <form @submit.prevent="updateStudent()">
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-6 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's LRN</label
                            >
                            <input
                                type="text"
                                v-model="form.lrn"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.lrn" class="text-red-500">
                                {{ errors.lrn }}
                            </div>
                        </div>
                        <div class="col-span-6 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Adviser's Email</label
                            >
                            <input
                                type="email"
                                v-model="form.email"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.email" class="text-red-500">
                                {{ errors.email }}
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-blue-800" />

                    <!-- Second Section: Student's Name -->
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's First Name</label
                            >
                            <input
                                type="text"
                                @input="form.firstname = formatName(form.firstname)" 
                                v-model="form.firstname"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.firstname" class="text-red-500">
                                {{ errors.firstname }}
                            </div>
                        </div>
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's Middle Name (Optional)</label
                            >
                            <input
                                type="text"
                                @input="form.middlename = formatName(form.middlename)" 
                                v-model="form.middlename"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.middlename" class="text-red-500">
                                {{ errors.middlename }}
                            </div>
                        </div>
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's Last Name</label
                            >
                            <input
                                type="text"
                                @input="form.lastname = formatName(form.lastname)" 
                                v-model="form.lastname"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.lastname" class="text-red-500">
                                {{ errors.lastname }}
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-blue-800" />

                    <!-- Third Section: Sex, Grade, and Section -->
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's Sex</label
                            >
                            <select
                                v-model="form.sex"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            >
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div v-if="errors.sex" class="text-red-500">
                                {{ errors.sex }}
                            </div>
                        </div>
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's Grade</label
                            >
                            <select
                                v-model="form.grade_id"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            >
                                <option value="">Select Grade</option>
                                <option
                                    v-for="grade in grades"
                                    :key="grade.id"
                                    :value="grade.id"
                                >
                                    {{ grade.grade }}
                                </option>
                            </select>
                            <div v-if="errors.grade_id" class="text-red-500">
                                {{ errors.grade_id }}
                            </div>
                        </div>
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Student's Section</label
                            >
                            <select
                                v-model="form.section_id"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            >
                                <option value="">Select Section</option>
                                <option
                                    v-for="section in sections"
                                    :key="section.id"
                                    :value="section.id"
                                >
                                    {{ section.section }}
                                </option>
                            </select>
                            <div v-if="errors.section_id" class="text-red-500">
                                {{ errors.section_id }}
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 border-blue-800" />

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >Select Quarter</label
                            >
                            <select
                                v-model="form.quarter"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            >
                                <option value="">Select</option>
                                <option value="1st Quarter">
                                    1st Quarter
                                </option>
                                <option value="2nd Quarter">
                                    2nd Quarter
                                </option>
                                <option value="3rd Quarter">
                                    3rd Quarter
                                </option>
                                <option value="4th Quarter">
                                    4th Quarter
                                </option>
                            </select>
                            <div
                                v-if="errors.quarter"
                                class="text-red-500 mt-1 text-sm"
                            >
                                {{ errors.quarter }}
                            </div>
                        </div>

                        <div class="col-span-2 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >School Year</label
                            >
                            <input
                                type="number"
                                :max="new Date().getFullYear() + 1"
                                step="1"
                                v-model="form.yeartoday"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div
                                v-if="errors.yeartoday"
                                class="text-red-500 mt-1 text-sm"
                            >
                                {{ errors.yeartoday }}
                            </div>
                        </div>

                        <div class="col-span-2 mb-3">
                            <label
                                class="block text-gray-700 font-semibold mb-1"
                                >To</label
                            >
                            <input
                                type="number"
                                :value="form.nextyear"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded cursor-not-allowed pointer-events-none"
                                readonly
                            />
                            <div
                                v-if="errors.nextyear"
                                class="text-red-500 mt-1 text-sm"
                            >
                                {{ errors.nextyear }}
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                        >
                            <span v-if="form.processing">Updating...</span>
                            <span v-else>Update</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </AuthenticatedLayout>
</template>
