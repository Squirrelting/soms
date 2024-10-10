<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, watch } from "vue"; 
import axios from 'axios'; 

const props = defineProps({
    errors: Object,
    grades: Array,
});

const form = useForm({
    grade_id: "", 
    section: "", 
});

const saveSection = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to add this section!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Saving...",
                text: "Please wait while we save this section.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            form.post(route("section.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Section Added",
                        text: "The section has been successfully added!",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    form.reset();
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Failed",
                        text: "There was a problem saving the section. Please try again.",
                    });
                },
            });
        }
    });
};
</script>




<template>
    <Head title="Add Section" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Add Section</h5>
                <Link
                    :href="route('section.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link>
            </div>

            <form @submit.prevent="saveSection()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">

                        <div class="mb-3">
                            <label>Student's Grade</label>
                            <select v-model="form.grade_id" class="py-1 w-full">
                                <option value="">Select Grade</option>
                                <option v-for="grade in grades" :key="grade.id" :value="grade.id">{{ grade.grade }}</option>
                            </select>
                            <div v-if="errors.grade_id" class="text-red-500">{{ errors.grade_id }}</div>
                        </div>

                        <div class="mb-3">
                            <label>Section</label>
                            <input
                                type="text"
                                v-model="form.section"
                                class="py-1 w-full"
                            />
                            <div v-if="errors.section" class="text-red-500">
                                {{ errors.section }}
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
