<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3'; 
import Swal from 'sweetalert2'; 

const props = defineProps({ 
    errors: Object,
    student: Object,
    minorOffenses: Array,
    submittedminorOffenses: Array,
});

// Initialize the form object
const form = useForm({
    minor_offense_id: '',
    lrn: props.student.lrn, 
    student_firstname: props.student.firstname,
    student_middlename: props.student.middlename,
    student_lastname: props.student.lastname,
    student_sex: props.student.sex, 
    student_grade: props.student.grade?.grade??'N/A', // Directly from props
    student_section: props.student.section?.section??'N/A', // Directly from props
});

const sanction = useForm({
    id: ''
});

const Cleanse = (id) => {
    sanction.id = id;
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to mark this offense as acted?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cleanse it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading alert
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the minor offense.',
                didOpen: () => {
                    Swal.showLoading(); // Start the loading spinner
                },
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            // Post request to the server
            sanction.post(route('minor.sanction', { id: id }), {
                onFinish: () => {
                    Swal.close(); // Close the loading alert once the request finishes

                    // Show a success message
                    Swal.fire(
                        'Saved!',
                        'The offense has been marked as acted successfully.',
                        'success'
                    );
                },
                onError: () => {
                    Swal.close(); // Close the loading alert if an error occurs

                    // Show an error message
                    Swal.fire(
                        'Error!',
                        'There was a problem marking the offense as acted.',
                        'error'
                    );
                }
            });
        }
    });
};

// Function to save a minor offense
const saveMinorOffense = () => {
    if (form.minor_offense_id === '') {
        form.post(route('minor.store'));
    } else {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to save this minor offense?",
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
                    text: 'Please wait while we save the minor offense.',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                    showConfirmButton: false,
                });

                // If user confirms, proceed with form submission
                form.post(route('minor.store'), {
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
<Head title="Minor Offenses" />
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Student</h5>
                <Link :href="route('students.index')" class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4">Back</Link>
            </div>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        {{ student.lrn }},
                        {{ student.firstname }},
                        {{ student.middlename }}
                        {{ student.lastname }},
                        {{ student.sex }},
                        Grade {{ student.grade?.grade??'N/A' }}, <!-- Auto-filled from props -->
                        {{ student.section?.section??'N/A'}} <!-- Auto-filled from props -->
                    </div>
                </div>
            </div>

            <div class="mt-4 mx-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Minor Offense</h5>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">Offense Committed</th>
                            <th class="py-2 px-4 text-left border">Penalty</th>
                            <th class="py-2 px-4 text-left border">Recorded date</th>
                            <th class="py-2 px-4 text-left border">Sanction</th>
                            <th class="py-2 px-4 text-left border">Acted Date</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="offense in submittedminorOffenses" :key="offense.id">
                            <td class="py-2 px-4 border">{{ offense.minor_offense.minor_offenses }}</td>
                            <td class="py-2 px-4 border">{{ offense.minor_penalty.minor_penalties }}</td>
                            <td class="py-2 px-4 border">{{ offense.offense_date }}</td>
                            <td class="py-2 px-4 border">
                                <button
                                v-if="offense.sanction === 0"
                                @click="Cleanse(offense.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded"
                            >
                                Cleanse
                            </button>
                            <button
                                v-else
                                class="px-2 py-1 text-sm bg-green-600 text-white p-3 rounded"
                                disabled
                            >
                                Acted
                            </button>
                            </td>
                            <td class="py-2 px-4 border">{{ offense.cleansed_date }}</td>

                        </tr>
                    </tbody>
                </table>
            </div>

            <form @submit.prevent="saveMinorOffense()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Select Minor Offenses</label>
                            <select v-model="form.minor_offense_id" class="py-1 w-full">
                                <option value="">Select Minor Offense</option>
                                <!-- Loop through the minorOffenses prop to populate the select -->
                                <option v-for="offense in minorOffenses" :key="offense.id" :value="offense.id">
                                    {{ offense.minor_offenses }}
                                </option>
                            </select>
                            <div v-if="errors.minor_offense_id" class="text-red-500">{{ errors.minor_offense_id }}</div>
                        </div>
                        <div class="mb-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
