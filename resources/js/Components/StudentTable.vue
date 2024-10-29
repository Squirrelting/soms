<script setup>
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    students: Array,
});

</script>

<!-- StudentTable.vue -->
<template>
    <table class="w-full bg-white border border-gray-200 shadow mt-4 overflow-x-auto">
        <thead>
            <tr>
                <th class="hidden">ID</th>
                <th class="py-1 px-2 text-left border text-sm">No.</th>
                <th class="py-2 px-2 text-left border text-sm">LRN</th>
                <th class="py-2 px-2 text-left border text-sm">Student's Name</th>
                <th class="py-2 px-2 text-left border text-sm">Sex</th>
                <th class="py-2 px-2 text-left border text-sm">Grade</th>
                <th class="py-2 px-2 text-left border text-sm">Section</th>
                <th class="py-2 px-2 text-left border text-sm">Offenses/Penalties</th>
                <th class="py-2 px-2 text-left border text-sm">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(student, index) in students" :key="student.id">
                <td class="hidden">{{ student.id }}</td>
                <td class="py-2 px-4 text-left border text-sm">{{ index + 1 }}</td>
                <td class="py-2 px-2 border text-sm">{{ student.lrn }}</td>
                <td class="py-2 px-2 border text-sm">
                    {{ student.lastname }}, {{ student.firstname }} {{ student.middlename }}
                </td>
                <td class="py-2 px-2 border text-sm">{{ student.sex }}</td>
                <td class="py-2 px-2 border text-sm">Grade {{ student.grade?.grade ?? "N/A" }}</td>
                <td class="py-2 px-2 border text-sm">{{ student.section?.section ?? "N/A" }}</td>
                <td class="py-2 px-2 border text-sm">
                    <div class="flex justify-center items-center gap-4 relative">
                        <div class="relative">
                            <Link
                                :href="route('minor.offenses', student.id)"
                                class="px-2 py-0.5 bg-yellow-300 text-dark rounded"
                            >
                                Minor
                            </Link>
                            <span
                                v-if="student.submitted_minor_offenses_count > 0"
                                class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"
                            >
                                {{ student.submitted_minor_offenses_count }}
                            </span>
                        </div>
                        <div class="relative">
                            <Link
                                :href="route('major.offenses', student.id)"
                                class="px-2 py-0.5 bg-red-300 text-dark rounded"
                            >
                                Major
                            </Link>
                            <span
                                v-if="student.submitted_major_offenses_count > 0"
                                class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center"
                            >
                                {{ student.submitted_major_offenses_count }}
                            </span>
                        </div>
                    </div>
                </td>
                <td class="py-2 px-4 border text-sm">
                    <Link
                        :href="route('students.edit', student.id)"
                        class="px-2 py-1 bg-green-500 text-white p-3 rounded"
                    >Edit</Link>
                    <Link
                        :href="route('students.view', student.id)"
                        class="px-2 py-1 bg-blue-500 text-white p-3 rounded"
                    >View</Link>
                </td>
            </tr>
        </tbody>
    </table>
</template>


