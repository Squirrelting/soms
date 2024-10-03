<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({
    errors: Object,
});

const form = useForm({
    lrn: "",
    name: "",
    sex: "",
    grade: "",
    email: "",
});

const saveStudent = () => {
    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to add this student!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Saving...",
                text: "Please wait while we save the student.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            // Proceed with form submission if confirmed
            form.post(route("students.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Student Added",
                        text: "The student has been successfully added!",
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    form.reset();
                },
                onError: () => {
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
</script>

<template>
    <Head title="Input Student" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Input Student</h5>
                <Link
                    :href="route('students.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link>
            </div>

            <form @submit.prevent="saveStudent()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>LRN</label>
                            <input
                                type="text"
                                v-model="form.lrn"
                                class="py-1 w-full"
                            />
                            <div v-if="errors.lrn" class="text-red-500">
                                {{ errors.lrn }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Student Name</label>
                            <input
                                type="text"
                                v-model="form.name"
                                class="py-1 w-full"
                            />
                            <div v-if="errors.name" class="text-red-500">
                                {{ errors.name }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Student Sex</label>
                            <select v-model="form.sex" class="py-1 w-full">
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <div v-if="errors.sex" class="text-red-500">
                                {{ errors.sex }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Student's Grade</label>
                            <input
                                type="text"
                                v-model="form.grade"
                                class="py-1 w-full"
                            />
                            <div v-if="errors.grade" class="text-red-500">
                                {{ errors.grade }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Parent's Email</label>
                            <input
                                type="email"
                                v-model="form.email"
                                class="py-1 w-full"
                            />
                            <div v-if="errors.email" class="text-red-500">
                                {{ errors.email }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                            >
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
