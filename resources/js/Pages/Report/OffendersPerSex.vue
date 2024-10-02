<script setup>
import { ref, computed } from "vue";
import { Head, router, Link, useForm } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    offensesData: Array,
});

// Combine minor and major offenses into one array
const offenseFilter = ref("");

// Default date values
const today = new Date();
const lastMonth = new Date();
lastMonth.setMonth(today.getMonth() - 1);

// Reactive start and end dates
const startDate = ref(lastMonth.toISOString().split("T")[0]);
const endDate = ref(today.toISOString().split("T")[0]);

const filter = () => {
    router.get(
        route("offenders.index"),
        {
            offenseFilter: offenseFilter.value,
            startDate: startDate.value,
            endDate: endDate.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

// Computed property for print URL
const printUrl = computed(() => {
    return route("printoffenders", {
        offenseFilter: offenseFilter.value,
        startDate: startDate.value,
        endDate: endDate.value,
    });
});
</script>

<template>
    <Head title="Offenders" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold text-gray-700">
                    Offenders per sex List
                </h5>

                <!-- Offense Dropdown -->
                <select
                    @change="filter"
                    v-model="offenseFilter"
                    class="border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:border-blue-300"
                >
                    <option value="">All Offenses</option>
                    <option value="Minor">Minor Offense</option>
                    <option value="Major">Major Offense</option>
                </select>

                <div class="ml-4">
                        <label for="startDate" class="mr-2">Start Date:</label>
                        <input
                            @change="filter"
                            type="date"
                            id="startDate"
                            v-model="startDate"
                            class="form-input"
                        />
                    </div>
                    <div class="ml-4">
                        <label for="endDate" class="mr-2">End Date:</label>
                        <input
                            @change="filter"
                            type="date"
                            id="endDate"
                            v-model="endDate"
                            class="form-input"
                        />
                    </div>
                    <a
        :href="printUrl"
        target="_blank"
        class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
    >
        Print
    </a>
                <div class="flex items-center">
                    <Link
                    :href="route('dashboard')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded"
                    >Back
                    </Link>
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
                    <tr v-for="offense in offensesData" :key="offense.id">
                        <td class="py-2 px-4 border">
                            {{
                                offense.minor_offenses || offense.major_offenses
                            }}
                        </td>
                        <td class="py-2 px-4 border">
                            {{ offense.male_count }}
                        </td>
                        <td class="py-2 px-4 border">
                            {{ offense.female_count }}
                        </td>
                        <td class="py-2 px-4 border">
                            {{ offense.male_count + offense.female_count }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :pagination="offensesData" />
    </AuthenticatedLayout>
</template>
