<script setup>
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";

const props = defineProps({
    students: {
        type: Object,
    },
});

const form = useForm({});

//pagination
const paginationData = ref(props.students);


const studentId = ref('');
const getId = (id) => {
    studentId.value = id;
};

const DeleteStudent = (id) => {
    router.delete(route("students.destroy", id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            alert("success");
        },
        onError: () => console.error("Failed to delete student"),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- message from StudentsController-->
        <div
        v-if="$page.props.flash.message"
        role="alert"
        class="alert alert-info mt-4 mx-5 px-4 py-2"
    >
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6 shrink-0 stroke-current"
        fill="none"
        viewBox="0 0 24 24"
    >
        <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
        />
    </svg>
    <span>{{ $page.props.flash.message }}</span>
    </div>

        <!-- Student List Table -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Student List</h5>
                <Link
                    :href="route('students.create')"
                    class="bg-blue-500 text-white p-3 rounded mb-4"
                    >Add Student</Link
                >
            </div>
            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">LRN</th>
                        <th class="py-2 px-4 text-left border">Name</th>
                        <th class="py-2 px-4 text-left border">Sex</th>
                        <th class="py-2 px-4 text-left border">Grade</th>
                        <th class="py-2 px-4 text-left border">
                            Offenses and Penalties
                        </th>
                        <th class="py-2 px-4 text-left border">
                            Parent's Email
                        </th>
                        <th class="py-2 px-4 text-left border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in students.data" :key="student.id">
                        <td class="py-2 px-4 border">{{ student.lrn }}</td>
                        <td class="py-2 px-4 border">
                            {{ student.name }}, {{ student.sex }}
                        </td>
                        <td class="py-2 px-4 border">{{ student.sex }}</td>
                        <td class="py-2 px-4 border">
                            Grade {{ student.grade }}
                        </td>
                        <td class="py-2 px-4 border">
                            <Link
                                :href="route('minor.offenses', student.id)"
                                class="px-2 py-1 text-sm bg-yellow-300 text-dark p-3 rounded me-2 inline-block"
                            >
                                Minor
                            </Link>
                            <Link
                                :href="route('major.offenses', student.id)"
                                class="px-2 py-1 text-sm bg-red-300 text-dark p-3 rounded me-2 inline-block"
                            >
                                Major
                            </Link>
                        </td>
                        <td class="py-2 px-4 border">
                            <Link
                                :href="route('students.show_email', student.id)"
                                class="px-2 py-1 text-sm bg-blue-300 text-dark p-3 rounded me-2 inline-block"
                            >
                                {{ student.email }}
                            </Link>
                        </td>

                        <td>
                            <Link
                                :href="route('students.edit', student.id)"
                                class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded me-2 inline-block"
                            >
                                Edit
                            </Link>
                            <Link
                                :href="route('students.print', student.id)"
                                class="px-2 py-1 text-sm bg-blue-500 text-white p-3 rounded me-2 inline-block"
                            >
                                Print
                            </Link>
                            <button
                                @click="getId(student.id)"
                                onclick="my_modal_1.showModal()"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block"
                            >
                                Delete
                            </button>

                            <!-- Open the modal using ID.showModal() method -->

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :pagination="paginationData" />

        <dialog id="my_modal_1" class="modal">
                                <div class="modal-box">
                                    <h3 class="text-lg font-bold">Hello!</h3>
                                    <p class="py-4">
                                        Are you sure you want to delete all this
                                        Student data? This action cannot be
                                        undone.
                                    </p>
                                    <div class="modal-action">
                                        <form method="dialog">
                                            <!-- if there is a button in form, it will close the modal -->
                                            <button class="btn">Cancel</button>
                                            <button
                                                @click="DeleteStudent(studentId)"
                                                class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-600 focus:outline-none hover:bg-red-500"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
    </AuthenticatedLayout>
</template>
