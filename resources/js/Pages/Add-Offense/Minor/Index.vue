<script setup>
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

// Initialize the reactive variables
const props = defineProps({
    minorOffense: Object,
});

const searchQuery = ref("");
const minorData = ref(props.minorOffense);

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
const filter = () => {
    router.get(
        route("minoroffense.index"),
        {
            search: searchQuery.value,
            sortColumn: sortColumn.value, 
            sortOrder: sortOrder.value,    
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                minorData.value = page.props.minorOffense;
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

const DeleteMinorOffense = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this minor offense! This action cannot be undone.",
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
                text: "Please wait while we delete the minor offense data.",
                didOpen: () => Swal.showLoading(),
            });

            router.delete(route("minoroffense.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    minorData.value.data = minorData.value.data.filter(
                        (minor_offenses) => minor_offenses.id !== id
                    );

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "The minor offense has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => Swal.fire({
                    icon: "error",
                    title: "Failed!",
                    text: "There was a problem deleting the minor offense. Please try again.",
                }),
            });
        }
    });
};
</script>

<template>
    <Head title="Minor Offenses" />

    <AuthenticatedLayout>
        <div
            v-if="$page.props.flash.message"
            role="alert"
            class="alert alert-info mx-5 px-4 py-2"
        >
            <span>{{ $page.props.flash.message }}</span>
        </div>

        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold text-gray-700">
                    Minor Offenses Table
                </h5>

                <div class="flex items-center">
                    <!-- Search Input -->
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by offense name"
                        class="border border-gray-300 rounded-lg p-2 w-72 focus:outline-none focus:ring focus:border-blue-300"
                    />

                    <!-- Add minor offense Button -->
                    <Link
                        :href="route('minoroffense.create')"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg ml-4 transition ease-in-out duration-150"
                    >
                        Add Minor Offense
                    </Link>
                </div>
            </div>

            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="hidden" @click="sortTable('updated_at')">
                            updated_at
                            <span class="ml-1 text-[8px]">
                                <span :class="sortColumn === 'updated_at' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                                <span :class="sortColumn === 'updated_at' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                            </span>
                        </th>                        
                        <th class="py-1 px-2 text-left border">No.</th>
                        <th class="py-2 px-2 text-left border cursor-pointer text-sm" @click="sortTable('minor_offenses')">
                        Minor Offense
                        <span class="ml-1 text-[8px]">
                            <span :class="sortColumn === 'minor_offenses' && sortOrder === 'asc' ? 'text-black' : 'text-gray-400'">▲</span>
                            <span :class="sortColumn === 'minor_offenses' && sortOrder === 'desc' ? 'text-black' : 'text-gray-400'">▼</span>
                        </span>
                        </th>
                        <th class="py-2 px-4 text-left border">Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(offense, index) in minorData.data" :key="offense.id">
                        <td class="hidden">{{ offense.updated_at }}</td>
                        <td class="py-2 px-4 text-left border text-sm">{{ index + 1 }}</td>
                        <td class="py-2 px-4 border text-sm">{{ offense.minor_offenses }}</td>
                        <td class="py-2 px-4 border text-sm">
                            <Link
                                :href="route('minoroffense.edit', offense.id)"
                                class="px-2 py-1 text-sm bg-green-500 text-white rounded"
                            >
                                Edit
                            </Link>
                            <button
                                @click="DeleteMinorOffense(offense.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination Component -->
            <Pagination :pagination="minorData" />
        </div>
    </AuthenticatedLayout>
</template>
