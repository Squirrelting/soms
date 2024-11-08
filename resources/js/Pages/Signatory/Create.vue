<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineProps({
    errors: Object,
});

const form = useForm({
    name: "",
    position: "",
});

// Function to save signatory with confirmation
const saveSignatory = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to add this signatory!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, save it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: "Saving...",
                text: "Please wait while we save the new signatory.",
                didOpen: () => {
                    Swal.showLoading();
                },
            });
            form.post(route("signatory.store"), {
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: "The signatory has been saved successfully.",
                        confirmButtonText: "OK",
                    });
                    form.reset(); // Reset the form after successful submission
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Failed to save the signatory. Please try again.",
                        confirmButtonText: "OK",
                    });
                    console.error("Failed to Save signatory");
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Input Signatory" />

    <AuthenticatedLayout>
        <div class="mt-4 mx-4">
            <div class="bg-white p-4 rounded-lg shadow-lg space-y-4">

            <div class="flex justify-between">
                <h5 class="m-4">Input Signatory</h5>
                <Link
                    :href="route('signatory.index')"
                    class="bg-red-600 text-white py-2 px-5 inline-block rounded mb-4"
                    >Back</Link
                >
            </div>

            <form @submit.prevent="saveSignatory()">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12">
                        <div class="mb-3">
                            <label>Signatory</label>
                            <input
                                type="text"
                                v-model="form.name"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"


                            />
                            <div v-if="errors.name" class="text-red-500">
                                {{ errors.name }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Position</label>
                            <input
                                type="text"
                                v-model="form.position"
                                class="py-1 w-full bg-gray-200 border border-gray-500 rounded"


                            />
                            <div v-if="errors.position" class="text-red-500">
                                {{ errors.position }}
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
    </div>

    </AuthenticatedLayout>
</template>
