<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    section: Object,
    errors: Object,
    grades: Array,
    sections: {
        type: Array,
        default: () => [],
    },
});

// Form data setup
const form = useForm({
    grade_id: props.section.grade_id || "", // Pre-fill with current grade_id
    section: props.section.section || "", // Pre-fill with current section name
});

// Grade and Section setup
const grades = ref(props.grades);
const sections = ref(props.sections);

const updateSection = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this section's information?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "Updating...",
                text: "Please wait while we update the section data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            form.put(route("section.update", props.section.id), {
                onSuccess: () => {
                    Swal.fire(
                        "Updated!",
                        "Section information has been updated.",
                        "success"
                    );
                    form.reset();
                },
                onError: (errors) => {
                    console.log(errors); // Log validation errors
                    Swal.fire(
                        "Error",
                        "Failed to update the section. Please check the input.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Edit Section" />

    <AuthenticatedLayout>
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

        <div class="mt-4 mx-4">
            <div class="flex justify-between">
                <h5 class="m-4">Edit Section</h5>
                <Link
                    :href="route('section.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link
                >
            </div>

            <form @submit.prevent="updateSection()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Grade</label>
                            <select v-model="form.grade_id" class="py-1 w-full">
                                <option value="">Select Grade</option>
                                <option
                                    v-for="grade in grades"
                                    :key="grade.id"
                                    :value="grade.id"
                                >
                                    {{ grade.grade }}
                                </option>
                            </select>
                            <div v-if="errors.grade_id" class="text-red-500">
                                {{ errors.grade_id }}
                            </div>
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

                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-500 text-white py-2 px-5 rounded mb-4"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
