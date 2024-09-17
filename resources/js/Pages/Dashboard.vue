<script setup>
import { ref } from 'vue';
import CustomModal from '@/Components/CustomModal.vue';
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia'; // Use this to directly call Inertia actions


const props = defineProps({
    students: {
        type:Object,
    }
});

const form = useForm({});

//pagination
const paginationData = ref(props.students);

//Modal
const showModal = ref(false);
const passId = ref(null);

function openModal(id){
    passId.value = id;
    showModal.value = true;
}

const DeleteStudent = (id) => {
    Inertia.delete(route('students.destroy', id), {
        onSuccess: () => {
            Inertia.visit(window.location.href, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        onError: () => console.error('Failed to delete student'),
    });
}


</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- message from StudentsController-->
        <div v-if="$page.props.flash.message" class="alert bg-green-200 mt-4 mx-5 px-4 py-2">
        {{ $page.props.flash.message }}
      </div>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

            <!-- <div class="max-w-7xl mx-auto sm:px-3 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in!</div>
                </div>
            </div> -->


        <!-- Student List Table -->
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Student List</h5>
                <Link :href="route('students.create')" class="bg-blue-500 text-white p-3 rounded mb-4">Add Student</Link>
            </div>
            <table class="w-full bg-white border border-gray-200 shadow">
                <thead>
                    <tr>
                        <th class="py-2 px-4 text-left border">Id</th>
                        <th class="py-2 px-4 text-left border">Student ID</th>
                        <th class="py-2 px-4 text-left border">Name</th>
                        <th class="py-2 px-4 text-left border">Sex</th>
                        <th class="py-2 px-4 text-left border">Grade</th>
                        <th class="py-2 px-4 text-left border">Parent's Email</th>
                        <th class="py-2 px-4 text-left border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in students.data" :key="student.id">
                        <td class="py-2 px-4 border">{{ student.id }}</td>
                        <td class="py-2 px-4 border">{{ student.student_id }}</td>
                        <td class="py-2 px-4 border">{{ student.name }}, {{ student.sex }}</td>
                        <td class="py-2 px-4 border">{{ student.sex }}</td>
                        <td class="py-2 px-4 border">{{ student.grade }}</td>
                        <td class="py-2 px-4 border">{{ student.email }}</td>
                        <td>
                            <Link 
                                :href="route('students.show', student.id)" 
                                class="px-2 py-1 text-sm bg-blue-300 text-dark p-3 rounded me-2 inline-block"
                                >
                                Show
                            </Link>
                            <Link 
                                :href="route('students.edit', student.id)" 
                                class="px-2 py-1 text-sm bg-green-500 text-white p-3 rounded me-2 inline-block"
                                >
                                Edit
                            </Link>
                            <button 
                                @click="openModal(student.id)"
                                class="px-2 py-1 text-sm bg-red-600 text-white p-3 rounded me-2 inline-block"
                                >
                                Delete
                            </button>
                            <CustomModal v-if="showModal" @close="showModal=false">
                                <p class="text-gray-800">
                                     Are you sure you want to delete all this Student data? This action cannot be undone.
                                </p>
                                <div class="text-right mt-4">
                                    <button @click="showModal = false" class="px-4 py-2 text-sm text-black-800 focus:outline-none hover:underline">Cancel</button>
                                    <button @click="DeleteStudent(passId)" class="mr-2 px-4 py-2 text-sm rounded text-white bg-red-600 focus:outline-none hover:bg-red-500">Delete</button>
                                </div>
                            </CustomModal>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :pagination="paginationData"/>
    </AuthenticatedLayout>
</template>
