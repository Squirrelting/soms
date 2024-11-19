<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, watch } from "vue"; 
import axios from 'axios'; 
import MinorMultiSelect from "@/Components/MinorMultiSelect.vue";
import MajorMultiSelect from "@/Components/MajorMultiSelect.vue";

const props = defineProps({
    studentsData: Array,
    errors: Object,
    grades: Array,
    sections: Array,
    minorOffenses: Array,
    majorOffenses: Array
});

const yearToday = ref(new Date().getFullYear() || "");
const nextYear = ref(yearToday.value + 1 || "");


const form = useForm({
    minor_offenses: [],
    major_offenses: [],
    lrn: "",
    firstname: "",
    middlename: "",
    lastname: "",
    sex: "",
    grade_id: "", 
    section_id: "", 
    email: "",
    quarter: "",
    yeartoday: yearToday.value,
    nextyear: nextYear.value,
});

const sections = ref(props.sections);

// Watch for changes in yearToday to update nextYear
watch(() => form.yeartoday, (newYearToday) => {
    form.nextyear = newYearToday + 1;
});

// Watch for changes in yearToday to update nextYear dynamically
watch(() => form.grade_id, async (newGrade) => {
    if (newGrade) {
        await fetchSections(newGrade);
    } else {
        form.section_id = ""; 
        sections.value = [];
    }
});

const fetchSections = async (gradeId) => {
    try {
        const response = await axios.get(`/students/sections?grade_id=${gradeId}`);
        sections.value = response.data.sections;

        // Only reset section_id if it's not already set by the student autofill
        if (!form.section_id) {
            form.section_id = "";
        }
    } catch (error) {
        console.error('Error fetching sections:', error);
    }
};


const studentsData = ref(props.studentsData);

watch(() => props.studentsData, (newData) => {
    studentsData.value = newData;
}, { immediate: true });


watch(() => form.lrn, (newLrn) => {
    const student = studentsData.value.find(student => student.lrn === parseInt(newLrn, 10));

    if (student) {
        form.firstname = student.firstname;
        form.middlename = student.middlename;
        form.lastname = student.lastname;
        form.sex = student.sex;
        form.grade_id = student.grade_id;
        form.section_id = student.section_id; // Prefill section_id here
        form.email = student.email;

        // Trigger fetchSections for the grade, which may update sections in the dropdown
        if (student.grade_id) {
            fetchSections(student.grade_id);
        }
    } else {
        console.log("No student found with that LRN:", newLrn);
    }
});


