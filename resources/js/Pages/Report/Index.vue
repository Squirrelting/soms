<script setup>
import { ref, computed, watch } from "vue";
import { Head, router, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  offensesData: Array,
  grade: String,
  grades: Array,
  section: String,
});

// Define refs for filters
const offenseFilter = ref("");
const sex = ref("");
const gradeFilter = ref(props.grade || "");
const sectionFilter = ref(props.section || "");
const sections = ref([]); 



// Filter function
const filter = () => {
  router.get(route("reports.index"), {
    offenseFilter: offenseFilter.value,
    sex: sex.value,
    grade: gradeFilter.value, // Ensure grade is defined as gradeFilter
    section: sectionFilter.value,

  }, { preserveScroll: true, preserveState: true });
};


// Watch for changes in filters and trigger the filter method
watch(
    [offenseFilter, gradeFilter, sectionFilter, sex],
    () => {
        filter();
    }
);
// Fetch sections based on selected grade
const fetchSections = async (gradeId) => {
  try {
    const response = await axios.get(`/students/sections?grade_id=${gradeId}`);
    sections.value = response.data.sections; // Populate the sections dropdown
    sectionFilter.value = ""; // Reset section filter when grade changes
  } catch (error) {
    console.error("Error fetching sections:", error);
  }
};

// Watch for grade changes and fetch sections when the grade changes
watch(gradeFilter, (newGrade) => {
  if (newGrade) {
    fetchSections(newGrade); 
  } else {
    sections.value = []; 
  }
});
</script>

<template>
<Head title="Reports" />
<AuthenticatedLayout>
  <div>
    <div class="flex justify-between mb-4 mt-4 mx-4">
      <h5 class="text-xl font-semibold">Offenders List</h5>

      <!-- Filters -->
      <div class="flex justify-between items-center mb-4">
        <!-- Offense Filter -->
        <select v-model="offenseFilter"  class="pl-2 border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300">
          <option value="">All Offenses</option>
          <option value="Minor">Minor Offense</option>
          <option value="Major">Major Offense</option>
        </select>

        <!-- Gender Filter -->
        <select v-model="sex"  class="pl-2 border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300">
          <option value="">All Genders</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>

        <!-- Grade Dropdown -->
        <select
          v-model="gradeFilter"
          class="pl-2 border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
        >
          <option value="">All Grades</option>
          <option v-for="grade in grades" :key="grade.id" :value="grade.id">Grade {{ grade.grade }}</option>
        </select>

        <!-- Section Dropdown -->
        <select
          :disabled="gradeFilter == ''"
          v-model="sectionFilter"
          class="border border-gray-300 rounded-lg p-1 text-sm focus:outline-none focus:ring focus:border-blue-300"
        >
          <option value="">All Sections</option>
          <option
            v-for="section in sections"
            :key="section.id"
            :value="section.section"
          >
            {{ section.section }}
          </option>
        </select>

                <!-- Print and Export buttons -->
                <button @click="checkDataAndProceed('print')" class="bg-blue-500 text-white py-2 px-5 rounded">
          Print
        </button>
        <button @click="checkDataAndProceed('export')" class="bg-green-500 text-white py-2 px-5 rounded">
          Export to Excel
        </button>


          <div class="flex items-center">
            <Link :href="route('dashboard')" class="bg-red-600 text-white py-2 px-5 inline-block rounded">
              Back
            </Link>
          </div>
      </div>
    </div>

    <!-- Offenders Table -->
<table class="w-full bg-white border shadow">
  <thead>
    <tr>
      <th class="py-1 px-2 border text-sm" >No.</th>
      <th class="py-1 px-2 border text-sm">LRN</th>
      <th class="py-1 px-2 border text-sm">Name</th>
      <th class="py-1 px-2 border text-sm">Sex</th>
      <th class="py-1 px-2 border text-sm">Grade</th>
      <th class="py-1 px-2 border text-sm">Section</th>
      <th class="py-1 px-2 border text-sm">Offense</th>
      <th class="py-1 px-2 border text-sm">Date Committed</th>
      <th class="py-1 px-2 border text-sm">Date Recorded</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="(offense, index) in offensesData" :key="offense.id">
      <td class="py-1 px-2 border text-sm">{{ index + 1 }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.lrn }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.student_lastname }}, {{ offense.student_firstname }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.student_sex }}</td>
      <td class="py-1 px-2 border text-sm">Grade {{ offense.student_grade }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.student_section }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.minor_offense ? offense.minor_offense : offense.major_offense }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.committed_date }}</td>
      <td class="py-1 px-2 border text-sm">{{ offense.recorded_date }}</td>
    </tr>
  </tbody>
</table>


    <Pagination :pagination="offensesData" />
  </div>
</AuthenticatedLayout>
</template>