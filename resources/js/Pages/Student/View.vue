<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import Swal from 'sweetalert2';

const props = defineProps({
    errors: Object,
    student: Object,
    submittedminorOffenses: Array,
    submittedmajorOffenses: Array,
});

</script>

<template>
    <Head title="Show Student" />

    <AuthenticatedLayout>
        <div class="my-6 mx-6 space-y-6">
            
            <!-- Header Section -->
            <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-lg">
            <!-- Student Information Section -->
            <div class=" text-gray-800">
                <span class="font-semibold">Student:</span> 
                {{ student.lrn }} | 
                {{ student.firstname }} {{ student.middlename }} {{ student.lastname }} |
                {{ student.sex }} |
                Grade {{ student.grade?.grade ?? 'N/A' }} |
                Section {{ student.section?.section ?? 'N/A' }}
            </div>

                <!-- Print Button -->
                <a 
                    :href="route('printrecord', { student: props.student.id })"
                    target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300"
                >
                    Print
                </a>
            </div>



            <!-- Minor Offense Section -->
            <div v-if="submittedminorOffenses.length" class="bg-white p-4 rounded-lg shadow-lg">
                <div class="mb-4">
                    <h5 class="text-lg font-bold text-gray-800">Minor Offense</h5>
                </div>
                <table class="w-full border border-gray-200 rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-sm font-medium">
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Offense Committed</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Penalty</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Status</th>
                            <th class="p-2 text-left border-b border-r border-gray-200">S.Y. Quarter</th>
                            <th class="p-2 text-left border-b border-r border-gray-200">Grade and Section</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Committed Date</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Recorded By</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Recorded Date</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Resolved By</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Resolved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="offense in submittedminorOffenses"
                            :key="offense.id"
                            class="hover:bg-gray-50 text-sm text-gray-800"
                        >
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.minor_offense }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.minor_penalty }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.sanction == 1 ? 'Resolved' : 'Unresolved' }}</td>
                            <td class="p-2 border-b border-r border-gray-200">{{ offense.student_schoolyear }}, {{ offense.student_quarter }}</td>
                            <td class="p-2 border-b border-r border-gray-200">Grade {{ offense.student_grade }}, {{ offense.student_section }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.committed_date }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.recorded_by}}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.recorded_date }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.cleansed_by}}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.cleansed_date }}</td>    
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Major Offense Section -->
            <div v-if="submittedmajorOffenses.length" class="bg-white p-4 rounded-lg shadow-lg">
                <div class="mb-4">
                    <h5 class="text-lg font-bold text-gray-800">Major Offense</h5>
                </div>
                <table class="w-full border border-gray-200 rounded-lg shadow">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-sm font-medium">
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Offense Committed</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Penalty</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Status</th>
                            <th class="p-2 text-left border-b border-r border-gray-200">S.Y. Quarter</th>
                            <th class="p-2 text-left border-b border-r border-gray-200">Grade and Section</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Committed Date</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Recorded By</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Recorded Date</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Resolved By</th>
                            <th class="py-2 px-4 text-left border-b border-r border-gray-200">Resolved Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="offense in submittedmajorOffenses"
                            :key="offense.id"
                            class="hover:bg-gray-50 text-sm text-gray-800"
                        >
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.major_offense }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.major_penalty }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.sanction == 1 ? 'Resolved' : 'Unresolved' }}</td>
                            <td class="p-2 border-b border-r border-gray-200">{{ offense.student_schoolyear }}, {{ offense.student_quarter }}</td>
                            <td class="p-2 border-b border-r border-gray-200">Grade {{ offense.student_grade }}, {{ offense.student_section }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.committed_date }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.recorded_by}}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.recorded_date }}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.cleansed_by}}</td>
                            <td class="py-2 px-4 border-b border-r border-gray-200">{{ offense.cleansed_date }}</td>   
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
