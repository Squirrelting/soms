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


const checkDataAndProceed = (action) => {
  if (props.student.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No offenses data",
      text: "There are no offenses data to export or print.",
    });
  } else {
    router.get(route("printrecord", { student: props.student.id }));
  }
};

</script>

<template>
    <Head title="Show Student" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex items-center justify-between mb-4">
    <!-- Student title -->
    <h5 class="text-xl font-semibold text-gray-700">Student</h5>

    <!-- Print and Back buttons -->
    <div class="flex space-x-2">
        <button 
            @click="checkDataAndProceed('print')" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300"
        >
            Print
        </button>
        <Link
            :href="route('students.index')"
            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300"
        >
            Back
        </Link>
    </div>
</div>


            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <div class="mb-3">
                        {{ student.lrn }}, 
                        {{ student.firstname }},
                        {{ student.middlename }},
                        {{ student.lastname }},
                        {{ student.sex }},
                        Grade {{ student.grade?.grade??'N/A' }},
                        {{ student.section?.section??'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Conditionally display Minor Offense table -->
            <div v-if="submittedminorOffenses.length" class="mt-4 mx-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Minor Offense</h5>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">
                                Offense Committed
                            </th>
                            <th class="py-2 px-4 text-left border">Penalty</th>
                            <th class="py-2 px-4 text-left border">Committed date</th>
                            <th class="py-2 px-4 text-left border">Recorded date</th>
                            <th class="py-2 px-4 text-left border">Cleansed date</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="offense in submittedminorOffenses"
                            :key="offense.id"
                        >
                            <td class="py-2 px-4 border">
                                {{ offense.minor_offense }}
                            </td>
                            <td class="py-2 px-4 border">
                                {{ offense.minor_penalty }}
                            </td>
                            <td class="py-2 px-4 border">{{ offense.committed_date }}</td>
                            <td class="py-2 px-4 border">{{ offense.recorded_date }}</td>
                            <td class="py-2 px-4 border">{{ offense.cleansed_date }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Conditionally display Major Offense table -->
            <div v-if="submittedmajorOffenses.length" class="mt-4 mx-4">
                <div class="flex justify-between">
                    <h5 class="m-4">Major Offense</h5>
                </div>
                <table class="w-full bg-white border border-gray-200 shadow">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 text-left border">
                                Offense Committed
                            </th>
                            <th class="py-2 px-4 text-left border">Penalty</th>
                            <th class="py-2 px-4 text-left border">Committed date</th>
                            <th class="py-2 px-4 text-left border">Recorded date</th>
                            <th class="py-2 px-4 text-left border">Cleansed date</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="offense in submittedmajorOffenses"
                            :key="offense.id"
                        >
                            <td class="py-2 px-4 border">
                                {{ offense.major_offense }}
                            </td>
                            <td class="py-2 px-4 border">
                                {{ offense.major_penalty }}
                            </td>
                            <td class="py-2 px-4 border">{{ offense.committed_date }}</td>
                            <td class="py-2 px-4 border">{{ offense.recorded_date }}</td>
                            <td class="py-2 px-4 border">{{ offense.cleansed_date }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
