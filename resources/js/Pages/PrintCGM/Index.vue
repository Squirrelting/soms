<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { defineProps, watch, ref, onMounted  } from 'vue';
import Pagination from "@/Components/Pagination.vue";
import Swal from 'sweetalert2'; 

const props = defineProps({
    errors: Object,
    signatory: Array, 
    students: Object,
    perPage: Number,
});

const form = useForm({
    lrn: "",
    firstname: "",
    middlename: "",
    lastname: "",
    signatory: '',
});

const perPage = ref(props.perPage || 10);
const searchQuery = ref("");
const studentsData = ref(props.students);
const sortColumn = ref("updated_at");
const sortOrder = ref("desc");

const sortTable = (column) => {
    if (sortColumn.value === column) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortColumn.value = column;
        sortOrder.value = "asc"; 
    }
    filter(); 
};

const isLoading = ref(false);

const filter = () => {
    isLoading.value = true;

    router.get(
        route("print.index"),
        {
            search: searchQuery.value,
            sortColumn: sortColumn.value,
            sortOrder: sortOrder.value,
            perPage: perPage.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                studentsData.value = page.props.students;
                isLoading.value = false;

            },
        }
    );
};

watch(
    [searchQuery],
    () => {
        filter();
    }
);

onMounted(() => {
    filter(); // Reload filter when component is mounted
});

const handlePrint = () => {
    // Validation for required fields
    if (!form.firstname || !form.lastname || !form.signatory) {
        Swal.fire({
            icon: 'error',
            title: 'Incomplete Information',
            text: 'Please fill in the student\'s first name, last name, and select a signatory before printing the certificate.',
        });
        return; // Stop execution if validation fails
    }

    // Confirmation dialog before proceeding
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to generate a Good Moral certificate for this student?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, generate',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state while saving
            Swal.fire({
                title: "Saving...",
                text: "Please wait while we save and send the student data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Store form data to use after reset
            const studentData = {
                firstname: form.firstname,
                middlename: form.middlename,
                lastname: form.lastname,
                signatory: form.signatory
            };

            // Send the form data
            form.post(route("print.store"), {
                onSuccess: () => {
                    // Success notification
                    Swal.fire({
                        icon: "success",
                        title: "Detail Saved",
                        text: "The student's data has been successfully added!",
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    // Reset the form
                    form.reset();

                    // Use stored student data to construct the print certificate URL
                    const url = `/print/print-certificate/${studentData.signatory}/${encodeURIComponent(studentData.firstname)}/${studentData.middlename ? encodeURIComponent(studentData.middlename) : ' '}/${encodeURIComponent(studentData.lastname)}`;

                    // Open in a new tab
                    const printWindow = window.open(url, '_blank');
                    if (!printWindow) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Open New Tab',
                            text: 'Please make sure your browser allows pop-ups for this site.',
                        });
                    }
                },
                onError: () => {
                    // Error notification
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem saving the student. Please try again.",
                    });
                },
            });
        }
    });
};

const formatName = (name) => {
    return name.charAt(0).toUpperCase() + name.slice(1).toLowerCase();
};

const DeleteData = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this Data! This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Deleting...",
                text: "Please wait while we delete the Data .",
                didOpen: () => Swal.showLoading(),
            });

            router.delete(route("print.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    studentsData.value.data = studentsData.value.data.filter(
                        (print_cgm) => print_cgm.id !== id
                    );

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The Data has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => Swal.fire({
                    icon: "error",
                    title: "Failed!",
                    text: "There was a problem deleting the Data. Please try again.",
                }),
            });
        }
    });
};
</script>

<template>
    <Head title="Print" />
    <AuthenticatedLayout>
        <div class="mt-4 mx-4">

            <div class="bg-white p-6 rounded-lg shadow-lg space-y-6">
  <form @submit.prevent="handlePrint">
    <!-- Form Title -->
    <h2 class="text-lg font-bold text-gray-800">Student Details Form</h2>
    
    <!-- Form Grid -->
    <div class="grid grid-cols-12 gap-6">
      <!-- LRN Input -->
      <div class="col-span-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Student's LRN
        </label>
        <input
          type="text"
          v-model="form.lrn"
          class="py-1 w-full bg-gray-200 border border-gray-500 rounded"        />
        <div v-if="errors.lrn" class="text-sm text-red-500 mt-1">
          {{ errors.lrn }}
        </div>
      </div>

            <!-- First Name Input -->
        <div class="col-span-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Student's First Name
        </label>
        <input
          type="text"
          v-model="form.firstname"
          @input="form.firstname = formatName(form.firstname)"
          class="py-1 w-full bg-gray-200 border border-gray-500 rounded"        />
        <div v-if="errors.firstname" class="text-sm text-red-500 mt-1">
          {{ errors.firstname }}
        </div>
      </div>

      <!-- Middle Name Input -->
      <div class="col-span-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Student's Middle Name (Optional)
        </label>
        <input
          type="text"
          v-model="form.middlename"
          @input="form.middlename = formatName(form.middlename)"
          class="py-1 w-full bg-gray-200 border border-gray-500 rounded"        />
        <div v-if="errors.middlename" class="text-sm text-red-500 mt-1">
          {{ errors.middlename }}
        </div>
      </div>

      <!-- Last Name Input -->
      <div class="col-span-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Student's Last Name
        </label>
        <input
          type="text"
          v-model="form.lastname"
          @input="form.lastname = formatName(form.lastname)"
          class="py-1 w-full bg-gray-200 border border-gray-500 rounded"        />
        <div v-if="errors.lastname" class="text-sm text-red-500 mt-1">
          {{ errors.lastname }}
        </div>
      </div>

      <!-- Signatory Dropdown -->
      <div class="col-span-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Select Signatory
        </label>
        <select
          v-model="form.signatory"
          class="py-1 w-full bg-gray-200 border border-gray-500 rounded"        >
          <option value="">Select Signatory</option>
          <option v-for="sign in signatory" :key="sign.id" :value="sign.id">
            {{ sign.name }}, {{ sign.position }}
          </option>
        </select>
        <div v-if="errors.signatory" class="text-sm text-red-500 mt-1">
          {{ errors.signatory }}
        </div>
      </div>

      <!-- Submit Button -->
      <div class="col-span-4 flex items-end">
        <button
          type="button"
          @click="handlePrint"
          class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded shadow-lg transition-all duration-200"
        >
          Print
        </button>
      </div>
    </div>
  </form>
