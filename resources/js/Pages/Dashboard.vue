<script setup>
import { ref, computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PieChart from "@/Components/PieChart.vue";
import LineChart from "@/Components/LineChart.vue";
import BarGraph from "@/Components/BarGraph.vue";

const props = defineProps({
    students: Object,
    schoolYears: Array,
});
const studentsData = ref(props.students);

const selectedYear = ref(props.schoolYears[0].student_schoolyear) || "";
const selectedQuarter = ref(props.schoolYears[0].quarters[0]) || "";

const filteredQuarters = computed(() => {
    const selectedSchoolYear = props.schoolYears.find(
        (year) => year.student_schoolyear === selectedYear.value
    );
    return selectedSchoolYear ? selectedSchoolYear.quarters : [];
});

const filterQuarters = () => {
    selectedQuarter.value = "";
};
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="m-4 flex flex-col space-y-4">
            <div class="flex space-x-2">
                <select
                    v-model="selectedYear"
                    @change="filterQuarters"
                    class="border border-gray-300 rounded-lg p-1 text-xs focus:outline-none focus:ring focus:border-blue-300"
                >
                    <option
                        v-for="(schoolyear, index) in props.schoolYears"
                        :key="index"
                        :value="schoolyear.student_schoolyear"
                    >
                        {{ schoolyear.student_schoolyear }}
                    </option>
                </select>

                <!-- Quarters Select -->
                <select
                    v-model="selectedQuarter"
                    class="border border-gray-300 rounded-lg p-1 text-xs focus:outline-none focus:ring focus:border-blue-300"
                >
                    <option value="">Select Quarter</option>
                    <option
                        v-for="(quarter, index) in filteredQuarters"
                        :key="index"
                    >
                        {{ quarter }}
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Bar Graph -->
                <div
                    class="flex flex-col bg-gray-100 text-gray-800 shadow-md border rounded-lg p-2 mb-4"
                >
                    <div class="bg-orange-600 text-white p-3 rounded-t-lg">
                        <h3 class="text-sm font-bold flex items-center">
                            <i class="fas fa-chart-bar mr-1"></i> Bar Graph for
                            Top 5 Committed Offenses
                        </h3>
                    </div>
                    <div
                        class="bg-white rounded-b-lg flex-grow p-2"
                        style="height: 200px"
                    >
                        <BarGraph
                            :selectedYear="selectedYear"
                            :selectedQuarter="selectedQuarter"
                        />
                    </div>
                </div>

                <!-- Line Chart -->
                <div
                    class="flex flex-col bg-gray-100 text-gray-800 shadow-md border rounded-lg p-2 mb-4"
                >
                    <div class="bg-green-600 text-white p-3 rounded-t-lg">
                        <h3 class="text-sm font-bold flex items-center">
                            <i class="fas fa-chart-line mr-1"></i> Line Chart
                            for Trends
                        </h3>
                    </div>
                    <div
                        class="bg-white rounded-b-lg flex-grow p-2"
                        style="height: 200px"
                    >
                        <LineChart
                            :selectedYear="selectedYear"
                            :selectedQuarter="selectedQuarter"
                        />
                    </div>
                </div>

                <!-- Pie Chart -->
                <div
                    class="flex flex-col bg-gray-100 text-gray-800 shadow-md border rounded-lg p-2"
                >
                    <div class="bg-blue-600 text-white p-3 rounded-t-lg">
                        <h3 class="text-sm font-bold flex items-center">
                            <i class="fas fa-chart-pie mr-1"></i> Pie Chart for
                            Sex Offenders
                        </h3>
                    </div>
                    <div
                        class="bg-white rounded-b-lg flex-grow p-2"
                        style="height: 200px"
                    >
                        <PieChart
                            :selectedYear="selectedYear"
                            :selectedQuarter="selectedQuarter"
                        />
                    </div>
                </div>
            </div>
        </div>

        <table class="w-full bg-white border border-gray-200 shadow">
            <thead>
                <tr>
                    <th class="hidden" @click="sortTable('id')">ID</th>

                    <th
                        class="py-1 px-2 text-left border cursor-pointer text-sm"
                    >
                        No.
                    </th>

                    <th
                        class="py-2 px-2 text-left border cursor-pointer text-sm"
                    >
                        LRN
                    </th>

                    <th
                        class="py-2 px-2 text-left border cursor-pointer text-sm"
                    >
                        Student's Name
                    </th>

                    <th
                        class="py-2 px-2 text-left border cursor-pointer text-sm"
                    >
                        Sex
                    </th>

                    <th
                        class="py-2 px-2 text-left border cursor-pointer text-sm"
                    >
                        Grade
                    </th>

                    <th
                        class="py-2 px-2 text-left border cursor-pointer text-sm"
                    >
                        Section
                    </th>

                    <th
                        class="py-2 px-2 text-left border cursor-pointer text-sm"
                    >
                        Parent's Email
                    </th>

                    <th class="py-2 px-2 text-left border text-sm">
                        Offenses/Penalties
                    </th>

                    <th class="py-2 px-2 text-left border text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(student, index) in studentsData.data"
                    :key="student.id"
                >
                    <td class="hidden">{{ student.id }}</td>
                    <td class="py-2 px-4 text-left border text-sm">
                        {{ index + 1 }}
                    </td>
                    <td class="py-2 px-2 border text-sm">{{ student.lrn }}</td>
                    <td class="py-2 px-2 border text-sm">
                        {{ student.lastname }}, {{ student.firstname }}
                        {{ student.middlename }}
                    </td>
                    <td class="py-2 px-2 border text-sm">{{ student.sex }}</td>
                    <td class="py-2 px-2 border text-sm">
                        Grade {{ student.grade?.grade ?? "N/A" }}
                    </td>
                    <td class="py-2 px-2 border text-sm">
                        {{ student.section?.section ?? "N/A" }}
                    </td>

                    <td class="py-2 px-4 border text-sm">
                        <div
                            class="px-2 py-1 text-sm bg-blue-200 text-dark p-3 rounded"
                        >
                            {{ student.email }}
                        </div>
                    </td>

                    <td class="py-2 px-2 border text-sm">
                        <div
                            class="flex justify-center items-center gap-4 relative"
                        >
                            <!-- Minor offenses button -->
                            <div class="relative">
                                <Link
                                    :href="route('minor.offenses', student.id)"
                                    class="px-2 py-0.5 text-s bg-yellow-300 text-dark rounded"
                                >
                                    Minor
                                </Link>
                                <!-- Badge for minor offenses -->
                                <span
                                    v-if="
                                        student.submitted_minor_offenses_count >
                                        0
                                    "
                                    class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-s rounded-full h-4 w-4 flex items-center justify-center"
                                >
                                    {{ student.submitted_minor_offenses_count }}
                                </span>
                            </div>

                            <!-- Major offenses button -->
                            <div class="relative">
                                <Link
                                    :href="route('major.offenses', student.id)"
                                    class="px-2 py-0.5 text-s bg-red-300 text-dark rounded"
                                >
                                    Major
                                </Link>
                                <!-- Badge for major offenses -->
                                <span
                                    v-if="
                                        student.submitted_major_offenses_count >
                                        0
                                    "
                                    class="absolute top-0 right-0 translate-x-2 -translate-y-2 bg-red-500 text-white text-s rounded-full h-4 w-4 flex items-center justify-center"
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
                            >Edit</Link
                        >
                        <Link
                            :href="route('students.view', student.id)"
                            class="px-2 py-1 bg-blue-500 text-white p-3 rounded"
                            >View</Link
                        >
                    </td>
                </tr>
            </tbody>
        </table>
    </AuthenticatedLayout>
</template>
