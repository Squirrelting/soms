<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3'; 
import Swal from 'sweetalert2'; 

const props = defineProps({ 
    errors: Object,
    student: Object,
    majorOffenses: Array,
    submittedmajorOffenses: Array,
    user: { // Accept user prop from backend
        type: Object,
        required: true
    },
});

const maxDate = new Date().toISOString().split('T')[0];

// Initialize the form object
const form = useForm({
    committed_date: '',
    major_offense: '',
    lrn: props.student.lrn, 
    student_firstname: props.student.firstname,
    student_middlename: props.student.middlename,
    student_lastname: props.student.lastname,
    student_sex: props.student.sex,
    student_schoolyear: props.student.schoolyear,
    student_quarter: props.student.quarter, 
    student_grade: props.student.grade?.grade??'N/A', // Directly from props
    student_section: props.student.section?.section??'N/A', // Directly from props
});

const sanction = useForm({
    id: ''
});

const Resolve = (id) => {
    sanction.id = id;
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to mark this offense as RESOLVED?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Resolve it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading alert
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the major offense.',
                didOpen: () => {
                    Swal.showLoading(); // Start the loading spinner
                },
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            // Post request to the server
            sanction.post(route('major.sanction', { id: id }), {
                onFinish: () => {
                    Swal.close(); // Close the loading alert once the request finishes

                    // Show a success message
                    Swal.fire(
                        'Saved!',
                        'The offense has been marked as RESOLVED successfully.',
                        'success'
                    );
                },
                onError: () => {
                    Swal.close(); // Close the loading alert if an error occurs

                    // Show an error message
                    Swal.fire(
                        'Error!',
                        'There was a problem marking the offense as Resolved.',
                        'error'
                    );
                }
            });
        }
    });
};

// Function to save a major offense
const saveMajorOffense = () => {
    if (form.major_offense === ''|| form.committed_date ==='') {
        form.post(route('major.store'));
    } else {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to save this major offense?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading alert
                const loadingAlert = Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while we save the major offense.',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });

                // If user confirms, proceed with form submission
                form.post(route('major.store'), {
                    onSuccess: () => {
                        // Close loading alert on success
                        loadingAlert.close();
                        Swal.fire(
                            'Saved!',
                            'The offense has been added successfully.',
                            'success'
                        );
                        form.reset(); // Optionally reset the form after success
                    },
                    onError: () => {
                        // Close loading alert on error
                        loadingAlert.close();
                        Swal.fire(
                            'Error!',
                            'There was a problem saving the offense.',
                            'error'
                        );
                    },
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // If user cancels
                Swal.fire(
                    'Cancelled',
                    'The offense was not saved.',
                    'error'
                );
            }
        });
    }
};
</script>
<template>
    <Head title="Major Offenses" />
    <AuthenticatedLayout>

        <div class="mt-4 mx-4 space-y-6">

    <!-- Wrapper Section with White Background -->
<div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

<!-- Header Section -->
<div class="flex flex-col space-y-2">
    <h3 class="text-lg font-bold text-gray-800">Major Offense</h3>
    <div class="text-gray-600 text-sm ml-1">
        <span class="font-semibold">Student:</span> {{ student.lrn }} |
        {{ student.firstname }} {{ student.middlename }} {{ student.lastname }} |
        {{ student.sex }} | Grade {{ student.grade?.grade ?? 'N/A' }} | Section {{ student.section?.section ?? 'N/A' }}
    </div>
</div>

<!-- Major Offense Section -->
<table class="w-full bg-white border border-gray-200 shadow rounded-lg">
    <thead>
        <tr class="bg-gray-100 text-gray-700 text-sm font-medium">
            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Offense Committed</th>
            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Penalty</th>
            <th class="p-2 text-left border-b border-r border-gray-200">S.Y. Quarter</th>
            <th class="p-2 text-left border-b border-r border-gray-200">Grade and Section</th>
            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Committed Date</th>
            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Recorded By</th>
            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Recorded Date</th>
            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Sanction</th>
            <th class="py-2 px-4 text-left border-b">Resolved By</th>
            <th class="py-2 px-4 text-left border-b">Resolved Date</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="offense in submittedmajorOffenses" :key="offense.id" class="hover:bg-gray-50 text-sm text-gray-800">
            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.major_offense }}</td>
            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.major_penalty }}</td>
            <td class="p-2 border-b border-r border-gray-200">{{ offense.student_schoolyear }}, {{ offense.student_quarter }}</td>
            <td class="p-2 border-b border-r border-gray-200">Grade {{ offense.student_grade }}, {{ offense.student_section }}</td>
            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.committed_date }}</td>
            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.recorded_by }}</td>
            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.recorded_date }}</td>
            <td class="py-2 px-4 border-b border-r border-gray-200">
                <button
                    :disabled="props.user.roles[0].name.includes('ADVISER')"
                    v-if="offense.sanction === 0"
                    @click="Resolve(offense.id)"
                    class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700"
                >
                    Unresolved
                </button>
                <button
                    v-else
                    class="px-2 py-1 text-xs bg-green-600 text-white rounded cursor-not-allowed"
                    disabled
                >
                    Resolved
                </button>
            </td>
            <td class="py-2 px-4 border-b">{{ offense.cleansed_by }}</td>
            <td class="py-2 px-4 border-b">{{ offense.cleansed_date }}</td>
        </tr>
    </tbody>
</table>
</div>



<!-- Form Section -->
<form @submit.prevent="saveMajorOffense()" class="bg-white p-6 rounded shadow-sm" v-if="!props.user.roles[0].name.includes('ADVISER')"
>
    <div class="grid grid-cols-10 gap-4">
        <!-- Select Major Offenses (70% width) -->
        <div class="col-span-7">
            <label class="block text-sm font-medium mb-1">Select Major Offenses</label>
            <select v-model="form.major_offense" class="py-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Major Offense</option>
                <option v-for="offense in majorOffenses" :key="offense.id" :value="offense.major_offenses">
                    {{ offense.major_offenses }}
                </option>
            </select>
            <div v-if="errors.major_offense" class="text-red-500 text-sm mt-1">{{ errors.major_offense }}</div>
        </div>
        
        <!-- Committed Date (30% width) -->
        <div class="col-span-3">
            <label class="block text-sm font-medium mb-1">Committed Date</label>
            <input type="date" v-model="form.committed_date" :max="maxDate" class="py-2 w-full border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <div v-if="errors.committed_date" class="text-red-500 text-sm mt-1">{{ errors.committed_date }}</div>
        </div>
    </div>
    
    <!-- Save Button -->
    <div class="mt-4">
        <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 text-white py-2 px-5 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span v-if="form.processing">Saving...</span>
            <span v-else>Save</span>
        </button>
    </div>
</form>

        </div>
    </AuthenticatedLayout>
</template>
