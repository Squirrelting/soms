<script setup>
import { ref, computed } from "vue";
import { Head, router, Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { CalendarDaysIcon } from "@heroicons/vue/24/solid"; // Heroicons
import Swal from "sweetalert2";

const props = defineProps({
    offensesData: Array,
});

// Combine minor and major offenses into one array
const offenseFilter = ref("");

// Default date values
const today = new Date();
const lastMonth = new Date();
lastMonth.setMonth(today.getMonth() - 1);

const maxDate = today.toISOString().split('T')[0];

// Reactive start and end dates
const startDate = ref(lastMonth.toISOString().split("T")[0]);
const endDate = ref(today.toISOString().split("T")[0]);

// Refs for date inputs
const startDateInput = ref(null);
const endDateInput = ref(null);

// Methods to show the date pickers
const showStartDatePicker = () => {
  startDateInput.value?.showPicker();
};
const showEndDatePicker = () => {
  endDateInput.value?.showPicker();
};

// Computed properties for formatted dates
const formattedStartDate = computed(() =>
  new Date(startDate.value).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  })
);
const formattedEndDate = computed(() =>
  new Date(endDate.value).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  })
);

// Method for filtering data
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

// Computed property for print URL
const exportExcel = computed(() => {
  return route("exportexcel", {
    offenseFilter: offenseFilter.value,
    startDate: startDate.value,
    endDate: endDate.value,
  });
});

// Check if there is data, if not, show SweetAlert and prevent navigation
const checkDataAndProceed = (action) => {
  if (props.offensesData.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "No offenses data",
      text: "There are no offenses data to export or print.",
    });
  } else {
    // Perform the action (either export or print)
    if (action === "print") {
      window.open(printUrl.value, "_blank");
    } else if (action === "export") {
      window.location.href = exportExcel.value;
    }
  }
};
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
  
          <!-- Start Date Picker with formatted display -->
          <div class="ml-4 flex items-center">
            <label for="startDate" class="mr-2">Start Date:</label>
            <button @click="showStartDatePicker" class="calendar-button">
              <CalendarDaysIcon class="h-6 w-6 text-gray-500" />
            </button>
            <input
              type="date"
              id="startDate"
              v-model="startDate"
              ref="startDateInput"
              :max="maxDate"
              @change="filter"
              class="invisible-input"
            />
            <span>{{ formattedStartDate }}</span>
          </div>
  
          <!-- End Date Picker with formatted display -->
          <div class="ml-4 flex items-center">
            <label for="endDate" class="mr-2">End Date:</label>
            <button @click="showEndDatePicker" class="calendar-button">
              <CalendarDaysIcon class="h-6 w-6 text-gray-500" />
            </button>
            <input
              type="date"
              id="endDate"
              v-model="endDate"
              ref="endDateInput"
              :max="maxDate"
              @change="filter"
              class="invisible-input"
            />
            <span>{{ formattedEndDate }}</span>
          </div>
  
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
                {{ offense.offense_name }}              
              </td>
              <td class="py-2 px-4 border">{{ offense.male_count }}</td>
              <td class="py-2 px-4 border">{{ offense.female_count }}</td>
              <td class="py-2 px-4 border">{{ parseInt(offense.male_count)  + parseInt(offense.female_count) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <Pagination :pagination="offensesData" />
    </AuthenticatedLayout>
  </template>
<style scoped>
.calendar-button {
  background: none;
  border: none;
  cursor: pointer;
  margin-right: 8px;
}

.invisible-input {
  opacity: 0;
  position: absolute;
  z-index: -1;
  pointer-events: none;
}

span {
  margin-left: 8px;
  font-size: 1rem;
  color: #333;
}
</style>
  
