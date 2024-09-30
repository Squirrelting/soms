<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";

const form = useForm({});
const props = defineProps({
    students: Object,
    search: String,
});

const searchQuery = ref(props.search || "");
const studentsData = ref(props.students);

// Watch for changes in the search input and trigger the search action
watch(searchQuery, (newSearch) => {
    router.get(route("students.index"), { search: newSearch }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            studentsData.value = page.props.students; // Update the students data
        }
    });
});

const DeleteStudent = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this student! This action cannot be undone.",
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
                text: "Please wait while we delete the student data.",
                didOpen: () => Swal.showLoading(),
            });

            router.delete(route("students.destroy", id), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => Swal.fire({
                    icon: "success",
                    title: "Deleted!",
                    text: "The student has been deleted.",
                    timer: 2000,
                    showConfirmButton: false,
                }),
                onError: () => Swal.fire({
                    icon: "error",
                    title: "Failed!",
                    text: "There was a problem deleting the student. Please try again.",
                }),
            });
        }
    });
};
</script>

<template>
    <Head title="Student" />

    <AuthenticatedLayout>
        <!-- message from StudentsController-->
        <div v-if="$page.props.flash.message" role="alert" class="alert alert-info mt-4 mx-5 px-4 py-2">
            <span>{{ $page.props.flash.message }}</span>
        </div>

        <!-- Student List with Search -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-xl font-semibold text-gray-700">Student List</h5>
                <div class="flex items-center">
                    <!-- Search Input -->
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search by name, LRN, or grade"
                        class="border border-gray-300 rounded-lg p-2 w-72 focus:outline-none focus:ring focus:border-blue-300"
                    />
                    <!-- Add Student Button -->
                    <Link
                        :href="route('students.create')"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg ml-4 transition ease-in-out duration-150"
                    >
                        Add Student
                    </Link>
                </div>
            </div>

            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">LRN</th>
                        <th class="py-2 px-4 text-left border">Name</th>
                        <th class="py-2 px-4 text-left border">Sex</th>
                        <th class="py-2 px-4 text-left border">Grade</th>
                        <th class="py-2 px-4 text-left border">Offenses/Penalties</th>
                        <th class="py-2 px-4 text-left border">Parent's Email</th>
                        <th class="py-2 px-4 text-left border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in studentsData.data" :key="student.id">
                        <td class="py-2 px-4 border">{{ student.lrn }}</td>
                        <td class="py-2 px-4 border">{{ student.name }}</td>
                        <td class="py-2 px-4 border">{{ student.sex }}</td>
                        <td class="py-2 px-4 border">Grade {{ student.grade }}</td>
                        <td class="py-2 px-4 border">
                            <Link :href="route('minor.offenses', student.id)" class="px-2 py-1 text-sm bg-yellow-300 text-dark p-3 rounded">
                                Minor
                            </Link>
                            <Link :href="route('major.offenses', student.id)" class="px-2 py-1 text-sm bg-red-300 text-dark p-3 rounded">
                                Major
                            </Link>
                        </td>
                        <td class="py-2 px-4 border">
                            <Link :href="route('students.show_email', student.id)" class="px-2 py-1 text-sm bg-blue-300 text-dark p-3 rounded">
                                {{ student.email }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('students.edit', student.id)" class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded">
                                Edit
                            </Link>
                            <Link :href="route('students.print', student.id)" class="px-2 py-1 text-sm bg-blue-500 text-white p-3 rounded">
                                Print
                            </Link>
                            <button @click="DeleteStudent(student.id)" class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :pagination="studentsData" />
    </AuthenticatedLayout>
</template>