const formatName = (name) => {
    return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
};
const previewData = () => {
    // Check if both minor and major offenses arrays are empty
    if (form.minor_offenses.length === 0 && form.major_offenses.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Offense is required',
            text: 'Please provide at least one minor or major offense.'
        });
        return; // Stop the function if offenses are missing
    }

    // Check if any offense is missing a date
    const isMinorDateMissing = form.minor_offenses.some(offense => !offense.date_committed);
    const isMajorDateMissing = form.major_offenses.some(offense => !offense.date_committed);

    if (isMinorDateMissing || isMajorDateMissing) {
        Swal.fire({
            icon: 'error',
            title: 'Date on offense is required',
            text: 'Please provide a date for each offense.'
        });
        return; // Stop the function if dates are missing
    }

    // Convert the minor and major offenses arrays into readable strings with dates
    const minorOffensesString = form.minor_offenses
        .map(offense => `${offense.minor_offenses} (Date: ${offense.date_committed || "N/A"})`)
        .join("<br>");

    const majorOffensesString = form.major_offenses
        .map(offense => `${offense.major_offenses} (Date: ${offense.date_committed || "N/A"})`)
        .join("<br>");    

    // Display SweetAlert with offense data preview
    Swal.fire({
        title: "Save and Send to Adviser's Email",
        html: `
            <div style="text-align: left; font-size: 18px;">
                <p><strong>LRN:</strong> ${form.lrn}</p>
                <p><strong>Adviser's Email:</strong> ${form.email}</p>
                <p><strong>First Name:</strong> ${form.firstname}</p>
                <p><strong>Middle Name:</strong> ${form.middlename}</p>
                <p><strong>Last Name:</strong> ${form.lastname}</p>
                <p><strong>Sex:</strong> ${form.sex}</p>
                <p><strong>Grade:</strong> ${form.grade_id}</p>
                <p><strong>Section:</strong> ${form.section_id}</p>
                <p><strong>Quarter:</strong> ${form.quarter}</p>
                <p><strong>School Year:</strong> ${form.yeartoday} - ${form.nextyear}</p>
                
                <hr style="margin: 10px 0;">
                
                ${minorOffensesString ? `<p><strong>Minor Offenses</strong><br>${minorOffensesString}</p>` : ""}
                ${majorOffensesString ? `<p><strong>Major Offenses</strong><br>${majorOffensesString}</p>` : ""}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Save",
        cancelButtonText: "Cancel",
        customClass: {
            popup: 'swal-wide' // Add a custom class if you want to control the width in CSS
        },
        preConfirm: () => {
            // Call saveStudent here to submit the form if confirmed
            saveStudent();
        }
    });
};




const saveStudent = () => {
    Swal.fire({
                title: "Saving...",
                text: "Please wait while we save and send the student data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            }); 
            form.post(route("students.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Send to Email Successfully",
                        text: "The student's data has been successfully added!",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    form.reset();
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem saving the student. Please try again.",
                    });
                },
            });
        };
</script>

<template>
    <Head title="Input Student" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

            <div class="flex justify-between">
                <h5 class="m-4">Input Student</h5>
            </div>

            <form @submit.prevent="saveStudent()">
    <div class="col-span-12">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-6 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's LRN</label>
                <input type="text" v-model="form.lrn" class="py-1 w-full bg-gray-200 border border-gray-500 rounded" />
                <div v-if="errors.lrn" class="text-red-500">{{ errors.lrn }}</div>
            </div>
            <div class="col-span-6 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Adviser's Email</label>
                <input type="email" v-model="form.email" class="py-1 w-full bg-gray-200 border border-gray-500 rounded" />
                <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
            </div>
        </div>

        <hr class="my-4 border-blue-800">

<!-- Second Section: Student's Name -->
<div class="grid grid-cols-12 gap-4">
    <div class="col-span-4 mb-3">
        <label class="block text-gray-700 font-semibold mb-1">Student's First Name</label>
        <input 
            type="text" 
            v-model="form.firstname" 
            @input="form.firstname = formatName(form.firstname)" 
            class="py-1 w-full bg-gray-200 border border-gray-500 rounded" 
        />
        <div v-if="errors.firstname" class="text-red-500">{{ errors.firstname }}</div>
    </div>
    <div class="col-span-4 mb-3">
        <label class="block text-gray-700 font-semibold mb-1">Student's Middle Name (Optional)</label>
        <input 
            type="text" 
            v-model="form.middlename" 
            @input="form.middlename = formatName(form.middlename)" 
            class="py-1 w-full bg-gray-200 border border-gray-500 rounded" 
        />
        <div v-if="errors.middlename" class="text-red-500">{{ errors.middlename }}</div>
    </div>
    <div class="col-span-4 mb-3">
        <label class="block text-gray-700 font-semibold mb-1">Student's Last Name</label>
        <input 
            type="text" 
            v-model="form.lastname" 
            @input="form.lastname = formatName(form.lastname)" 
            class="py-1 w-full bg-gray-200 border border-gray-500 rounded" 
        />
        <div v-if="errors.lastname" class="text-red-500">{{ errors.lastname }}</div>
    </div>
</div>


        <hr class="my-4 border-blue-800">

        <!-- Third Section: Sex, Grade, and Section -->
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-4 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's Sex</label>
                <select v-model="form.sex" class="py-1 w-full bg-gray-200 border border-gray-500 rounded">
                    <option value="">Select Sex</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
                <div v-if="errors.sex" class="text-red-500">{{ errors.sex }}</div>
            </div>
            <div class="col-span-4 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's Grade</label>
                <select v-model="form.grade_id" class="py-1 w-full bg-gray-200 border border-gray-500 rounded">
                    <option value="">Select Grade</option>
                    <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.grade }}</option>
                </select>
                <div v-if="errors.grade_id" class="text-red-500">{{ errors.grade_id }}</div>
            </div>
            <div class="col-span-4 mb-3">
                <label class="block text-gray-700 font-semibold mb-1">Student's Section</label>
                <select v-model="form.section_id" class="py-1 w-full bg-gray-200 border border-gray-500 rounded">
                    <option value="">Select Section</option>
                    <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.section }}</option>
                </select>
                <div v-if="errors.section_id" class="text-red-500">{{ errors.section_id }}</div>
            </div>
        </div>

        <hr class="my-4 border-blue-800">

        <div class="grid grid-cols-12 gap-4">
    <div class="col-span-4 mb-3">
        <label class="block text-gray-700 font-semibold mb-1">Select Quarter</label>
        <select v-model="form.quarter" class="py-1 w-full bg-gray-200 border border-gray-500 rounded">
            <option value="">Select</option>
            <option value="1st Quarter">1st Quarter</option>
            <option value="2nd Quarter">2nd Quarter</option>
            <option value="3rd Quarter">3rd Quarter</option>
            <option value="4th Quarter">4th Quarter</option>

        </select>
        <div v-if="errors.quarter" class="text-red-500 mt-1 text-sm">{{ errors.quarter }}</div>
    </div>
    
    <div class="col-span-4 mb-3">
    <label class="block text-gray-700 font-semibold mb-1">School Year</label>
    <input type="number" :max="new Date().getFullYear() + 1" step="1" v-model="form.yeartoday" 
           class="py-1 w-full bg-gray-200 border border-gray-500 rounded" />
    <div v-if="errors.yeartoday" class="text-red-500 mt-1 text-sm">{{ errors.yeartoday }}</div>
</div>

<div class="col-span-4 mb-3">
    <label class="block text-gray-700 font-semibold mb-1">To</label>
    <input type="number" :value="form.nextyear" 
           class="py-1 w-full bg-gray-200 border border-gray-500 rounded cursor-not-allowed pointer-events-none" 
           readonly />
    <div v-if="errors.nextyear" class="text-red-500 mt-1 text-sm">{{ errors.nextyear }}</div>
</div>
</div>
<div class="grid grid-cols-12 gap-4">

    <div class="col-span-6 mb-3">

        <MinorMultiSelect :form="form" :options="props.minorOffenses"/>
        </div>

        <div class="col-span-6 mb-3">
        <MajorMultiSelect :form="form" :options="props.majorOffenses"/>
    </div>
</div>
        <!-- Submit Button -->
        <div class="mb-3">
    <button type="button" :disabled="form.processing" @click="previewData" class="bg-blue-500 text-white py-2 px-5 rounded mb-4">
        <span v-if="form.processing">Processing...</span>
        <span v-else>Next</span>
    </button>
</div>
    </div>
</form>
</div>
        </div>
    </AuthenticatedLayout>
</template>
