<script setup>
import { ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    submittedminorOffenses: Array,
    submittedmajorOffenses: Array,
});

// Combine minor and major offenses into one array
const offensesData = ref([...props.submittedminorOffenses, ...props.submittedmajorOffenses]);
const gradeFilter = ref('');

// Computed property to filter offenses based on the selected grade (Minor or Major)
const filteredOffenses = computed(() => {
    if (!gradeFilter.value) {
        return offensesData.value;
    }
    return offensesData.value.filter(offense => offense.type === gradeFilter.value);
});
</script>
<template>
    <Head title="Offenders" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold text-gray-700">Offenders per sex List</h5>
                <div class="flex items-center">
                    <!-- Offense Dropdown -->
                    <select v-model="gradeFilter" class="ml-4 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">All Offenses</option>
                        <option value="Minor">Minor Offense</option>
                        <option value="Major">Major Offense</option>
                    </select>
                </div>
            </div>

            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">Offense Name</th>
                        <th class="py-2 px-4 text-left border">Male count</th>
                        <th class="py-2 px-4 text-left border">Female count</th>
                        <th class="py-2 px-4 text-left border">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="offense in filteredOffenses" :key="offense.id">
                        <td class="py-2 px-4 border">{{ offense.minor_offenses || offense.major_offenses }}</td> <!-- Offense name here -->
                        <td class="py-2 px-4 border">{{ offense.male_count }}</td>
                        <td class="py-2 px-4 border">{{ offense.female_count }}</td>
                        <td class="py-2 px-4 border">{{ offense.male_count + offense.female_count }}</td> <!-- Total -->
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :pagination="offensesData" />
    </AuthenticatedLayout>
</template>