</div>


    
    <span v-if="isLoading" class="loading loading-spinner loading-lg"></span>

<div class="mt-4 bg-white py-2 px-2 rounded-lg shadow-lg space-y-4">
        <div class="flex justify-between items-center mb-2 space-x-2">

<div class="flex items-center space-x-2">
<select
id="perPage"
v-model="perPage"
@change="filter"
class="select select-xs text-xs py-1 px-1 w-[4rem] h-8 focus:outline-none border-gray-500 focus:ring focus:border-blue-200 focus:ring-blue-200"
>
<option value="5">5</option>
<option value="10">10</option>
<option value="15">15</option>
<option value="20">20</option>
<option value="25">25</option>
</select>

<!-- Label next to the select dropdown -->
<span class="text-xs">Entries per page</span>
</div>

<!-- Search Input -->
<div class="relative w-full max-w-xs">
<input
v-model="searchQuery"
type="text"
placeholder="Search"
class="input rounded-lg text-sm h-10 pl-9 pr-3 w-full border-blue-200 ring ring-blue-200 focus:border-blue-300 focus:ring focus:ring-blue-300 focus:outline-none"
/>
<svg
xmlns="http://www.w3.org/2000/svg"
viewBox="0 0 16 16"
fill="currentColor"
class="absolute left-2 top-1/2 transform -translate-y-1/2 h-5 w-5  opacity-70 pointer-events-none"
>
<path
    fill-rule="evenodd"
    d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
    clip-rule="evenodd"
/>
</svg>
</div>
</div>
<table class="w-full bg-white border border-gray-200 shadow">

    <thead>
        <tr>
            <th class="hidden" @click="sortTable('updated_at')">
                updated_at
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'updated_at' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'updated_at' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>

            <th
                class="py-1 px-2 text-left border cursor-pointer text-sm"
            >
                No.
            </th>

            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
                @click="sortTable('lrn')"
            >
                LRN
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'lrn' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'lrn' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>
            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
                @click="sortTable('firstname')"
            >
                First Name
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'firstname' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'firstname' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>
            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
                @click="sortTable('middlename')"
            >
                Middle Name
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'middlename' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'middlename' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>
            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
                @click="sortTable('lastname')"
            >
                Last Name
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'lastname' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'lastname' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>

            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
                @click="sortTable('generated_by')"
            >
                Generated By
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'generated_by' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'generated_by' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>

            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
                @click="sortTable('created_at')"
            >
                Generated Date
                <span class="ml-1 text-[8px]">
                    <span
                        :class="
                            sortColumn === 'created_at' &&
                            sortOrder === 'asc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▲</span
                    >
                    <span
                        :class="
                            sortColumn === 'created_at' &&
                            sortOrder === 'desc'
                                ? 'text-black'
                                : 'text-gray-400'
                        "
                        >▼</span
                    >
                </span>
            </th>

            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
            >
                Signatory
            </th>

            <th
                class="py-2 px-2 text-left border cursor-pointer text-sm"
            >
                Signatory Position
            </th>



            <th class="py-2 px-2 text-left border text-sm">
                Actions
            </th>
        </tr>
    </thead>
    <tbody>
            <!-- Check if studentsData is empty -->
    <tr v-if="!studentsData.data || studentsData.data.length === 0">
        <td
            class="py-2 px-2 text-center border text-sm"
            colspan="10" 
        >
            No data available.
        </td>
    </tr>
        <tr
            v-for="(student, index) in studentsData.data"
            :key="student.id"
        >
            <td class="hidden">{{ student.updated_at }}</td>
            <td class="py-2 px-4 text-left border text-sm">
                {{ parseInt(index) + 1 }}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.lrn }}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.firstname }}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.middlename }}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.lastname }}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.generated_by}}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.created_at}}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.signatory}}
            </td>
            <td class="py-2 px-2 border text-sm">
                {{ student.position}}
            </td>

            <td class="py-2 px-4 border text-sm">

                <!-- <Link
        :href="route('print.view', student.id)"
        class="px-2 py-1 text-sm bg-blue-500 text-white rounded"
    >
        View
    </Link> -->
                <button
                    @click="DeleteData(student.id)"
                    class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded"
                            >
                                Delete
                            </button>
            </td>
        </tr>
    </tbody>
</table>
<Pagination :pagination="studentsData" />
</div>
    </div>
    </AuthenticatedLayout>
</template>