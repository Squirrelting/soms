<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { ref } from "vue";

const props = defineProps({
    major_offenses: Object,
    errors: Object,
});

// Form data setup
const form = useForm({
    major_offenses: props.major_offenses.major_offenses || "",  
});


const updateMajorOffense = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update this major offense's information?",
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
                text: "Please wait while we update the major offense's data.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            form.put(route("majoroffense.update", props.major_offenses.id), {
                onSuccess: () => {
                    Swal.fire(
                        "Updated!",
                        "Major offense's information has been updated.",
                        "success"
                    );
                    form.reset();
                },
                onError: (errors) => {
                    console.log(errors); // Log validation errors
                    Swal.fire(
                        "Error",
                        "Failed to update the major offense's. Please check the input.",
                        "error"
                    );
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Edit Major Offense's" />

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
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

            <div class="flex justify-between">
                <h5 class="m-4">Edit Major Offense's</h5>
                <Link
                    :href="route('majoroffense.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link
                >
            </div>

            <form @submit.prevent="updateMajorOffense()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
 
                        <div class="mb-3">
                            <label>Major Offense</label>
                            <input
                                type="text"
                                v-model="form.major_offenses"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"
                            />
                            <div v-if="errors.major_offenses" class="text-red-500">
                                {{ errors.major_offenses }}
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
        </div>
    </AuthenticatedLayout>
</template>
